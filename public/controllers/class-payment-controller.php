<?php

use Stripe\Event;
use Stripe\StripeClient;

class AP_Payment_Controller extends AP_Base_Controller
{    
    public function contractPayment($contractId)
    {
        $validate = request()->validate([
            'gateway' => 'required|in:stripe,paypal',
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), $validate['message'], 'error');
        }

        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('buyer_id', $user_id)->find($contractId);

        if (!$contract || $contract['status'] != 'approved' || $contract['status'] == 'inprogress') {
            return ap_abort();
        }

        $contract['budget'] = prepare_buyer_amount($contract['budget']);

        switch (request('gateway')) {
            case 'paypal':
                $data = $this->contractPaypalPayment($contract);
                break;

            default:
                $data = $this->contractStripePayment($contract);
                break;
        }

        $transaction = AP_Transaction_Model::where('contract_id', $contract['id'])->one();
        if ($transaction) {
            AP_Transaction_Model::query()->update([
                'gateway' => request('gateway'),
                'gateway_info' => $data['id'],
                'amount' => $contract['budget'],
                'updated_at' => ap_date_format()
            ])->where('contract_id', $contractId)->execute();
        } else {
            AP_Transaction_Model::query()->insert([
                'from_user_id' => $user_id,
                'user_id' => $contract['provider_id'],
                'contract_id' => $contract['id'],
                'description' => 'Contract: ' . $contract['title'],
                'amount' => $contract['budget'],
                'gateway' => request('gateway'),
                'gateway_info' => $data['id'],
                'created_at' => ap_date_format()
            ])->execute();
        }

        return $this->redirect($data['url']);
    }

    public function contractPaymentComplete($contractId)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('buyer_id', $user_id)->find($contractId);
        $transaction = AP_Transaction_Model::where('contract_id', $contractId)->one();

        if (!$contract || !$transaction) {
            return ap_abort();
        }

        switch ($transaction['gateway']) {
            case 'paypal':
                $paymentStatus = $this->contractPaypalConfirm($transaction);
                break;

            default:
                $paymentStatus = $this->contractStripeConfirm($transaction);
                break;
        }

        $contractStatuses = [
            'paid' => 'inprogress',
            'failed' => 'approved',
            'pending' => 'waiting'
        ];

        AP_Contract_Model::query()->update([
            'status' => $contractStatuses[$paymentStatus],
            'updated_at' => ap_date_format()
        ])->where('id', $contractId)->execute();

        AP_Transaction_Model::query()->update([
            'status' => $paymentStatus,
            'updated_at' => ap_date_format()
        ])->where('contract_id', $contractId)->execute();

        $messages = [
            'paid' => 'Congrats, the payment was successful. Please wait till the provider deliver the work.',
            'failed' => 'Sorry, the payment was unsuccessful. Please try again.',
            'waiting' => 'Good news, the payment is being processed. Please wait till the payment complete.',
        ];

        $provider = AP_User_Model::find($contract['provider_id']);

        ap_send_mail($provider->get('email'), 'The contract got the payment', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $provider->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => "Buyer make the payment for the contract. You can start working on it and finish it before the deadline.",
                'body_second' => 'Please be noted, completing a contract within the dealine increase good impressions to the buyers.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$paymentStatus], $paymentStatus != 'paid' ? 'error' : 'success');
    }

    public function contractPaymentWebhook($contractId, $gateway)
    {
        $contract = AP_Contract_Model::find($contractId);
        if ($contract['status'] == 'completed' || $contract['status'] == 'cleared') {
            return false;
        }

        $transaction = AP_Transaction_Model::where('contract_id', $contract['id'])->one();

        switch ($gateway) {
            case 'paypal':
                $paymentStatus = $this->contractPaypalWebhook($transaction);
                break;

            default:
                $paymentStatus = $this->contractStripeWebhook($transaction);
                break;
        }

        $contractStatuses = [
            'paid' => 'completed',
            'failed' => 'approved',
            'pending' => 'waiting'
        ];

        AP_Contract_Model::query()->update([
            'status' => $contractStatuses[$paymentStatus],
            'updated_at' => ap_date_format()
        ])->where('id', $contractId)->execute();

        AP_Transaction_Model::query()->update([
            'status' => $paymentStatus,
            'updated_at' => ap_date_format()
        ])->where('contract_id', $contractId)->execute();
    }

    private function contractStripePayment($contract)
    {
        $setting = maybe_unserialize(get_option('ap_payment_settings'));
        $stripe = new StripeClient($setting['stripe_client_secret'] ?? '');

        $price = $stripe->prices->create([
            'currency' => 'eur',
            'unit_amount' => $contract['budget'] * 100,
            'product_data' => [
                'name' => 'Contract: ' . $contract['title'],
                'metadata' => [
                    'id' => $contract['id'],
                    'buyer_id' => $contract['buyer_id'],
                    'provider_id' => $contract['provider_id'],
                    'budget' => $contract['budget'],
                    'deadline' => $contract['deadline']
                ]
            ]
        ]);

        $session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price' => $price['id'],
                'quantity' => 1
            ]],
            'success_url' => ap_route('contracts.payment.complete', $contract['id']),
            'mode' => 'payment'
        ]);

        return [
            'id' => $session->id,
            'url' => $session->url
        ];
    }

    private function contractPaypalPayment($contract)
    {
        $paypal = new AP_PayPal_Service();
        $checkout = $paypal->checkout(number_format($contract['budget'], 2, '.', ''), ap_route('contracts.payment.complete', $contract['id']), ap_route('contracts.show', $contract['id']));
        $link = reset(array_filter($checkout['links'], function ($dt) {
            return $dt['rel'] == 'payer-action';
        }));

        return [
            'id' => $checkout['id'],
            'url' => $link['href']
        ];
    }

    private function contractStripeConfirm($transaction)
    {
        $setting = maybe_unserialize(get_option('ap_payment_settings'));
        $stripe = new StripeClient($setting['stripe_client_secret'] ?? '');
        $session = $stripe->checkout->sessions->retrieve($transaction['gateway_info']);

        return $session->payment_status == 'paid' ? 'paid' : 'failed';
    }

    private function contractPaypalConfirm($transaction)
    {
        $paypal = new AP_PayPal_Service();
        $order = $paypal->details($transaction['gateway_info']);

        return $order['status'] == 'APPROVED' ? 'paid' : 'failed';
    }

    private function contractStripeWebhook($transaction)
    {
        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            //
        }

        // Handle the event
        if ($event->type == 'checkout.session.completed') {
            $checkout = $event->data->object;
            return $checkout->payment_status;
        }

        return false;
    }

    private function contractPaypalWebhook($transaction)
    {
        //
    }
}
