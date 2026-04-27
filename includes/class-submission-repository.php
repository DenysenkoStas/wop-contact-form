<?php
/**
 * Repository for the submissions table. Encapsulates all DB access.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Submission_Repository {

	/**
	 * Insert a new submission.
	 *
	 * @param array $data Sanitized fields: full_name, phone, email, city, project_details.
	 * @param int|null $attachment_id WP media attachment ID, or null when no file was uploaded.
	 *
	 * @return int|false Inserted row ID on success, false on DB failure.
	 */
	public function insert(array $data, $attachment_id = null) {
		global $wpdb;

		$inserted = $wpdb->insert(
			WOP_CF_Activator::get_table_name(),
			array(
				'full_name'       => $data['full_name'],
				'phone'           => $data['phone'],
				'email'           => $data['email'],
				'city'            => $data['city'],
				'project_details' => $data['project_details'],
				'attachment_id'   => $attachment_id ? (int) $attachment_id : null,
				'created_at'      => current_time('mysql'),
			),
			array('%s', '%s', '%s', '%s', '%s', '%d', '%s')
		);

		if (false === $inserted) {
			return false;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Fetch all submissions, newest first.
	 *
	 * @return array<int, object>
	 */
	public function get_all() {
		global $wpdb;

		$table = WOP_CF_Activator::get_table_name();

		return $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC");
	}
}
