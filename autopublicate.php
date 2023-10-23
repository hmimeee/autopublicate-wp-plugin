<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://autopublicate.com
 * @since             1.0.0
 * @package           Autopublicate
 *
 * @wordpress-plugin
 * Plugin Name:       Autopublícate®
 * Plugin URI:        https://autopublicate.com
 * Description:       Autopublícate was created to connect writers who want to self-publish with freelancers who offer services from the publishing world.
 * Version:           1.0.1
 * Author:            Autopublícate®
 * Author URI:        https://autopublicate.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       autopublicate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('AUTOPUBLICATE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-autopublicate-activator.php
 */
function activate_autopublicate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-autopublicate-activator.php';
	Autopublicate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-autopublicate-deactivator.php
 */
function deactivate_autopublicate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-autopublicate-deactivator.php';
	Autopublicate_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_autopublicate');
register_deactivation_hook(__FILE__, 'deactivate_autopublicate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-autopublicate.php';

/**
 * The plugin helpers that are beign used in the plugin files,
 */
require plugin_dir_path(__FILE__) . 'helpers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_autopublicate()
{

	$plugin = new Autopublicate();
	$plugin->run();
}
run_autopublicate();