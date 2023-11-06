<?php

class AP_Route_Service
{
    protected $instance;

    protected $routes;

    protected $uri;

    protected $method;

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

                if ($route['parsed_uri'] == implode('/', $uri_parts) && ($route['method'] == $_SERVER['REQUEST_METHOD'] || $route['method'] == 'any')) {
                    if (($route['auth'] && !get_current_user_id())) {
                        wp_redirect(wp_login_url());
                        exit;
                    }

                    //Declare a global variable to get current route
                    global $current_route;
                    $current_route = $route;

                    wp_enqueue_style($plugin_name . '-plugin', plugin_dir_url(__DIR__) . 'css/autopublicate-plugin-public.css', array(), AUTOPUBLICATE_VERSION, 'all');
                    wp_enqueue_style($plugin_name, plugin_dir_url(__DIR__) . 'css/autopublicate-public.css', array(), AUTOPUBLICATE_VERSION, 'all');

                    wp_enqueue_script($plugin_name . '-bootstrap-bundle', plugin_dir_url(__DIR__) . 'js/bootstrap.bundle.min.js', array('jquery'), AUTOPUBLICATE_VERSION, false);
                    wp_enqueue_script($plugin_name, plugin_dir_url(__DIR__) . 'js/autopublicate-public.js', array('jquery'), AUTOPUBLICATE_VERSION, false);

                    $object = new $route['class'];
                    extract($params);
                    call_user_func([$object, $route['function']], ...array_values($params));
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
            'method' => $method,
            'auth' => false
        ];
        wp_cache_set('routes', $routes);
    }

    public function name($name)
    {
        $routes = wp_cache_get('routes');
        $filter = $this->route(true);
        $route = reset($filter);
        $keys = array_keys($filter);
        $index = reset($keys);

        unset($routes[$index]);
        $routes[$name] = $route;

        wp_cache_set('routes', $routes);

        return $this;
    }

    public function auth()
    {
        $routes = wp_cache_get('routes');
        $filter = $this->route(true);
        $route = reset($filter);
        $keys = array_keys($filter);
        $key = reset($keys);

        $route['auth'] = true;
        $routes[$key] = $route;

        wp_cache_set('routes', $routes);

        return $this;
    }

    private function route($withKey = false)
    {
        $routes = wp_cache_get('routes') ?  wp_cache_get('routes') : [];
        $filter = array_filter($routes, fn ($dt) => $dt['uri'] == $this->uri && $dt['method'] == $this->method);
        $route = reset($filter);

        if (!$route) {
            echo 'Invalid route declaration sequence, please call the route method first';
            exit;
        }

        return $withKey ? $filter : $route;
    }

    public function _get($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $this->add_route($uri, $class, $function);
        $this->uri = $uri;
        $this->method = 'GET';

        return $this;
    }

    public function _post($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $this->add_route($uri, $class, $function, 'POST');
        $this->uri = $uri;
        $this->method = 'POST';

        return $this;
    }

    public function _any($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $this->add_route($uri, $class, $function, 'ANY');
        $this->uri = $uri;
        $this->method = 'ANY';

        return $this;
    }

    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        return (new static())->call($method, $args);
    }

    private function call($method, $args)
    {
        if (!method_exists($this, '_' . $method)) {
            throw new Exception('Call undefined method ' . $method);
        }

        return $this->{'_' . $method}(...$args);
    }
}
