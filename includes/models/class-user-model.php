<?php

class AP_User_Model extends AP_Base_Model
{
    protected $table = 'users';

    public static function find($id)
    {
        return get_user_by('id', $id);
    }
}