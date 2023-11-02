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
			DROP image,
			DROP country,
			DROP profession_title,
			DROP languages,
			DROP about,
			DROP professional_description,
			DROP attachments,
			DROP skills;
		", $wpdb->prefix . 'users');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		DROP TABLE %1s;
		", $wpdb->prefix . 'ap_contract_comments');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		DROP TABLE %1s;
		", $wpdb->prefix . 'ap_transactions');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		DROP TABLE %1s;
		", $wpdb->prefix . 'ap_contracts');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		DROP TABLE %1s;
		", $wpdb->prefix . 'ap_payout_requests');

		// run the query
		$wpdb->query($sql);
	}
}
