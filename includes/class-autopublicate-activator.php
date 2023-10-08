<?php

/**
 * Fired during plugin activation
 *
 * @link       https://autopublicate.com
 * @since      1.0.0
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Autopublicate
 * @subpackage Autopublicate/includes
 * @author     Autopublícate® <contact@autopublicate.com>
 */
class Autopublicate_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb; // the wordpress database object

		$sql = $wpdb->prepare("
			ALTER TABLE %1s
			ADD profession_title varchar(255) NULL,
			ADD country varchar(255) NULL,
			ADD languages varchar(255) NULL,
			ADD skills varchar(255) NULL,
			ADD about LONGTEXT NULL;
		", $wpdb->prefix . 'users');

		// run the query
		$wpdb->query($sql);
	}
}
