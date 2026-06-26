<?php
/**
 * Settings class for WP Social Media Automation plugin
 */

namespace WPSMA\Admin;

class Settings {
    /**
     * Initialize settings
     */
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        add_settings_section(
            'wpsma_general_settings',
            __('General Settings', 'wp-social-media-automation'),
            [$this, 'render_general_settings_section'],
            'wpsma_settings_group'
        );

        add_settings_field(
            'enabled_platforms',
            __('Enabled Platforms', 'wp-social-media-automation'),
            [$this, 'render_enabled_platforms_field'],
            'wpsma_settings_group',
            'wpsma_general_settings'
        );

        add_settings_field(
            'default_schedule',
            __('Default Posting Time', 'wp-social-media-automation'),
            [$this, 'render_default_schedule_field'],
            'wpsma_settings_group',
            'wpsma_general_settings'
        );

        add_settings_field(
            'url_shortener',
            __('URL Shortener', 'wp-social-media-automation'),
            [$this, 'render_url_shortener_field'],
            'wpsma_settings_group',
            'wpsma_general_settings'
        );

        add_settings_field(
            'analytics_enabled',
            __('Enable Analytics', 'wp-social-media-automation'),
            [$this, 'render_analytics_enabled_field'],
            'wpsma_settings_group',
            'wpsma_general_settings'
        );

        add_settings_section(
            'wpsma_api_settings',
            __('API Settings', 'wp-social-media-automation'),
            [$this, 'render_api_settings_section'],
            'wpsma_settings_group'
        );

        add_settings_field(
            'twitter_api_key',
            __('Twitter API Key', 'wp-social-media-automation'),
            [$this, 'render_twitter_api_key_field'],
            'wpsma_settings_group',
            'wpsma_api_settings'
        );

        add_settings_field(
            'facebook_api_key',
            __('Facebook API Key', 'wp-social-media-automation'),
            [$this, 'render_facebook_api_key_field'],
            'wpsma_settings_group',
            'wpsma_api_settings'
        );
    }

    /**
     * Render general settings section
     */
    public function render_general_settings_section() {
        echo '<p>' . esc_html__('Configure general plugin settings.', 'wp-social-media-automation') . '</p>';
    }

    /**
     * Render API settings section
     */
    public function render_api_settings_section() {
        echo '<p>' . esc_html__('Configure API keys for social media platforms.', 'wp-social-media-automation') . '</p>';
    }

    /**
     * Render enabled platforms field
     */
    public function render_enabled_platforms_field() {
        $settings = get_option('wpsma_settings', []);
        $platforms = isset($settings['enabled_platforms']) ? $settings['enabled_platforms'] : ['twitter', 'facebook'];

        $options = [
            'twitter' => __('Twitter', 'wp-social-media-automation'),
            'facebook' => __('Facebook', 'wp-social-media-automation'),
            'instagram' => __('Instagram', 'wp-social-media-automation'),
            'linkedin' => __('LinkedIn', 'wp-social-media-automation'),
        ];

        echo '<fieldset>';
        foreach ($options as $value => $label) {
            echo '<label>';
            echo '<input type="checkbox" name="wpsma_settings[enabled_platforms][]" value="' . esc_attr($value) . '" ' . checked(in_array($value, $platforms), true, false) . '> ';
            echo esc_html($label);
            echo '</label><br>';
        }
        echo '</fieldset>';
    }

    /**
     * Render default schedule field
     */
    public function render_default_schedule_field() {
        $settings = get_option('wpsma_settings', []);
        $default_schedule = isset($settings['default_schedule']) ? $settings['default_schedule'] : '09:00';

        echo '<input type="time" name="wpsma_settings[default_schedule]" value="' . esc_attr($default_schedule) . '" class="regular-text">';
    }

    /**
     * Render URL shortener field
     */
    public function render_url_shortener_field() {
        $settings = get_option('wpsma_settings', []);
        $url_shortener = isset($settings['url_shortener']) ? $settings['url_shortener'] : 'none';

        $options = [
            'none' => __('None', 'wp-social-media-automation'),
            'bitly' => __('Bitly', 'wp-social-media-automation'),
            'rebrandly' => __('Rebrandly', 'wp-social-media-automation'),
        ];

        echo '<select name="wpsma_settings[url_shortener]" class="regular-text">';
        foreach ($options as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($url_shortener, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Render analytics enabled field
     */
    public function render_analytics_enabled_field() {
        $settings = get_option('wpsma_settings', []);
        $analytics_enabled = isset($settings['analytics_enabled']) ? $settings['analytics_enabled'] : true;

        echo '<label>';
        echo '<input type="checkbox" name="wpsma_settings[analytics_enabled]" value="1" ' . checked($analytics_enabled, true, false) . '> ';
        echo esc_html__('Enable analytics tracking', 'wp-social-media-automation');
        echo '</label>';
    }

    /**
     * Render Twitter API key field
     */
    public function render_twitter_api_key_field() {
        $settings = get_option('wpsma_settings', []);
        $api_key = isset($settings['twitter_api_key']) ? $settings['twitter_api_key'] : '';

        echo '<input type="password" name="wpsma_settings[twitter_api_key]" value="' . esc_attr($api_key) . '" class="regular-text">';
    }

    /**
     * Render Facebook API key field
     */
    public function render_facebook_api_key_field() {
        $settings = get_option('wpsma_settings', []);
        $api_key = isset($settings['facebook_api_key']) ? $settings['facebook_api_key'] : '';

        echo '<input type="password" name="wpsma_settings[facebook_api_key]" value="' . esc_attr($api_key) . '" class="regular-text">';
    }
}