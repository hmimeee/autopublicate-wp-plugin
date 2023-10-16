<?php

class AP_Contracts_Controller extends AP_Base_Controller
{
    public function index()
    {
        $title = 'Contracts';

        return $this->view('contracts/index', compact('title'));
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

        $this->wpdb->insert(
            $this->wpdb->prefix . 'ap_contracts',
            $data
        );

        return $this->redirectWith(ap_route('contracts.show', [
            'user' => $username,
            'contract' => $this->wpdb->insert_id
        ]), 'Contract created successfully');
    }

    public function show($user, $contract)
    {
        $provider = get_user_by('login', $user);
        if (!$provider) {
            return ap_abort();
        }

        $provider_id = $provider->get('ID');
        $buyer_id = get_current_user_id();
        
        $contract = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}ap_contracts WHERE provider_id = $provider_id AND buyer_id = $buyer_id AND id = $contract", ARRAY_A);
        if (!$contract) {
            return ap_abort();
        }

        $this->user = $provider;

        return $this->view('contracts/show', compact('contract'));
    }
}
