<?php
/**
 * Main plugin class. Wires together all components.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Plugin {

	/**
	 * Wire up plugin components and register hooks.
	 */
	public function init() {
		(new WOP_CF_Shortcode())->register();
		(new WOP_CF_Assets())->register();

		$ajax_handler = new WOP_CF_Ajax_Handler(
			new WOP_CF_Form_Validator(),
			new WOP_CF_Submission_Repository(),
			new WOP_CF_File_Uploader()
		);
		$ajax_handler->register();
	}
}
