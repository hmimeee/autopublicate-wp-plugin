<?php

class Route_Service
{
    protected $uri;

    public static function init($plugin_name)
    {
        //Route preparing
        if (isset(parse_url($_SERVER['REQUEST_URI'])['path']) && parse_url($_SERVER['REQUEST_URI'])['path'] != '/') {
            $routes = wp_cache_get('routes');
            $parsed_uri = parse_url($_SERVER['REQUEST_URI'])['path'];
            $uri_parts = explode('/', trim($parsed_uri, '/'));

            array_filter($routes, function ($route) use ($uri_parts, $plugin_name) {
                $parts = explode('/', $route['parsed_uri']);
                if (count($parts) != count($uri_parts)) {
                    return false;
                }

                $variables = $route['params'];

                $params = [];
                foreach (array_keys($parts, '', true) as $i => $index) {
                    if (isset($variables[$i])) {                        
                        $params[$variables[$i]] = $uri_parts[$index];
                    }
                    $parts[$index] = $uri_parts[$index];
                }


                $route['parsed_uri'] = implode('/', $parts);
                //Declare a global variable to get current route
                global $current_route;
                $current_route = $route;

                if ($route['parsed_uri'] == implode('/', $uri_parts) && $route['method'] == $_SERVER['REQUEST_METHOD']) {
                    wp_enqueue_style($plugin_name . '-plugin', plugin_dir_url(__DIR__) . 'css/autopublicate-plugin-public.css', array(), AUTOPUBLICATE_VERSION, 'all');
                    wp_enqueue_style($plugin_name, plugin_dir_url(__DIR__) . 'css/autopublicate-public.css', array(), AUTOPUBLICATE_VERSION, 'all');

                    wp_enqueue_script($plugin_name, plugin_dir_url(__DIR__) . 'js/autopublicate-public.js', array('jquery'), AUTOPUBLICATE_VERSION, false);

                    $object = new $route['class'];
                    extract($params);
                    call_user_func([$object, $route['function']], ...array_values($params));
                    return true;
                }
            });
        }
    }

    public function add_route($uri, $class, $function, $method = 'GET')
    {
        preg_match_all("/{[^}]*}/", $uri, $matches);
        $parsed_uri = str_replace(reset($matches), '', $uri);
        $params = str_replace(['{', '}'], '', reset($matches));

        $routes = wp_cache_get('routes') ?  wp_cache_get('routes') : [];
        $routes[] = [
            'uri' => $uri,
            'parsed_uri' => $parsed_uri,
            'raw_params' => reset($matches),
            'params' => $params,
            'class' => $class,
            'function' => $function,
            'method' => $method
        ];
        wp_cache_set('routes', $routes);
    }

    public function name($name)
    {
        $routes = wp_cache_get('routes') ?  wp_cache_get('routes') : [];
        $filter = array_filter($routes, fn ($dt) => $dt['uri'] == $this->uri);
        $route = reset($filter);

        if (!$route) {
            echo 'Invalid route declaration sequence, please call the route method first';
            exit;
        }

        $keys = array_keys($filter);
        $index = reset($keys);

        unset($routes[$index]);
        $routes[$name] = $route;

        wp_cache_set('routes', $routes);

        return $this;
    }

    public static function get($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function);
        $object->uri = $uri;

        return $object;
    }

    public static function post($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function, 'POST');
    }

    public static function put($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function, 'PUT');
    }

    public static function delete($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function, 'DELETE');
    }

    public static function patch($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function, 'PATCH');
    }

    public static function any($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function, null);
    }
}
