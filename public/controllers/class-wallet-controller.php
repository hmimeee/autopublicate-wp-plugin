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
            ->select(['t.id', 't.user_id', 't.description', 't.type', 't.created_at as date', 't.amount', 'f.display_name as from'])
            ->join($wpdb->prefix . 'ap_contracts as c', 'c.id', '=', 't.contract_id')
            ->leftJoin($wpdb->prefix . 'users as f', 'f.ID', '=', 't.from_user_id')
            ->where('user_id', get_current_user_id())
            ->whereIn('c.status', ['completed', 'cleared'])
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return $this->view('wallet/index', compact('title', 'transactions', 'pending'));
    }
}
