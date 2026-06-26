<?php
/**
 * Bitly integration for WP Social Media Automation plugin
 */

namespace WPSMA\Integrations;

class Bitly {
    /**
     * Bitly API base URL
     *
     * @var string
     */
    private $api_url = 'https://api-ssl.bitly.com/v4/';

    /**
     * Initialize Bitly integration
     */
    public function __construct() {
        add_filter('wpsma_shortened_url', [$this, 'shorten_url'], 10, 2);
    }

    /**
     * Shorten URL using Bitly
     *
     * @param string $url Original URL
     * @param string $platform Platform name
     * @return string Shortened URL or original if failed
     */
    public function shorten_url($url, $platform) {
        $settings = get_option('wpsma_settings', []);

        if (!isset($settings['bitly_access_token']) || empty($settings['bitly_access_token'])) {
            return $url;
        }

        if (!isset($settings['url_shortener']) || $settings['url_shortener'] !== 'bitly') {
            return $url;
        }

        $access_token = $settings['bitly_access_token'];
        $response = $this->make_api_request('shorten', [
            'long_url' => $url,
        ], $access_token);

        if (isset($response['link'])) {
            return $response['link'];
        }

        return $url;
    }

    /**
     * Make API request to Bitly
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param string $access_token Access token
     * @return array API response
     */
    private function make_api_request($endpoint, $data, $access_token) {
        $url = $this->api_url . $endpoint;

        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
            'timeout' => 30,
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return [];
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return is_array($body) ? $body : [];
    }

    /**
     * Get Bitly settings fields
     *
     * @return array Settings fields
     */
    public static function get_settings_fields() {
        return [
            'bitly_access_token' => [
                'label' => __('Bitly Access Token', 'wp-social-media-automation'),
                'type' => 'password',
                'description' => __('Enter your Bitly API access token', 'wp-social-media-automation'),
            ],
        ];
    }

    /**
     * Validate Bitly API credentials
     *
     * @param string $access_token Access token
     * @return bool Validation status
     */
    public function validate_credentials($access_token) {
        $response = $this->make_api_request('user', [], $access_token);

        return !empty($response) && isset($response['name']);
    }

    /**
     * Get Bitly click statistics
     *
     * @param string $bitly_link Bitly link
     * @return array Statistics data
     */
    public function get_click_statistics($bitly_link) {
        $settings = get_option('wpsma_settings', []);

        if (!isset($settings['bitly_access_token'])) {
            return [];
        }

        $access_token = $settings['bitly_access_token'];
        $link_parts = parse_url($bitly_link);
        $link_path = isset($link_parts['path']) ? $link_parts['path'] : '';
        $link = ltrim($link_path, '/');

        $response = $this->make_api_request('bitlinks/' . $link . '/clicks', [], $access_token);

        return is_array($response) ? $response : [];
    }
}