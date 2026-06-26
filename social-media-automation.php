<?php
/*
Plugin Name: WP Social Media Automation
Plugin URI: https://github.com/oktayhaktan0/Wp-Social-Media-Otomation
Description: Advanced social media automation and analytics for WordPress
Version: 1.0.0
Author: Oktay Haktan
Author URI: https://haktanoktay.com
License: GPL-2.0+
Text Domain: wp-social-media-automation
Domain Path: /languages
*/

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('WPSMA_VERSION', '1.0.0');
define('WPSMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPSMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPSMA_PLUGIN_BASE', plugin_basename(__FILE__));

// Include files
require_once WPSMA_PLUGIN_DIR . 'includes/class-autoloader.php';
require_once WPSMA_PLUGIN_DIR . 'includes/class-plugin.php';

// Initialize plugin
function wpsma_init() {
    $plugin = new WPSMA\Plugin();
    $plugin->run();
}
add_action('plugins_loaded', 'wpsma_init');

// Activation and deactivation hooks
register_activation_hook(__FILE__, ['WPSMA\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['WPSMA\Plugin', 'deactivate']);