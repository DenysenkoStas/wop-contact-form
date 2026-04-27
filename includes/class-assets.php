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
