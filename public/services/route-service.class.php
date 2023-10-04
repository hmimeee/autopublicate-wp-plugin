<?php

class Route_Service
{
    protected $routes;

    public static function init()
    {
        //Route preparing
        if (isset(parse_url($_SERVER['REQUEST_URI'])['path']) && parse_url($_SERVER['REQUEST_URI'])['path'] != '/') {
            $routes = wp_cache_get('routes');
            $parsed_uri = parse_url($_SERVER['REQUEST_URI'])['path'];
            $uri_parts = explode('/', trim($parsed_uri, '/'));

            array_filter($routes, function ($route) use ($uri_parts, $routes) {
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

                if (implode('/', $parts) == implode('/', $uri_parts) && $route['method'] == $_SERVER['REQUEST_METHOD']) {
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
            'params' => $params,
            'class' => $class,
            'function' => $function,
            'method' => $method
        ];
        wp_cache_set('routes', $routes);
    }

    public static function get($uri, $params)
    {
        $class = reset($params);
        $function = end($params);

        $object = new self;
        $object->add_route($uri, $class, $function);
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
