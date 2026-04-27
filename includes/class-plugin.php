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
	}
}
