<?php
/**
 * Form data sanitizer and validator.
 *
 * Sanitization runs first (always), then validation runs against the
 * sanitized values. Returns either ['data' => ..., 'errors' => []]
 * or ['data' => ..., 'errors' => [...]].
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Form_Validator {

	/**
	 * Sanitize and validate raw $_POST data.
	 *
	 * @param array $raw Raw input (e.g. $_POST).
	 *
	 * @return array {
	 * @type array $data Sanitized values, ready for storage.
	 * @type array $errors Field name => error message. Empty when valid.
	 * }
	 */
	public function process(array $raw) {
		$data = array(
			'full_name'       => isset($raw['full_name']) ? sanitize_text_field(wp_unslash($raw['full_name'])) : '',
			'phone'           => isset($raw['phone']) ? sanitize_text_field(wp_unslash($raw['phone'])) : '',
			'email'           => isset($raw['email']) ? sanitize_email(wp_unslash($raw['email'])) : '',
			'city'            => isset($raw['city']) ? sanitize_text_field(wp_unslash($raw['city'])) : '',
			'project_details' => isset($raw['project_details']) ? sanitize_textarea_field(wp_unslash($raw['project_details'])) : '',
		);

		$errors = array();

		if ('' === $data['full_name']) {
			$errors['full_name'] = __('Please enter your full name.', 'wop-contact-form');
		}

		$phone_digits = preg_replace('/\D/', '', $data['phone']);
		if (strlen($phone_digits) < 10) {
			$errors['phone'] = __('Please enter a valid phone number.', 'wop-contact-form');
		}

		if ('' === $data['email'] || ! is_email($data['email'])) {
			$errors['email'] = __('Please enter a valid email address.', 'wop-contact-form');
		}

		return array(
			'data'   => $data,
			'errors' => $errors,
		);
	}
}
