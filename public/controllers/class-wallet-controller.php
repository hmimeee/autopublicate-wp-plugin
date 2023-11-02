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

        $pending = AP_Contract_Model::query()
            ->select()
            ->whereIn('status', ['inprogress', 'delivered'])
            ->where('provider_id', get_current_user_id())
            ->addFieldSum('budget', 'total')
            ->one();

        $transactions = AP_Transaction_Model::query('t')
            ->select(['t.id', 't.user_id', 't.description', 't.type', 't.created_at as date', 't.amount', 't.contract_id', 'f.user_login', 'f.display_name as from'])
            ->join($wpdb->prefix . 'ap_contracts as c', 'c.id', '=', 't.contract_id')
            ->leftJoin($wpdb->prefix . 'users as f', 'f.ID', '=', 't.from_user_id')
            ->where('user_id', get_current_user_id())
            ->whereIn('c.status', ['completed', 'cleared'])
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return $this->view('wallet/index', compact('title', 'transactions', 'pending'));
    }

    public function payout()
    {
        $user = AP_User_Model::find(get_current_user_id());
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
            $data['gateway_info'] = json_encode([[
                'name' => request('paypal_holder'),
                'email' => request('paypal_email')
            ]]);
        }

        AP_Payout_Request_Model::query()->insert($data)->execute();
        
        AP_User_Model::query()->update([
            'balance' => 0
        ])->where('ID', $user->get('ID'))->execute();

        return $this->redirectWith(ap_route('wallet.index'), 'Request submitted successfully');
    }
}
