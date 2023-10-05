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
