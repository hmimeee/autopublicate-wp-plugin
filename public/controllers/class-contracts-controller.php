<?php

class AP_Contracts_Controller extends AP_Base_Controller
{
    public function index()
    {
        $title = 'Contracts';
        $user_id = get_current_user_id();
        $type = request('contract', 'ongoing');

        $query = AP_Contract_Model::where('provider_id', $user_id)->orWhere('buyer_id', $user_id);

        switch ($type) {
            case 'delivered':
                $query = $query->where('status', 'delivered');
                break;

            case 'completed':
                $query = $query->whereNotIn('status', ['completed', 'cleared']);
                break;

            default:
                $query = $query->whereNotIn('status', ['completed', 'cleared']);
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

        $data = request()->only('title', 'description', 'expected_deadline', 'budget', 'budget_type');
        $data['provider_id'] = $provider->get('ID');
        $data['buyer_id'] = get_current_user_id();
        $data['attachments'] = implode(',', request()->file('attachments')->save());

        $this->wpdb->insert(
            $this->wpdb->prefix . 'ap_contracts',
            $data
        );

        return $this->redirectWith(ap_route('contracts.show', [
            'user' => $username,
            'contract' => $this->wpdb->insert_id
        ]), 'Contract created successfully');
    }

    public function show($contract)
    {
        $attachments = [];
        $user_id = get_current_user_id();
        $contract = AP_Contract_Model::with('buyer', 'provider')->where('provider_id', $user_id)->orWhere('buyer_id', $user_id)->find($contract);
        if (!$contract) {
            return ap_abort();
        }

        if($contract['buyer_id'] == get_current_user_id()) {
            $this->user = $contract['provider'];
        } else {
            $this->user = $contract['buyer'];
        }

        if ($contract['attachments']) {
            $args = array(
                'post__in' => explode(',', $contract['attachments']),
                'post_type' => 'attachment',
                'no_found_rows' => true,
                'post_status' => 'any'
            );
            $query = new WP_Query($args);
            $attachments = $query->get_posts();
        }
        $title = 'Contract: ' . $contract['title'];

        return $this->view('contracts/show', compact('contract', 'title'));
    }

    public function modify($user, $contractId)
    {
        $provider = get_user_by('login', $user);
        if (!$provider) {
            return ap_abort();
        }

        $provider_id = $provider->get('ID');
        $buyer_id = get_current_user_id();
        $contract = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}ap_contracts WHERE provider_id = $provider_id AND buyer_id = $buyer_id AND id = $contractId", ARRAY_A);
        if (!$contract) {
            return ap_abort();
        }

        $deadline = request('deadline', $contract['expected_deadline']);
        $budget = request('budget', $contract['budget']);
        $this->wpdb->get_row("UPDATE {$this->wpdb->prefix}ap_contracts SET deadline = '$deadline', budget = '$budget', modified_by = '$buyer_id', status = 'modified' WHERE provider_id = $provider_id AND buyer_id = $buyer_id AND id = $contractId");

        return $this->redirectWith(ap_route('contracts.show', ['user' => $user, 'contract' => $contractId]), 'Contract submitted as modified, please wait till other parties to approve');
    }
}
