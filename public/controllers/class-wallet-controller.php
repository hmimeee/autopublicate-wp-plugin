<?php

use ClanCats\Hydrahon\Query\Sql\Func;
use Stripe\StripeClient;

class AP_Wallet_Controller extends AP_Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        global $wpdb;
        $title = 'Wallet';

        $user_id = get_current_user_id();
        $pending = AP_Contract_Model::query()
            ->select()
            ->whereIn('status', ['inprogress', 'delivered'])
            ->where('provider_id', $user_id)
            ->addFieldSum('budget', 'total')
            ->one();

        $transactions = AP_Transaction_Model::query('t')
            ->select(['t.id', 't.user_id', 't.description', 't.type', 't.created_at as date', 't.amount', 't.contract_id', 'f.user_login', 'f.display_name as from'])
            ->leftJoin($wpdb->prefix . 'ap_contracts as c', 'c.id', '=', 't.contract_id')
            ->leftJoin($wpdb->prefix . 'users as f', 'f.ID', '=', 't.from_user_id')
            ->where('user_id', $user_id)
            ->whereIn('c.status', ['completed', 'cleared', 'refunded'])
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        $payoutRequests = AP_Payout_Request_Model::query()
            ->select()
            ->where('user_id', $user_id)
            ->limit(5)
            ->orderBy('id', 'desc')
            ->get();

        return $this->view('wallet/index', compact('title', 'transactions', 'pending', 'payoutRequests'));
    }

    public function payout()
    {
        $user_id = get_current_user_id();
        $payoutExists = AP_Payout_Request_Model::query()
            ->select()
            ->where('status', 'pending')
            ->where('user_id', $user_id)
            ->exists();

        if ($payoutExists) {
            return $this->redirectWith(ap_route('wallet.index'), 'One of your withdraw request is pending, please wait till that gets cleared', 'error');
        }

        $user = AP_User_Model::find($user_id);
        if ($user->get('balance') < 10) {
            return $this->redirectWith(ap_route('wallet.index'), 'Not enough amount to withdraw');
        }

        $title = 'Payout';

        return $this->view('wallet/payout', compact('title'));
    }

    public function payoutRequest()
    {
        $validate = request()->validate([
            'method' => 'required|in:paypal,bank'
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('wallet.index'), $validate['message'], 'error');
        }

        $user = AP_User_Model::find(get_current_user_id());

        if ($user->get('balance') < 10) {
            return $this->redirectWith(ap_route('wallet.index'), 'Not enough amount to withdraw');
        }

        $data = [
            'user_id' => $user->get('ID'),
            'amount' => $user->get('balance'),
            'gateway' => request('method', 'bank')
        ];

        if (request('method') == 'bank') {
            $data['gateway_info'] = json_encode([[
                'name' => request('bank_name'),
                'type' => request('bank_type'),
                'holder' => request('bank_holder'),
                'routing' => request('bank_routing'),
                'account' => request('bank_account')
            ]]);
        }

        if (request('method') == 'paypal') {
            $data['gateway_info'] = json_encode([
                'name' => request('paypal_holder'),
                'email' => request('paypal_email')
            ]);
        }

        $payoutId = AP_Payout_Request_Model::query()->insert($data)->execute();

        $adminEmail = get_option('admin_email');
        $user = AP_User_Model::find($user->get('ID'));

        ap_send_mail($adminEmail, $user->get('user_nicename') . ' has submitted a payout request', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => 'Admin',
                'action' => 'See Details',
                'action_url' => ap_admin_route('payout_requests', ['payout' => $payoutId]),
                'body_first' => 'A request to payout has been submitted by ' . $user->get('user_nicename') . '. Please have look on the detail page and make an action regarding this.',
                'body_second' => 'Please verify the gateway information before sending the amount.'
            ]
        ]);

        return $this->redirectWith(ap_route('wallet.index'), 'Request submitted successfully');
    }
}
