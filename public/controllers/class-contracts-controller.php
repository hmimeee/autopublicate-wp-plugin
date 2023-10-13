<?php

class AP_Contracts_Controller extends AP_Base_Controller
{
    public function index()
    {
        $title = 'Contracts';

        return $this->view('contracts/index', compact('title'));
    }
}
