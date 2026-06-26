<?php
/**
 * PHPUnit bootstrap file for WP Social Media Automation
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load WordPress test framework
if (file_exists(__DIR__ . '/../vendor/wp-mock/wp-mock/WP_Mock.php')) {
    require_once __DIR__ . '/../vendor/wp-mock/wp-mock/WP_Mock.php';
}

// Initialize WP_Mock
WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();

// Load plugin files
require_once __DIR__ . '/../social-media-automation.php';

// Set up plugin constants for testing
define('WPSMA_PLUGIN_DIR', __DIR__ . '/../');
define('WPSMA_PLUGIN_URL', 'http://test.example.com/wp-content/plugins/wp-social-media-automation/');
define('WPSMA_PLUGIN_BASE', 'wp-social-media-automation/wp-social-media-automation.php');