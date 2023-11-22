<?php

use ClanCats\Hydrahon\Query\Sql\Select;

if (!function_exists('ap_loader')) {
    /**
     * Autoload all the files inside a path that is provided in the params
     * 
     * @param string $path
     * @return void
     */
    function ap_loader($path)
    {
        //Append the plugin dir
        $baseDir = plugin_dir_path(__FILE__);
        $path = $baseDir . $path;

        $classes = [];
        foreach (glob("$path/*") as $filename) {
            if (preg_match('/\.php$/', $filename)) {
                require_once $filename;

                //Filter out all the classes
                if (str_contains($filename, '.class.')) {
                    $classes[] = str_replace(' ', '_', ucwords(str_replace(['.class.php', '-'], ['', ' '], basename($filename))));
                }
            } elseif (is_dir($filename)) {
                ap_loader(str_replace($baseDir, '', $filename));
            }
        }
    }
}

if (!function_exists('ap_file_loader')) {

    /**
     * Autoload the file inside a path that is provided in the params
     * 
     * @param string $filePath
     * @return void
     */
    function ap_file_loader($filePath, $data = [], $isAbsolutePath = false)
    {
        if (!file_exists(plugin_dir_path(__FILE__) . trim($filePath, '/')) && !$isAbsolutePath) {
            throw new Exception('Invalid file', 404);
        }

        if($isAbsolutePath && !file_exists($filePath)) {
            throw new Exception('Invalid file', 404);
        }

        extract($data);
        require_once ($isAbsolutePath ? $filePath : plugin_dir_path(__FILE__) . trim($filePath, '/'));
    }
}

if (!function_exists('ap_abort')) {
    function ap_abort()
    {
        global $wp_query;
        $wp_query->is_404 = true;
    }
}

if (!function_exists('ap_route')) {
    function ap_route($name, $params = null)
    {
        $routes = wp_cache_get('ap_routes') ?  wp_cache_get('ap_routes') : [];
        $route = $routes[$name] ?? null;
        $query = null;
        $uri = '/';
        $params = $params ?? '';

        if ($route) {

            if (!is_array($params) && (count($route['raw_params']) == 1 || !count($route['raw_params']))) {
                $uri = str_replace($route['raw_params'], $params, $route['uri']);
            } else if (is_array($params) && count($route['raw_params']) > 0) {
                $selected_params = [];
                foreach ($route['params'] as $value) {
                    if (isset($params[$value]))
                        $selected_params[$value] = $params[$value];
                }
                $query = array_diff($params, $selected_params);
                $uri = str_replace($route['raw_params'], $selected_params, $route['uri']);
            } else if (is_array($params) && !count($route['raw_params'])) {
                $query = $params;
                $uri = $route['parsed_uri'];
            }

            if ($query) {
                $uri = $query ? $uri . '?' . http_build_query($query) : $uri;
            }

            return site_url($uri);
        }

        return '/';
    }
}

if (!function_exists('ap_is_route')) {
    function ap_is_route($route_name, $params = [], $has_multiple = false)
    {
        global $current_route;
        if ($has_multiple && is_array($route_name)) {
            $routeUris = [];
            array_walk($route_name, function ($route, $key) use (&$routeUris) {
                $routeUris[] = is_array($route) || is_string($key) ? ap_route($key, $route) : ap_route($route);
            });

            return in_array(site_url($current_route['parsed_uri']), $routeUris);
        } else {
            $routeUri = ap_route($route_name, $params);
            return site_url($current_route['parsed_uri']) == $routeUri;
        }
    }
}

if (!function_exists('ap_admin_route')) {
    function ap_admin_route($name, $params = [])
    {
        $base_url = admin_url('admin.php');
        $query = http_build_query(array_merge(['page' => 'ap_' . $name], $params));

        return $base_url . '?' . $query;
    }
}

if (!function_exists('ap_admin_api_route')) {
    function ap_admin_api_route($name, $params = [])
    {
        $base_url = admin_url('admin-ajax.php');
        $query = http_build_query(array_merge(['action' => 'wp_ajax_' . $name], $params));

        return $base_url . '?' . $query;
    }
}

if (!function_exists('now')) {
    /**
     * Get the current time
     * 
     * @param mixed|null $object
     * @return int|string|\DateTime
     */
    function now($object = null)
    {
        if ($object)
            return new DateTime();

        return current_time('U');
    }
}

if (!function_exists('request')) {

    /**
     * Request helper function, it'll return all request data
     * 
     * @param mixed|null $key Key name of the request query to get or set the value
     * @param mixed|null $value Value of the key to set in the request
     * @return \Autopublicate_Request|mixed
     */
    function request($key = null, $value = null)
    {
        if ($key && !isset($_GET[$key]))
            $_GET[$key] = $value;

        $request = new Autopublicate_Request;

        if ($key)
            return $request->$key ?? null;

        return $request;
    }
}

if (!function_exists('dd')) {

    /**
     * Dump and die
     * 
     * @param mixed $data
     * @return never
     */
    function dd($data, $style = true)
    {
        if (!$style) {
            echo  '<pre>';
            print_r($data);
            die;
        }

        echo  '<pre style="background: #111; color: #3cb53c;">';
        print_r($data);
        die;
    }
}

