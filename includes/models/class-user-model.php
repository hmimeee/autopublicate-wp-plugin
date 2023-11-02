<?php

class AP_User_Model extends AP_Base_Model
{
    public static function find($id)
    {
        return get_user_by('id', $id);
    }
}