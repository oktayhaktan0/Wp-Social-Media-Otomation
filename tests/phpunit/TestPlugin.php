<?php
/**
 * Test plugin main functionality
 */

class TestPlugin extends WP_Mock\Tools\TestCase {
    /**
     * Test plugin initialization
     */
    public function testPluginInitialization() {
        WP_Mock::userFunction('plugin_dir_path', [
            'args' => [__FILE__],
            'return' => __DIR__ . '/../..' . '/',
        ]);

        WP_Mock::userFunction('plugin_dir_url', [
            'args' => [__FILE__],
            'return' => 'http://test.example.com/wp-content/plugins/wp-social-media-automation/',
        ]);

        WP_Mock::userFunction('plugin_basename', [
            'args' => [__FILE__],
            'return' => 'wp-social-media-automation/wp-social-media-automation.php',
        ]);

        // Test that plugin constants are defined
        $this->assertTrue(defined('WPSMA_VERSION'));
        $this->assertTrue(defined('WPSMA_PLUGIN_DIR'));
        $this->assertTrue(defined('WPSMA_PLUGIN_URL'));
        $this->assertTrue(defined('WPSMA_PLUGIN_BASE'));

        // Test that autoloader is registered
        $this->assertTrue(class_exists('WPSMA\Autoloader'));
        $this->assertTrue(class_exists('WPSMA\Plugin'));
    }

    /**
     * Test plugin activation
     */
    public function testPluginActivation() {
        global $wpdb;

        // Mock database operations
        WP_Mock::userFunction('dbDelta', [
            'args' => [WP_Mock\Functions::type('string')],
            'return' => true,
        ]);

        WP_Mock::userFunction('get_option', [
            'args' => ['wpsma_settings', []],
            'return' => false,
        ]);

        WP_Mock::userFunction('update_option', [
            'args' => ['wpsma_settings', WP_Mock\Functions::type('array')],
            'return' => true,
        ]);

        WP_Mock::userFunction('wp_next_scheduled', [
            'args' => ['wpsma_check_scheduled_posts'],
            'return' => false,
        ]);

        WP_Mock::userFunction('wp_schedule_event', [
            'args' => [WP_Mock\Functions::type('int'), WP_Mock\Functions::type('string'), WP_Mock\Functions::type('string')],
            'return' => true,
        ]);

        // Call activation method
        $result = WPSMA\Plugin::activate();

        // Assert that activation completed
        $this->assertNull($result);
    }

    /**
     * Test plugin deactivation
     */
    public function testPluginDeactivation() {
        WP_Mock::userFunction('wp_clear_scheduled_hook', [
            'args' => ['wpsma_check_scheduled_posts'],
            'return' => true,
        ]);

        WP_Mock::userFunction('wp_clear_scheduled_hook', [
            'args' => ['wpsma_update_analytics'],
            'return' => true,
        ]);

        // Call deactivation method
        $result = WPSMA\Plugin::deactivate();

        // Assert that deactivation completed
        $this->assertNull($result);
    }
}