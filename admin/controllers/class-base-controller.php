<?php

class AP_Base_Controller
{
    public function view($page, $params = [])
    {
        $params['_page'] = trim($page, '/') . '.php';
        ap_file_loader('admin/views/layout.php', $params);
    }

    public function response($data, $status_code = 200)
    {
        wp_send_json($data, $status_code);
        exit;
    }
}
