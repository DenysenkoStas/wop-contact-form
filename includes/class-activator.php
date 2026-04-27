<?php
/**
 * Plugin activator. Creates the custom submissions table.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Activator {

	/**
	 * @return string Submissions table name with WP prefix.
	 */
	public static function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wop_cf_submissions';
	}

	/**
	 * Create the submissions table.
	 */
	public static function activate() {
		global $wpdb;

		$table_name      = self::get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			full_name VARCHAR(255) NOT NULL DEFAULT '',
			phone VARCHAR(50) NOT NULL DEFAULT '',
			email VARCHAR(255) NOT NULL DEFAULT '',
			city VARCHAR(255) NOT NULL DEFAULT '',
			project_details TEXT NOT NULL,
			attachment_id BIGINT(20) UNSIGNED DEFAULT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY created_at (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}
}
