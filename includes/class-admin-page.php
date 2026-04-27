<?php
/**
 * Admin page that lists all submissions.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Admin_Page {

	const MENU_SLUG = 'wop-cf-submissions';
	const CAPABILITY = 'manage_options';

	/**
	 * @var WOP_CF_Submission_Repository
	 */
	private $repository;

	public function __construct(WOP_CF_Submission_Repository $repository) {
		$this->repository = $repository;
	}

	public function register() {
		add_action('admin_menu', array($this, 'add_menu'));
	}

	/**
	 * Register the top-level admin menu item.
	 */
	public function add_menu() {
		add_menu_page(
			__('Contact Form Submissions', 'wop-contact-form'),
			__('Submissions', 'wop-contact-form'),
			self::CAPABILITY,
			self::MENU_SLUG,
			array($this, 'render'),
			'dashicons-email-alt',
			26
		);
	}

	/**
	 * Render the submissions page.
	 */
	public function render() {
		if ( ! current_user_can(self::CAPABILITY)) {
			wp_die(esc_html__('You do not have permission to view this page.', 'wop-contact-form'));
		}

		$submissions = $this->repository->get_all();

		include WOP_CF_TEMPLATES_DIR . 'admin-submissions.php';
	}
}
