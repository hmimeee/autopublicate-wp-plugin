<?php

class AP_Settings_Controller extends AP_Base_Controller
{
    public function __construct()
    {
        $this->layout = 'settings';
    }

    public function index()
    {
        if (request('tab') == 'payment') {
            $settings = ['ap_payment_settings' => maybe_unserialize(get_option('ap_payment_settings'))];
            return $this->view('settings/payment', compact('settings'));
        } else {
            $settings = ['ap_settings' => maybe_unserialize(get_option('ap_settings'))];

            return $this->view('settings/general', compact('settings'));
        }
    }

    public function update()
    {
        $data = request()->only([
            'ap_settings',
            'ap_payment_settings'
        ]);

        foreach ($data as $key => $value)
            update_option($key, serialize($value), false);

        return $this->redirectWith(ap_admin_route('settings', ['tab' => request('tab')]));
    }
}
