<?php
/**
 * Admin class for WP Social Media Automation plugin
 */

namespace WPSMA\Admin;

class Admin {
    /**
     * Initialize admin functionality
     */
    public function __construct() {
        add_action('admin_init', [$this, 'init_admin']);
        add_action('admin_menu', [$this, 'setup_admin_menu'], 20);
    }

    /**
     * Initialize admin hooks
     */
    public function init_admin() {
        // Register settings
        register_setting('wpsma_settings_group', 'wpsma_settings');

        // Add admin notices
        add_action('admin_notices', [$this, 'display_admin_notices']);
    }

    /**
     * Setup admin menu
     */
    public function setup_admin_menu() {
        add_menu_page(
            __('Social Media Automation', 'wp-social-media-automation'),
            __('Social Media', 'wp-social-media-automation'),
            'manage_options',
            'wpsma-dashboard',
            [$this, 'render_dashboard_page'],
            'dashicons-share',
            6
        );

        add_submenu_page(
            'wpsma-dashboard',
            __('Settings', 'wp-social-media-automation'),
            __('Settings', 'wp-social-media-automation'),
            'manage_options',
            'wpsma-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'wpsma-dashboard',
            __('Analytics', 'wp-social-media-automation'),
            __('Analytics', 'wp-social-media-automation'),
            'manage_options',
            'wpsma-analytics',
            [$this, 'render_analytics_page']
        );

        add_submenu_page(
            'wpsma-dashboard',
            __('Scheduled Posts', 'wp-social-media-automation'),
            __('Scheduled Posts', 'wp-social-media-automation'),
            'manage_options',
            'wpsma-scheduled-posts',
            [$this, 'render_scheduled_posts_page']
        );
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard_page() {
        echo '<div class="wrap">';
        echo '<div id="wpsma-admin-root"></div>';
        echo '</div>';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Social Media Automation Settings', 'wp-social-media-automation') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('wpsma_settings_group');
        do_settings_sections('wpsma_settings_group');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Social Media Analytics', 'wp-social-media-automation') . '</h1>';
        echo '<div id="wpsma-analytics-dashboard"></div>';
        echo '</div>';
    }

    /**
     * Render scheduled posts page
     */
    public function render_scheduled_posts_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Scheduled Posts', 'wp-social-media-automation') . '</h1>';
        echo '<div id="wpsma-scheduled-posts"></div>';
        echo '</div>';
    }

    /**
     * Display admin notices
     */
    public function display_admin_notices() {
        $settings = get_option('wpsma_settings', []);

        if (empty($settings)) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>' . esc_html__('WP Social Media Automation: Please configure plugin settings to get started.', 'wp-social-media-automation') . '</p>';
            echo '</div>';
        }
    }
}