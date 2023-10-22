<?php

use ClanCats\Hydrahon\Query\Sql\Func;

class Autopublicate_Short_Code
{
	protected $plugin_name;

	 /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      ap_loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    public function __construct($plugin_name)
    {
        $this->loader = new ap_loader();
		$this->plugin_name = $plugin_name;

		$this->add_short_code('ap-profile-card', 'profileCard');
    }

	/**
     * Load the service using the service class method
     * 
     * @param object $class A reference to the instance of the object on which the action is defined.
     * @param string $method The name of the function definition on the $class.
     */
    public function add_short_code($code, $method)
    {
        add_shortcode($code, [$this, $method]);
    }

	/**
	 * Central location to create all shortcodes.
	 */
	public function profileCard($atts = [])
	{
		wp_enqueue_style($this->plugin_name . '-plugin', plugin_dir_url(__DIR__) . 'public/css/autopublicate-plugin-public.css', array(), AUTOPUBLICATE_VERSION, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__DIR__) . 'public/css/autopublicate-public.css', array(), AUTOPUBLICATE_VERSION, 'all');
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__DIR__) . 'public/js/autopublicate-public.js', array('jquery'), AUTOPUBLICATE_VERSION, false);

		$user = get_user_by('login', $atts['user']);
		if(!$user) {
			echo '<div class="alert alert-danger">Autopublicate Profile Card: User not found!</div>';
			return;
		}

		$contracts = AP_Contract_Model::query()
			->select([new Func('sum', 'rating'), new Func('count', 'id')])
			->where('provider_id', $user->get('ID'))
			->whereNotNull('rating')
			->whereIn('status', ['completed', 'cleared'])
			->one();
		$rating = round(reset($contracts)) && end($contracts) ? round(reset($contracts) / end($contracts)) : 0;
		$rating_count = end($contracts);

		ap_file_loader('public/views/short-codes/user-card.php', compact('user', 'rating', 'rating_count'));
	}
}
