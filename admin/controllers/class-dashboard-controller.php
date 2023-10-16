<?php

class AP_Dashboard_Controller extends AP_Base_Controller
{
    public function index()
    {
        return $this->view('dashboard');
    }
}