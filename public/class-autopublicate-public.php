<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://autopublicate.com
 * @since      1.0.0
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/public
 * @author     Autopublícate® <contact@autopublicate.com>
 */
class Autopublicate_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Autopublicate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Autopublicate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/autopublicate-public.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-plugin', plugin_dir_url(__FILE__) . 'css/autopublicate-plugin-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Autopublicate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Autopublicate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/autopublicate-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . '-plugin', plugin_dir_url(__FILE__) . 'js/autopublicate-plugin-public.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register the services for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_services()
	{
		autopublicate_loader('public/services');
		autopublicate_loader('public/controllers');
		autopublicate_loader('public/routes');

		//Initiate the route service
		Route_Service::init();
	}
}
