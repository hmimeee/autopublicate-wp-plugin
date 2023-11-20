<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://autopublicate.com
 * @since      1.0.0
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/admin
 * @author     Autopublícate® <contact@autopublicate.com>
 */
class Autopublicate_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ap_loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ap_loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/autopublicate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ap_loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ap_loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/autopublicate-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * This function is provided for demonstration purposes only.
	 * 
	 * This function will include all the files inside the admin folder
	 * for admin panel actions.
	 */
	public function enqueue_files()
	{
		session_start();
		/**
		 * These classes are responsible for admin panel controlling
		 * core plugin.
		 */
		if (strpos($_SERVER['REQUEST_URI'], 'wp-admin')) {
			ap_loader('admin/controllers');
			ap_loader('admin/services');
		}
	}

	/**
	 * This function is provided for demonstration purposes only.
	 * 
	 * This function will add admin main menu for the Plugin
	 */
	public function admin_menu()
	{
		add_menu_page(
			'Autopublícate® Dashboard',
			'Autopublícate®',
			'ap_access_none',
			'autopublicate',
			array('Autopublicate_Admin', 'route'),
			plugin_dir_url(__FILE__) . 'img/icon.png',
			20
		);

		add_submenu_page(
			'autopublicate',
			'Autopublícate® - Dashboard',
			'Dashboard',
			'read',
			'ap_dashboard',
			array('Autopublicate_Admin', 'route'),
			1
		);

		add_submenu_page(
			'autopublicate',
			'Autopublícate® - Contracts',
			'Contracts',
			'read',
			'ap_contracts',
			array('Autopublicate_Admin', 'route'),
			2
		);

		add_submenu_page(
			'autopublicate',
			'Autopublícate® - Settings',
			'Settings',
			'read',
			'ap_settings',
			array('Autopublicate_Admin', 'route'),
			3
		);


		add_submenu_page(
			'contracts',
			'Autopublícate® - Contract View',
			null,
			'read',
			'ap_contract_view',
			array('Autopublicate_Admin', 'route'),
			2
		);

		add_submenu_page(
			'contracts',
			'Autopublícate® - Contract Resolution',
			null,
			'read',
			'ap_contract_resolution',
			array('Autopublicate_Admin', 'route'),
			2
		);
	}

	public static function route()
	{
		require_once __DIR__ . '/routes/web.php';
	}
}
