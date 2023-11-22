<?php

class AP_Base_Controller
{
    public $layout = 'main';

    public $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function view($page, $params = [])
    {
        $params['_page'] = trim($page, '/') . '.php';
        ap_file_loader('admin/views/layouts/' . $this->layout . '.php', $params);
    }

    public function render($page, $params = [])
    {
        ap_file_loader(plugin_dir_path(__DIR__) . 'views/' . $page . '.php', $params, true);
        exit;
    }

    public function response($data, $status_code = 200)
    {
        wp_send_json($data, $status_code);
        exit;
    }

    public function redirect($uri = '/wp-admin/admin.php?page=ap_dashboard')
    {
?>

        <!DOCTYPE html>
        <html>

        <head>
            <meta http-equiv="refresh" content="0; url='<?= $uri ?>'" />
        </head>

        </html>

<?php
    }

    public function redirectWith($uri = '/wp-admin/admin.php?page=ap_dashboard', $message = 'Request processed successfully', $status = 'success')
    {
        $_SESSION['_request'] = request()->all();
        ap_alert(__($message), $status);

        $this->redirect($uri);
    }
}
