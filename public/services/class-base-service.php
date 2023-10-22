<?php

class AP_Base_Service
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      ap_loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    public function __construct()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-autopublicate-loader.php';

        $this->loader = new ap_loader();
    }

    /**
     * Load the service using the service class method
     * 
     * @param object $class A reference to the instance of the object on which the action is defined.
     * @param string $method The name of the function definition on the $class.
     */
    public function load_service($class, $method, $hook = 'wp_enqueue_scripts')
    {
        $this->loader->add_action($hook, $class, $method);
    }
}
