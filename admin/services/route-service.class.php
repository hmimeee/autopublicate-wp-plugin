<?php

class AP_Route_Service
{
    public function __construct()
    {
        //
    }

    /**
     * Get method server request handler
     * 
     * @param string $path URI path to indicate what user wants to see
     * @param string $controller Controller for the correspondant request
     * @param string $method Controller method in which function will handle the request
     * @return mixed
     */
    public static function get(string $path, $params)
    {
        $controller = reset($params);
        $method = end($params);

        if (request('page') == $path && empty($_POST)) {
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
    public static function post(string $path, $params)
    {
        $controller = reset($params);
        $method = end($params);
        
        if (request('page') == $path && !empty($_POST)) {
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
    public static function api(string $path, string $controller, string $method, $public = false)
    {
        add_action('wp_ajax_' . $path, [$controller, $method]);

        if($public)
        add_action('wp_ajax_nopriv_' . $path, [ $controller, $method]);
    }
}
