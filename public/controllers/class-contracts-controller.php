<?php

use Stripe\StripeClient;

class AP_Contracts_Controller extends AP_Base_Controller
{
    public function index()
    {
        $title = 'Contracts';
        $user_id = get_current_user_id();
        $tab = request('tab');

        $query = AP_Contract_Model::where(fn ($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id));

        switch ($tab) {
            case 'delivered':
                $query = $query->where('status', 'delivered');
                break;

            case 'completed':
                $query = $query->whereIn('status', ['completed', 'cleared']);
                break;

            default:
                $query = $query->whereNotIn('status', ['completed', 'cleared', 'delivered']);
                break;
        }

        $contracts = paginate($query, request('page', 1));

        $pendingCount = AP_Contract_Model::where(
            fn ($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id)
        )->where('status', 'pending')
            ->count();

        $deliveredCount = AP_Contract_Model::where(
            fn ($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id)
        )->where('status', 'delivered')
            ->count();

        return $this->view('contracts/index', compact('title', 'contracts', 'pendingCount', 'deliveredCount'));
    }

    public function create($username)
    {
        $this->user = get_user_by('login', $username);
        if (!$this->user) {
            return ap_abort();
        }
        $this->user->completed_count = AP_Contract_Model::completedContracts($this->user->get('ID'));

        $title = 'New Contract with ' . $this->user->get('user_nicename');

        return $this->view('contracts/create', compact('title', 'username'));
    }

