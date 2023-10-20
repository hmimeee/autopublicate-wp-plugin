<?php

class AP_Base_Controller
{
    public $layout;

    public $user;

    public $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

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

                $params['_layout'] = $this->layout;
                $params['user'] = $this->user;
                $params['_page'] = $file;
                $params['_current_url'] = home_url($wp->request);
                $params['_current_user'] = get_user_by('ID', get_current_user_id());

                ap_file_loader('public/views/layouts/main.php', $params);
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
        $redirect = add_query_arg( $status . '_message', __($message), $uri );

        $this->redirect($redirect);
    }
}