if (!function_exists('ap_alert')) {

    /**
     * This function will return if there is any kind of alert exists in the session
     * and also will destroy after return.
     * 
     * @param string|null $message Message to show the user
     * @param string $status Status of the action. Allowed statuses are: `success`, `warning`, `error`
     * @return array ['status' => 'success', 'message' => 'Message of the alert']. Session array ['_ap_alert' => ['status' => 'error', 'message' => 'Message']]
     */
    function ap_alert(string $message = null, string $status = 'error', $data = null)
    {
        if ($message) {

            //Make class for the alert
            switch ($status) {
                case 'success':
                    $class = 'updated';
                    break;

                case 'warning':
                    $class = 'warning';
                    break;

                default:
                    $class = 'error';
                    break;
            }

            return session('_ap_alert', [
                'message' => $message,
                'status' => $status,
                'class' => $class,
                'data' => $data
            ]);
        }

        if ($alert = session('_ap_alert')) {

            //Unset or delete the alert message
            unset($_SESSION['_ap_alert']);

            return $alert;
        }

        return false;
    }
}

if (!function_exists('alert_response')) {

    /**
     * Get the ajax response along with message and data with the status
     * 
     * @param string $message Message to let the user know
     * @param string $status Status of the action
     * @param mixed|null $data Data to deliver to the action creator
     * @return response Response json data
     */
    function alert_response(string $message, $status = 'error', $data = null)
    {
        return response(alert($message, $status, $data));
    }
}

if (!function_exists('asset')) {

    /**
     * Asset url preparing for asset using
     * 
     * @param string $file Asset file with path
     * @return string URL of the resource
     */
    function asset(string $file)
    {
        return plugin_dir_url(__FILE__) . $file;
    }
}

if (!function_exists('session')) {
    /**
     * Session helper function, it'll return specific session data using key
     * 
     * @param mixed|null $key Key name of the session to get or set the value
     * @param mixed|null $value Value of the key to set in the session
     * @return mixed|null
     */
    function session($key = null, $value = null)
    {
        if ($key && $value)
            $_SESSION[$key] = $value;

        if (isset($_SESSION[$key]))
            return $_SESSION[$key];

        return false;
    }
}

if (!function_exists('paginate')) {
    function paginate(Select $query, $page = 1, $per_page = 15)
    {
        return [
            'per_page' => $per_page,
            'page' => $page,
            'total_pages' => ceil((clone $query)->count() / $per_page) ?: 1,
            'data' => $query->page($page - 1, $per_page)->get(),
        ];
    }
}

if (!function_exists('ap_paginate_view')) {
    function ap_paginate_view($pagination)
    {
        ap_file_loader('public/views/layouts/sections/pagination.php', ['pagination' => $pagination]);
    }
}

if (!function_exists('ap_admin_paginate_view')) {
    function ap_admin_paginate_view($pagination)
    {
        ap_file_loader('admin/views/layouts/sections/pagination.php', ['pagination' => $pagination]);
    }
}

if (!function_exists('ap_send_mail')) {

    function ap_send_mail($to, $subject, $body)
    {
        if (is_array($body)) {
            $content = file_get_contents(dirname(__FILE__) . '/' . $body['path'] . '.php');

            $body['params']['subject'] = $subject;
            foreach ($body['params'] as $var => $value) $content = str_replace("[$var]", $value, $content);
        }
        $headers = array('From: Autopublícate® <noreply@autopublicate.com>');

        add_filter('wp_mail_content_type', 'set_html_content_type');
        $sent = wp_mail($to, $subject, $content, $headers);
        remove_filter('wp_mail_content_type', 'set_html_content_type');

        return $sent;
    }

    function set_html_content_type($content_type)
    {
        return 'text/html';
    }
}

if (!function_exists('ap_date_format')) {
    function ap_date_format(string $date = null, string $format = 'Y-m-d H:i:s')
    {
        return (new \DateTime($date ?? ''))->setTimezone(wp_timezone())->format($format);
    }
}

if (!function_exists('prepare_provider_amount')) {

    function prepare_provider_amount(float $amount)
    {
        $setting = maybe_unserialize(get_option('ap_payment_settings'));

        if (!isset($setting['provider_charge'])) {
            return $amount;
        }

        if ($setting['provider_charge_type'] == '%') {
            $charge = $amount * floatval($setting['provider_charge']) / 100;
        } else {
            $charge = $amount - floatval($setting['provider_charge']);
        }

        return $amount - $charge;
    }
}

if (!function_exists('prepare_buyer_amount')) {

    function prepare_buyer_amount(float $amount)
    {
        $setting = maybe_unserialize(get_option('ap_payment_settings'));

        if (!isset($setting['buyer_charge'])) {
            return $amount;
        }

        if ($setting['buyer_charge_type'] == '%') {
            $charge = $amount * floatval($setting['buyer_charge']) / 100;
        } else {
            $charge = $amount - floatval($setting['buyer_charge']);
        }

        return $amount - $charge;
    }
}