    public function store($username)
    {
        $validate = request()->validate([
            'title' => 'string|min:5',
            'description' => 'nullable|string',
            'expected_deadline' => 'required|date:Y-m-d'
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('contracts.create', $username), $validate['message'], 'error');
        }

        $provider = get_user_by('login', $username);
        if (!$provider) {
            return ap_abort();
        }

        $data = request()->only('title', 'expected_deadline');
        $data['description'] = htmlentities(stripslashes(request('description')));
        $data['provider_id'] = $provider->get('ID');
        $data['buyer_id'] = get_current_user_id();
        $data['attachments'] = implode(',', request()->file('attachments')->save());
        $data['updated_at'] = now(true)->format('Y-m-d H:i:s');
        $data['created_at'] = now(true)->format('Y-m-d H:i:s');

        $contractId = AP_Contract_Model::query()->insert(array_filter($data))->execute();

        $user = get_user_by('ID', get_current_user_id());

        ap_send_mail($provider->get('email'), 'A contract has been created for you', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $provider->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => "New contract created, please take a look on the details page to accept the contract.",
                'body_second' => 'The contract was created by ' . $user->get('user_nicename') . ', buyer is waiting for your response regarding the contract.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), 'Contract created successfully');
    }

    public function show($contract)
    {
        $attachments = [];
        $user_id = get_current_user_id();

        $contract = AP_Contract_Model::where(fn ($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id))->find($contract);
        if (!$contract) {
            return ap_abort();
        }

        if ($contract['buyer_id'] == get_current_user_id()) {
            $contract['buyer'] = AP_User_Model::find($contract['buyer_id']);
            $this->user = AP_User_Model::find($contract['provider_id']);
            $contract['provider'] = $this->user;
        } else {
            $contract['provider'] = AP_User_Model::find($contract['provider_id']);
            $this->user = AP_User_Model::find($contract['buyer_id']);
            $contract['buyer'] = $this->user;
        }

        $this->user->completed_count = AP_Contract_Model::completedContracts($this->user->get('ID'));
        $comments = AP_Contract_Comment_Model::where('contract_id', $contract['id'])->get() ?? [];
        $contract['comments'] = array_map(function ($dt) use ($contract) {
            $dt['user'] = $dt['user_id'] == $contract['provider_id'] ? $contract['provider'] : $contract['buyer'];
            return $dt;
        }, $comments);

        if ($contract['attachments']) {
            $args = array(
                'post__in' => explode(',', $contract['attachments']),
                'post_type' => 'attachment',
                'no_found_rows' => true,
                'post_status' => 'any'
            );
            $query = new WP_Query($args);
            $contract['attachments'] = $query->get_posts();
        }

        if ($contract['delivery_attachments']) {
            $args = array(
                'post__in' => explode(',', $contract['delivery_attachments']),
                'post_type' => 'attachment',
                'no_found_rows' => true,
                'post_status' => 'any'
            );
            $query = new WP_Query($args);
            $contract['delivery_attachments'] = $query->get_posts();
        } else {
            $contract['delivery_attachments'] = [];
        }

        $title = 'Contract: ' . $contract['title'];
        $progressSequences = [
            'cancelled' => 0,
            'pending' => 1,
            'modified' => 2,
            'approved' => 3,
            'inprogress' => 4,
            'delivered' => 5,
            'completed' => 6,
            'cleared' => 7
        ];

        $statusStyles = [
            'pending' => 'warning',
            'modified' => 'info',
            'approved' => 'primary',
            'delivered' => 'info',
            'completed' => 'success',
            'cleared' => 'dark',
            'cancelled' => 'danger'
        ];

        $pendingUnder = null;
        if (in_array($contract['status'], ['pending', 'modified'])) {
            $pendingUnder = AP_User_Model::find(
                $contract['status'] == 'pending' ?
                    $contract['provider_id'] : ($contract['modified_by'] == $contract['provider_id'] ?
                        $contract['buyer_id'] : $contract['provider_id']
                    )
            );
        }

        return $this->view('contracts/show', compact('contract', 'title', 'progressSequences', 'pendingUnder', 'statusStyles'));
    }

    public function modify($contractId)
    {
        $validate = request()->validate([
            'deadline' => 'required|date:Y-m-d',
            'budget' => 'required|numeric|min:1',
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), $validate['message'], 'error');
        }

        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where(fn ($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id))->find($contractId);
        if (!$contract) {
            return ap_abort();
        }

        $deadline = request('deadline', $contract['expected_deadline']);
        $budget = request('budget', $contract['budget']);

        AP_Contract_Model::query()->update([
            'deadline' => $deadline,
            'budget' => $budget,
            'modified_by' => $user_id,
            'status' => 'modified',
            'updated_at' => now(true)->format('Y-m-d H:i:s')
        ])
            ->where('id', $contract['id'])
            ->execute();

        $user = AP_User_Model::find($user_id);
        $notifiable = AP_User_Model::find($user_id != $contract['provider_id'] ? $contract['provider_id'] : $contract['buyer_id']);

        ap_send_mail($notifiable->get('email'), $user->get('user_nicename') . ' has modified a contract', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $notifiable->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => "A contract has been modified, please take a look on the details page to accept or modify again.",
                'body_second' => 'The contract was modified by ' . $user->get('user_nicename') . ', the user is waiting for your response regarding the contract.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), 'Contract submitted as modified, please wait till other parties to approve');
    }

    public function payment($contractId)
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

        switch (request('gateway')) {
            case 'paypal':
                $data = $this->paypalPayment($contract);
                break;

            default:
                $data = $this->stripePayment($contract);
                break;
        }

        $transaction = AP_Transaction_Model::where('contract_id', $contract['id'])->one();
        if ($transaction) {
            AP_Transaction_Model::query()->update([
                'gateway' => request('gateway'),
                'gateway_id' => $data['id'],
                'amount' => $contract['budget'],
                'updated_at' => ap_date_format()
            ])->where('contract_id', $contractId)->execute();;
        } else {
            AP_Transaction_Model::query()->insert([
                'from_user_id' => $user_id,
                'user_id' => $contract['provider_id'],
                'contract_id' => $contract['id'],
                'description' => $contract['title'],
                'amount' => $contract['budget'],
                'gateway' => request('gateway'),
                'gateway_id' => $data['id'],
                'created_at' => ap_date_format()
            ])->execute();
        }

        return $this->redirect($data['url']);
    }

