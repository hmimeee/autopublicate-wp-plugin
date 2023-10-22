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
			ADD image varchar(255) NULL;
			ADD profession_title varchar(255) NULL,
			ADD country varchar(255) NULL,
			ADD languages varchar(255) NULL,
			ADD skills varchar(255) NULL,
			ADD professional_description longtext NULL,
			ADD about TEXT NULL;
		", $wpdb->prefix . 'users');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		CREATE TABLE %1s (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			provider_id BIGINT UNSIGNED NOT NULL,
			buyer_id BIGINT UNSIGNED NOT NULL,
			title varchar(255) NOT NULL,
			description longtext NULL,
			expected_deadline date NULL,
			deadline date NULL,
			budget_type varchar(255) NULL,
			budget decimal(10,4) NULL,
			final_budget decimal(10,4) NOT NULL DEFAULT 0,
			status enum('pending', 'modified', 'approved', 'delivered', 'completed', 'cleared','cancelled') NOT NULL DEFAULT 'pending',
			attachments varchar(255) NULL,
			modified_by BIGINT UNSIGNED NULL,
			delivery_notes longtext NULL,
			delivery_attachments varchar(255) NULL,
			rating int NULL,
			review varchar(255) NULL,
			delivered_at datetime NULL,
			completed_at datetime NULL,
			updated_at datetime NULL,
			created_at datetime NULL,

			FOREIGN KEY (provider_id) REFERENCES {$wpdb->prefix}users (ID) ON DELETE CASCADE,
			FOREIGN KEY (buyer_id) REFERENCES {$wpdb->prefix}users (ID) ON DELETE CASCADE,
			FOREIGN KEY (modified_by) REFERENCES {$wpdb->prefix}users (ID) ON DELETE SET NULL
			)
		", $wpdb->prefix . 'ap_contracts');

		// run the query
		$wpdb->query($sql);

		$sql = $wpdb->prepare("
		CREATE TABLE %1s (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT UNSIGNED NOT NULL,
			contract_id BIGINT UNSIGNED NOT NULL,
			comment longtext NULL,
			read_at datetime NULL,
			created_at datetime NULL,

			FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users (ID) ON DELETE CASCADE,
			FOREIGN KEY (contract_id) REFERENCES {$wpdb->prefix}ap_contracts (id) ON DELETE CASCADE
			)
			", $wpdb->prefix . 'ap_contract_comments');

		// run the query
		$wpdb->query($sql);
	}
}
