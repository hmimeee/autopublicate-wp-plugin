<?php

class AP_Payout_Controller extends AP_Base_Controller
{
    public function index()
    {
        $query = AP_Payout_Request_Model::query('p')->select([
            'p.id',
            'p.user_id',
            'p.amount',
            'p.gateway',
            'p.status',
            'p.created_at',
            'u.display_name as user'
        ])
            ->join($this->wpdb->prefix . 'users as u', 'p.user_id', '=', 'u.ID')
            ->orderBy('p.id', 'desc');

        if (request('search')) {
            $query->where(function ($q) {
                return $q->where('u.display_name', 'like', '%' . request('search') . '%')
                    ->orWhere('u.user_login', 'like', '%' . request('search') . '%');
            });
        }

        if (request('status')) {
            $query->where('p.status', request('status'));
        }

        $payouts = paginate($query, request('page_number', 1));

        return $this->view('payouts/index', compact('payouts'));
    }

    public function show()
    {
        $payout = AP_Payout_Request_Model::query('p')->select([
            'p.id',
            'p.user_id',
            'p.amount',
            'p.gateway',
            'p.gateway_info',
            'p.status',
            'p.created_at',
            'p.updated_at',
            'p.notes',
            'u.display_name as user'
        ])
            ->join($this->wpdb->prefix . 'users as u', 'p.user_id', '=', 'u.ID')
            ->where('p.id', request('payout'))
            ->one();

        $payout['gateway_info'] = json_decode($payout['gateway_info'], true);

        return $this->render('payouts/show', compact('payout'));
    }

    public function update()
    {
        $payout = AP_Payout_Request_Model::find(request('payout'));

        if (in_array($payout['status'], ['sent', 'cancelled'])) {
            return $this->redirectWith(request()->server('HTTP_REFERER'), 'Invalid request', 'error');
        }

        $user = AP_User_Model::find($payout['user_id']);

        AP_Payout_Request_Model::query()
            ->update([
                'status' => request('status'),
                'notes' => stripslashes(request('notes')),
                'updated_at' => ap_date_format(),
            ])->where('id', request('payout'))
            ->execute();

        if (request('status') == 'sent') {
            AP_User_Model::query()->update([
                'balance' => $user->get('balance') - $payout['amount']
            ])->where('ID', $user->get('ID'))->execute();
        }

        return $this->redirectWith(request()->server('HTTP_REFERER'));
    }
}
