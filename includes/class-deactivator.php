<?php
/**
 * Plugin deactivator. Drops the custom submissions table.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Deactivator {

	/**
	 * Drop the submissions table.
	 */
	public static function deactivate() {
		global $wpdb;

		$table_name = WOP_CF_Activator::get_table_name();

		$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
	}
}
