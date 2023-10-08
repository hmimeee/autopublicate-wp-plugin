<?php

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
    function ap_file_loader($filePath, $data = [])
    {
        if (!file_exists(plugin_dir_path(__FILE__) . trim($filePath, '/'))) {
            throw new Exception('Invalid file', 404);
        }

        extract($data);
        require_once plugin_dir_path(__FILE__) . trim($filePath, '/');
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
        $routes = wp_cache_get('routes') ?  wp_cache_get('routes') : [];
        $route = $routes[$name] ?? null;
        $query = null;
        $uri = '/';

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
            }

            if ($query) {
                $uri = $query ? $uri . '?' . http_build_query($query) : $uri;
            }

            return site_url($uri);
        }

        return '/';
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
     * @return \Autopublicate_Request
     */
    function request($key = null, $value = null)
    {
        if ($key && $value)
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

if (!function_exists('alert')) {

    /**
     * This function will return if there is any kind of alert exists in the session
     * and also will destroy after return.
     * 
     * @param string|null $message Message to show the user
     * @param string $status Status of the action. Allowed statuses are: `success`, `warning`, `error`
     * @return array ['status' => 'success', 'message' => 'Message of the alert']. Session array ['_bc_alert' => ['status' => 'error', 'message' => 'Message']]
     */
    function alert(string $message = null, string $status = 'error', $data = null)
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

            return session('_bc_alert', [
                'message' => $message,
                'status' => $status,
                'class' => $class,
                'data' => $data
            ]);
        }

        if ($alert = session('_bc_alert')) {

            //Unset or delete the alert message
            unset($_SESSION['_bc_alert']);

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