    public function stripePayment($contract)
    {
        $stripe = new StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');

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

    public function paypalPayment($contract)
    {
        $paypal = new AP_PayPal_Service();
        $paypal->checkout($contract['budget'], ap_route('contracts.payment.complete', $contract['id']), ap_route('contracts.show', $contract['id']));

        $price = $stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $contract['budget'],
            'product_data' => [
                'name' => 'Test'
            ]
        ]);

        $payment = $stripe->paymentLinks->create([
            'line_items' => [[
                'price' => $price['id'],
                'quantity' => 1
            ]]
        ]);

        return $this->redirect($payment['url']);
    }

    public function paymentComplete($contractId)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('buyer_id', $user_id)->find($contractId);
        $transaction = AP_Transaction_Model::where('contract_id', $contractId)->one();

        if (!$contract || !$transaction) {
            return ap_abort();
        }

        switch ($transaction['gateway']) {
            case 'paypal':
                $paymentStatus = $this->paypalConfirm($transaction);
                break;

            default:
                $paymentStatus = $this->stripeConfirm($transaction);
                break;
        }

        AP_Contract_Model::query()->update([
            'status' => $paymentStatus == 'paid' ? 'inprogress' : 'approved',
            'updated_at' => ap_date_format()
        ])->where('id', $contractId)->execute();

        AP_Transaction_Model::query()->update([
            'status' => $paymentStatus,
            'updated_at' => ap_date_format()
        ])->where('contract_id', $contractId)->execute();;

        $messages = [
            'paid' => 'Congrats, the payment was successful. Please wait till the provider deliver the work.',
            'failed' => 'Sorry, the payment was unsuccessful. Please try again.'
        ];

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$paymentStatus], $paymentStatus != 'paid' ? 'error' : 'success');
    }

    public function stripeConfirm($transaction)
    {
        $stripe = new StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $session = $stripe->checkout->sessions->retrieve($transaction['gateway_id']);

        return $session->payment_status == 'paid' ? 'paid' : 'failed';
    }

    public function statusUpdate($contractId, $status)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where(
            fn ($q) =>
            $q->where('provider_id', $user_id)
                ->orWhere('buyer_id', $user_id)
        )->where(
            fn ($q) =>
            $q->whereNull('modified_by')
                ->orWhere('modified_by', '!=', $user_id)
        )->find($contractId);

        if (!$contract) {
            return ap_abort();
        }

        if (!in_array($status, ['approved', 'cancelled'])) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), 'Invalid request', 'error');
        }

        AP_Contract_Model::query()->update([
            'modified_by' => $user_id,
            'status' => $status
        ])
            ->where('id', $contract['id'])
            ->execute();

        $messages = [
            'approved' => 'Good news, the contract has approved',
            'cancelled' => 'The contract has been cancelled'
        ];

        $provider = AP_User_Model::find($contract['provider_id']);
        $buyer = AP_User_Model::find($contract['buyer_id']);

        ap_send_mail($provider->get('email'), 'The contract has approved', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $provider->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => $messages[$status] . '. Please wait till buyer make the payment for the contract. We\'ll notify you as soon as we got the payment.',
                'body_second' => 'Please don\'t start working before the payment.'
            ]
        ]);

        ap_send_mail($buyer->get('email'), 'The contract has approved', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $buyer->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => $messages[$status] . '. Please make the payment to start the contract.',
                'body_second' => 'The provider will not start until you make the payment.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$status]);
    }

    public function deliver($contractId)
    {
        $validate = request()->validate([
            'delivery_notes' => 'required|string|min:5',
            'attachments' => 'nullable|file|max:10240',
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), $validate['message'], 'error');
        }

        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('provider_id', $user_id)->find($contractId);
        if (!$contract) {
            return ap_abort();
        }

        $delivery_notes = htmlentities(stripslashes(request('delivery_notes')));
        $attachments = implode(',', request()->file('attachments')->save());

        AP_Contract_Model::query()->update([
            'delivery_notes' => $delivery_notes,
            'delivery_attachments' => $attachments,
            'status' => 'delivered',
            'delivered_at' => now(true)->format('Y-m-d H:i:s'),
            'updated_at' => now(true)->format('Y-m-d H:i:s')
        ])
            ->where('id', $contract['id'])
            ->execute();

        $user = AP_User_Model::find($user_id);
        $notifiable = AP_User_Model::find($user_id != $contract['provider_id'] ? $contract['provider_id'] : $contract['buyer_id']);

        ap_send_mail($notifiable->get('email'), $user->get('user_nicename') . ' has delivered a contract', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $notifiable->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => 'Good news, a cantract has been delivered. Please click the see details button to know more and find out the delivery.',
                'body_second' => 'However, you\'re able to return the delivery if it\'s not satisfiable.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), 'Contract marked as delivered, please wait till other parties to approve it');
    }

    public function deliveryAction($contractId, $status)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('buyer_id', $user_id)->find($contractId);
        if (!$contract || ($status == 'completed' && !isset($_POST['rating']))) {
            return ap_abort();
        }

        if (!in_array($status, ['approved', 'completed'])) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), 'Invalid request', 'error');
        }

        AP_Contract_Model::query()->update([
            'status' => $status,
            'rating' => request('rating'),
            'review' => request('review'),
            'updated_at' => now(true)->format('Y-m-d H:i:s'),
            'completed_at' => $status == 'completed' ? now(true)->format('Y-m-d H:i:s') : null
        ])
            ->where('id', $contract['id'])
            ->execute();

        if ($status == 'completed') {
            AP_User_Model::query()->update([
                'balance' => $contract['budget']
            ])->where('ID', $contract['provider_id'])->execute();
        }

        $messages = [
            'approved' => 'The contract has returned to the provider',
            'completed' => 'The contract has marked as completed'
        ];

        $user = AP_User_Model::find($user_id);
        $notifiable = AP_User_Model::find($user_id != $contract['provider_id'] ? $contract['provider_id'] : $contract['buyer_id']);

        ap_send_mail($notifiable->get('email'), $user->get('user_nicename') . ' has ' . ($status == 'approved' ? 'returned' : 'accepted') . ' the delivery', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $notifiable->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => $status == 'approved' ? $messages[$status] . ' by the buyer. please take a look on the comment to modify as expected.' : $user->get('user_nicename') . ' accepted the delivery and marked the contract as completed',
                'body_second' => 'Keep up the good work and get more contracts.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$status]);
    }

    public function comment($contractId)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where(
            fn ($q) =>
            $q->where('provider_id', $user_id)
                ->orWhere('buyer_id', $user_id)
        )->whereNotIn('status', ['completed', 'cleared'])
            ->find($contractId);

        if (!$contract) {
            return ap_abort();
        }

        $data = [
            'comment' => htmlentities(stripslashes(request('comment'))),
            'contract_id' => $contract['id'],
            'user_id' => get_current_user_id(),
            'created_at' => now(true)->format('Y-m-d H:i:s')
        ];
        AP_Contract_Comment_Model::query()->insert(array_filter($data))->execute();

        $user = AP_User_Model::find($user_id);
        $notifiable = AP_User_Model::find($user_id != $contract['provider_id'] ? $contract['provider_id'] : $contract['buyer_id']);

        ap_send_mail($notifiable->get('email'), $user->get('user_nicename') . ' has added a comment', [
            'path' => 'public/views/mails/common',
            'params' => [
                'name' => $notifiable->get('user_nicename'),
                'action' => 'See Details',
                'action_url' => ap_route('contracts.show', $contractId),
                'body_first' => 'A comment has been posted by ' . $user->get('user_nicename') . '. Please have look on the detail page and post a reply',
                'body_second' => 'Also you can take actions based on the comment.'
            ]
        ]);

        return $this->redirectWith(ap_route('contracts.show', $contract['id']), 'Comment posted successfully');
    }
}
