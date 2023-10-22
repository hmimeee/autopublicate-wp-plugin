<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://autopublicate.com
 * @since      1.0.0
 *
 * @package    Autopublicate
 * @subpackage Autopublicate/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Autopublicate
 * @subpackage Autopublicate/includes
 * @author     Autopublícate® <contact@autopublicate.com>
 */
class Autopublicate_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		global $wpdb; // the wordpress database object

		$sql = $wpdb->prepare("
			ALTER TABLE %1s
			DROP COLUMN image,
			DROP COLUMN country,
			DROP COLUMN profession_title,
			DROP COLUMN languages,
			DROP COLUMN about,
			DROP COLUMN professional_description,
			DROP COLUMN attachments,
			DROP COLUMN skills;
		", $wpdb->prefix . 'users');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		DROP TABLE %1s;
		", $wpdb->prefix . 'ap_contracts');

		// run the query
		$wpdb->query($sql);
	}
}
