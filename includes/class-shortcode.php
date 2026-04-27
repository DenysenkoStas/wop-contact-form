<?php
/**
 * Shortcode handler. Renders the contact form template.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Shortcode {

	const TAG = 'contact_form';

	public function register() {
		add_shortcode(self::TAG, array($this, 'render'));
	}

	/**
	 * Render the form template and return its HTML.
	 *
	 * Shortcode callbacks must return a string, not echo it — otherwise output
	 * appears at the top of the page instead of where the shortcode was placed.
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		include WOP_CF_TEMPLATES_DIR . 'form.php';

		return ob_get_clean();
	}
}
