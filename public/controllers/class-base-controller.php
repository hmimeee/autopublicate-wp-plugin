<?php

class AP_Base_Controller
{
    public $layout = 'account';

    public function view($file, $params = [])
    {
        global $wp;
        status_header(200);

        add_filter('document_title_parts', function ($parts) use ($file, $params) {
            $parts['title'] = __($params['title'] ?? ucfirst(basename($file)));
            return $parts;
        }, 10000);

        add_action('elementor/theme/before_do_single', function ($location) use ($file, $params, $wp) {
            try {
                //Set the current route as a varibale for the view
                global $current_route;
                $params['current_route'] = $current_route;

                //Set the current user data in a variable for the view
                global $current_user;
                wp_get_current_user();
                $params['current_user'] = $current_user;
                $params['_page'] = $file;
                $params['_current_url'] = home_url($wp->request);

                ap_file_loader('public/views/layouts/' . $this->layout . '.php', $params);
            } catch (Throwable $th) {
                if ($th->getCode() == 404) {
                    echo 'View not found';
                    exit;
                }
            }

            get_footer();
            exit;
        });
    }

    public function response($data, $status_code = 200)
    {
        wp_send_json($data, $status_code);
        exit;
    }

    public function redirect($uri = '/', $status_code = 302)
    {
        wp_redirect($uri, $status_code);
        exit;
    }

    public function redirectWith($uri = '/', $message = 'Request processed successfully', $status = 'success')
    {
        session_start();
        $_SESSION[$status . '_message'] = __($message);

        $this->redirect($uri);
    }
}
