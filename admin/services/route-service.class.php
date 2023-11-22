<?php

class AP_Route_Service
{
    use AP_Static_Handler;

    private $routes;

    /**
     * Get method server request handler
     * 
     * @param string $path URI path to indicate what user wants to see
     * @param string $controller Controller for the correspondant request
     * @param string $method Controller method in which function will handle the request
     * @return mixed
     */
    public function _get(string $path, $params)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') return;

        $controller = reset($params);
        $method = end($params);

        $this->routes[] = [
            'path' => $path,
            'controller' => $controller,
            'method' => $method
        ];

        if (request('page') ==  'ap_' . $path) {
            $class = new $controller;
            return $class->$method();
        }
    }

    /**
     * Post method server request handler
     * 
     * @param string $path URI path to indicate what user wants to see
     * @param string $controller Controller for the correspondant request
     * @param string $method Controller method in which function will handle the request
     * @return mixed
     */
    public function _post(string $path, $params)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

        $controller = reset($params);
        $method = end($params);

        if (request('page') == 'ap_' . $path) {
            $class = new $controller;
            return $class->$method();
        }
    }

    /**
     * Post method server request handler
     * 
     * @param string $path URI path to indicate what user wants to see
     * @param string $controller Controller for the correspondant request
     * @param string $method Controller static method in which function will handle the request
     * @return mixed
     */
    public function _api(string $path, $params, $public = false)
    {
        $controller = reset($params);
        $method = end($params);

        if ('wp_ajax_' . $path == request('action')) {
            $class = new $controller;
            return $class->$method();
        }
    }
}
