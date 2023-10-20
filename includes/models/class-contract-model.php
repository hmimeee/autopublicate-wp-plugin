<?php

class AP_Contract_Model extends AP_Base_Model
{
    public function provider()
    {
        return $this->relation('belongs_to', 'users', 'ID', 'provider_id');
    }

    public function buyer()
    {
        return $this->relation('belongs_to', 'users', 'ID', 'buyer_id');
    }

    public static function completedContracts($id)
    {
        return AP_Contract_Model::where(fn ($q) => $q->where('provider_id', $id)->orWhere('buyer_id', $id))->whereIn('status', ['completed', 'cleared'])->count();
    }
}