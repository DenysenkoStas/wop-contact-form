<?php
/**
 * Plugin Name:       WOP Contact Form
 * Plugin URI:        https://github.com/DenysenkoStas/wop-contact-form
 * Description:       Custom contact form plugin with AJAX submission, file uploads and admin submissions page.
 * Version:           0.1.0
 * Author:            Stas Denysenko
 * Author URI:        https://github.com/DenysenkoStas
 * License:           GPL-2.0+
 * Text Domain:       wop-contact-form
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

define('WOP_CF_VERSION', '0.1.0');
define('WOP_CF_PLUGIN_FILE', __FILE__);
define('WOP_CF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WOP_CF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOP_CF_INCLUDES_DIR', WOP_CF_PLUGIN_DIR . 'includes/');
define('WOP_CF_TEMPLATES_DIR', WOP_CF_PLUGIN_DIR . 'templates/');

/**
 * Simple PSR-4-like autoloader for plugin classes.
 *
 * Maps `WOP_CF_Plugin` -> includes/class-plugin.php
 *      `WOP_CF_Submission_Repository` -> includes/class-submission-repository.php
 */
spl_autoload_register(
	function ($class_name) {
		if (strpos($class_name, 'WOP_CF_') !== 0) {
			return;
		}

		$relative   = strtolower(str_replace('WOP_CF_', '', $class_name));
		$relative   = str_replace('_', '-', $relative);
		$class_file = WOP_CF_INCLUDES_DIR . 'class-' . $relative . '.php';

		if (file_exists($class_file)) {
			require_once $class_file;
		}
	}
);

// Activation/deactivation hooks must live in the main plugin file (WP core requirement).
register_activation_hook(__FILE__, array('WOP_CF_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('WOP_CF_Deactivator', 'deactivate'));

/**
 * Initialize the plugin on plugins_loaded.
 */
function wop_cf_init() {
	$plugin = new WOP_CF_Plugin();
	$plugin->init();
}

add_action('plugins_loaded', 'wop_cf_init');
