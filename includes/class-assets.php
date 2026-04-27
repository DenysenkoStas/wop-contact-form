<?php
/**
 * Assets manager. Enqueues frontend CSS/JS only on pages that need them.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Assets {

	const HANDLE_CSS = 'wop-cf-form';
	const HANDLE_JS = 'wop-cf-form';
	const HANDLE_INPUTMASK = 'wop-cf-inputmask';

	const NONCE_ACTION = 'wop_cf_submit';

	public function register() {
		add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend'));
	}

	/**
	 * Enqueue frontend assets, but only on pages that actually contain the shortcode.
	 */
	public function enqueue_frontend() {
		if ( ! $this->should_enqueue()) {
			return;
		}

		wp_enqueue_style(
			self::HANDLE_CSS,
			WOP_CF_PLUGIN_URL . 'assets/css/form.css',
			array(),
			WOP_CF_VERSION
		);

		wp_enqueue_script(
			self::HANDLE_INPUTMASK,
			'https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/inputmask.min.js',
			array(),
			'5.0.9',
			true
		);

		wp_enqueue_script(
			self::HANDLE_JS,
			WOP_CF_PLUGIN_URL . 'assets/js/form.js',
			array(self::HANDLE_INPUTMASK),
			WOP_CF_VERSION,
			true
		);

		wp_localize_script(
			self::HANDLE_JS,
			'wopCfData',
			array(
				'ajaxUrl'    => admin_url('admin-ajax.php'),
				'nonce'      => wp_create_nonce(self::NONCE_ACTION),
				'action'     => 'wop_cf_submit',
				'phoneMask'  => '+380 (99) 999-99-99',
				'maxFileMb'  => 5,
				'allowedExt' => array('jpg', 'jpeg', 'png'),
				'i18n'       => array(
					'requiredFullName' => __('Please enter your full name.', 'wop-contact-form'),
					'requiredPhone'    => __('Please enter a valid phone number.', 'wop-contact-form'),
					'invalidEmail'     => __('Please enter a valid email address.', 'wop-contact-form'),
					'fileTooLarge'     => __('File is too large. Maximum size is %d MB.', 'wop-contact-form'),
					'fileWrongType'    => __('Only JPG and PNG files are allowed.', 'wop-contact-form'),
					'sending'          => __('Sending…', 'wop-contact-form'),
					'genericError'     => __('Something went wrong. Please try again.', 'wop-contact-form'),
				),
			)
		);
	}

	/**
	 * Whether the current request should load the form assets.
	 *
	 * @return bool
	 */
	private function should_enqueue() {
		if ( ! is_singular()) {
			return false;
		}

		$post = get_post();
		if ( ! $post) {
			return false;
		}

		return has_shortcode($post->post_content, WOP_CF_Shortcode::TAG);
	}
}
