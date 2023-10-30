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

        $earnings = AP_Transaction_Model::query()->select()->addFieldSum('amount', 'total')->one();
        $pending = AP_Contract_Model::where('status', 'inprogress')->addFieldSum('budget', 'total')->one();
        $transactions = AP_Transaction_Model::query('t')
            ->select(['t.id', 't.user_id', 't.description', 't.created_at as date', 't.amount', 'f.display_name as from'])
            ->leftJoin($wpdb->prefix . 'users as f', 'f.ID', '=', 't.from_user_id')
            ->where('user_id', get_current_user_id())
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return $this->view('wallet/index', compact('title', 'transactions', 'earnings', 'pending'));
    }
}
