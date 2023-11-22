<?php

class AP_Contract_Controller extends AP_Base_Controller
{
    public function index()
    {
        $query = AP_Contract_Model::query('c')->select([
            'c.id',
            'c.budget',
            'c.deadline',
            'c.expected_deadline',
            'c.status',
            'c.title',
            'c.provider_id',
            'c.buyer_id',
            'p.display_name as provider',
            'b.display_name as buyer',
        ])
            ->join($this->wpdb->prefix . 'users as p', 'c.provider_id', '=', 'p.ID')
            ->join($this->wpdb->prefix . 'users as b', 'c.buyer_id', '=', 'b.ID')
            ->orderBy('c.id', 'desc');

        if (request('search')) {
            $query->where(function ($q) {
                return $q->where('c.title', 'like', '%' . request('search') . '%')
                    ->orWhere('p.display_name', 'like', '%' . request('search') . '%')
                    ->orWhere('b.display_name', 'like', '%' . request('search') . '%');
            });
        }

        if (request('status')) {
            $query->where('c.status', request('status'));
        }

        $contracts = paginate($query, request('page_number', 1));

        return $this->view('contracts/index', compact('contracts'));
    }

    public function show()
    {
        $contract = AP_Contract_Model::find(request('contract'));
        $provider = AP_User_Model::find($contract['provider_id']);
        $buyer = AP_User_Model::find($contract['buyer_id']);

        $comments = AP_Contract_Comment_Model::where('contract_id', $contract['id'])->get() ?? [];
        $comments = array_map(function ($dt) use ($contract, $provider, $buyer) {
            if ($provider->get('ID') == $contract['provider_id']) {
                $dt['user'] = $provider;
                $dt['user']->type = 'Provider';
            } else {
                $dt['user'] = $buyer;
                $dt['user']->type = 'Buyer';
            }
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
        }

        $resolution = AP_Contract_Resolution_Request_Model::query()
            ->select()
            ->where('contract_id', $contract['id'])
            ->where('status', 'pending')
            ->one();

        $back = [
            'label' => 'Back to contracts',
            'url' => ap_admin_route('contracts')
        ];

        return $this->view('contracts/show', compact('contract', 'provider', 'buyer', 'comments', 'resolution', 'back'));
    }

    public function resolution()
    {
        $resolution = AP_Contract_Resolution_Request_Model::find(request('resolution'));

        AP_Contract_Resolution_Request_Model::query()
            ->update([
                'status' => request('status') == 'Accept' ? 'approved' : 'declined',
                'action_taken_by' => get_current_user_id(),
                'updated_at' => now(true)->format('Y-m-d H:i:s')
            ])
            ->where('id', $resolution['id'])
            ->execute();

        $contract = AP_Contract_Model::find($resolution['contract_id']);

        if (request('status') == 'Accept') {
            AP_Contract_Model::query()
                ->update([
                    'status' => 'refunded'
                ])
                ->where('id', $resolution['contract_id'])
                ->execute();

            $buyerAmount = $contract['budget'] * request('amount', 100) / 100;
            $providerAmount = prepare_provider_amount($contract['budget'] - $buyerAmount);

            //Update the provider amount after the resolution
            AP_Transaction_Model::query()->insert([
                'from_user_id' => $contract['buyer_id'],
                'user_id' => $contract['provider_id'],
                'contract_id' => $contract['id'],
                'description' => 'Partial Contract: ' . $contract['title'],
                'amount' => $providerAmount,
                'gateway' => 'wallet',
                'gateway_info' => $resolution['id'],
                'created_at' => ap_date_format(),
                'updated_at' => ap_date_format(),
                'status' => 'paid'
            ])->execute();

            $provider = AP_User_Model::find($contract['provider_id']);
            AP_User_Model::query()->update([
                'balance' => $provider->get('balance') + $providerAmount
            ])->where('ID', $contract['provider_id'])->execute();

            //Update the buyer amount after the resolution
            AP_Transaction_Model::query()->insert([
                'from_user_id' => $contract['provider_id'],
                'user_id' => $contract['buyer_id'],
                'contract_id' => $contract['id'],
                'description' => 'Refunded Contract: ' . $contract['title'],
                'amount' => $buyerAmount,
                'gateway' => 'wallet',
                'gateway_info' => $resolution['id'],
                'created_at' => ap_date_format(),
                'updated_at' => ap_date_format(),
                'status' => 'paid'
            ])->execute();

            $buyer = AP_User_Model::find($contract['buyer_id']);
            AP_User_Model::query()->update([
                'balance' => $buyer->get('balance') + $buyerAmount
            ])->where('ID', $contract['buyer_id'])->execute();
        }

        return $this->redirectWith(request()->server('HTTP_REFERER'));
    }
}
