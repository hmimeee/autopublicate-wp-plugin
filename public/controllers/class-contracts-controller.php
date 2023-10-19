<?php

class AP_Contracts_Controller extends AP_Base_Controller
{
    public function index()
    {
        $title = 'Contracts';
        $user_id = get_current_user_id();
        $tab = request('tab');

        $query = AP_Contract_Model::where(fn($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id));

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

        return $this->view('contracts/index', compact('title', 'contracts'));
    }

    public function create($username)
    {
        $this->user = get_user_by('login', $username);
        if (!$this->user) {
            return ap_abort();
        }

        $title = 'New Contract with ' . $this->user->get('user_nicename');

        return $this->view('contracts/create', compact('title'));
    }

    public function store($username)
    {
        $validate = request()->validate([
            'title' => 'string|min:5',
            'description' => 'string',
            'expected_deadline' => 'date:Y-m-d'
        ]);

        if (!$validate['status']) {
            return $this->redirectWith(ap_route('user_profile.contracts.create', $username), $validate['message'], 'error');
        }

        $provider = get_user_by('login', $username);
        if (!$provider) {
            return ap_abort();
        }

        $data = request()->only('title', 'expected_deadline', 'budget', 'budget_type');
        $data['description'] = htmlentities(stripslashes(request('description')));
        $data['provider_id'] = $provider->get('ID');
        $data['buyer_id'] = get_current_user_id();
        $data['attachments'] = implode(',', request()->file('attachments')->save());
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

        $contract = AP_Contract_Model::where(fn($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id))->find($contract);
        if (!$contract) {
            return ap_abort();
        }

        if ($contract['buyer_id'] == get_current_user_id()) {
            $this->user = AP_User_Model::find($contract['provider_id']);
        } else {
            $this->user = AP_User_Model::find($contract['buyer_id']);
        }

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
        }

        $title = 'Contract: ' . $contract['title'];
        $progressSequences = [
            'cancelled' => 0,
            'pending' => 1,
            'modified' => 2,
            'approved' => 3,
            'delivered' => 4,
            'completed' => 5,
            'cleared' => 6
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
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where(fn($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id))->find($contractId);
        if (!$contract) {
            return ap_abort();
        }

        $deadline = request('deadline', $contract['expected_deadline']);
        $budget = request('budget', $contract['budget']);

        AP_Contract_Model::query()->update([
            'deadline' => $deadline,
            'budget' => $budget,
            'modified_by' => $user_id,
            'status' => 'modified'
        ])
            ->where('id', $contract['id'])
            ->execute();

        return $this->redirectWith(ap_route('contracts.show', $contractId), 'Contract submitted as modified, please wait till other parties to approve');
    }

    public function statusUpdate($contractId, $status)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where(fn($q) => $q->where('provider_id', $user_id)->orWhere('buyer_id', $user_id))->where('modified_by', '<>', $user_id)->find($contractId);
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
            'approved' => 'Good news, the contract has started',
            'cancelled' => 'The contract has been cancelled'
        ];

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$status]);
    }

    public function deliver($contractId)
    {
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
            'status' => 'delivered'
        ])
            ->where('id', $contract['id'])
            ->execute();

        return $this->redirectWith(ap_route('contracts.show', $contractId), 'Contract marked as delivered, please wait till other parties to approve it');
    }

    public function deliveryAction($contractId, $status)
    {
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::where('buyer_id', $user_id)->find($contractId);
        if (!$contract) {
            return ap_abort();
        }

        if (!in_array($status, ['approved', 'completed'])) {
            return $this->redirectWith(ap_route('contracts.show', $contractId), 'Invalid request', 'error');
        }

        AP_Contract_Model::query()->update([
            'modified_by' => $user_id,
            'status' => $status
        ])
            ->where('id', $contract['id'])
            ->execute();

        $messages = [
            'approved' => 'The contract has returned to the provider',
            'completed' => 'The contract has marked as completed'
        ];

        return $this->redirectWith(ap_route('contracts.show', $contractId), $messages[$status]);
    }
}
