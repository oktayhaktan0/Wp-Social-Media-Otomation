<?php
/**
 * Rebrandly integration for WP Social Media Automation plugin
 */

namespace WPSMA\Integrations;

class Rebrandly {
    /**
     * Rebrandly API base URL
     *
     * @var string
     */
    private $api_url = 'https://api.rebrandly.com/v1/';

    /**
     * Initialize Rebrandly integration
     */
    public function __construct() {
        add_filter('wpsma_shortened_url', [$this, 'shorten_url'], 10, 2);
    }

    /**
     * Shorten URL using Rebrandly
     *
     * @param string $url Original URL
     * @param string $platform Platform name
     * @return string Shortened URL or original if failed
     */
    public function shorten_url($url, $platform) {
        $settings = get_option('wpsma_settings', []);

        if (!isset($settings['rebrandly_api_key']) || empty($settings['rebrandly_api_key'])) {
            return $url;
        }

        if (!isset($settings['url_shortener']) || $settings['url_shortener'] !== 'rebrandly') {
            return $url;
        }

        $api_key = $settings['rebrandly_api_key'];
        $response = $this->make_api_request('links', [
            'destination' => $url,
        ], $api_key);

        if (isset($response['shortUrl'])) {
            return $response['shortUrl'];
        }

        return $url;
    }

    /**
     * Make API request to Rebrandly
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param string $api_key API key
     * @return array API response
     */
    private function make_api_request($endpoint, $data, $api_key) {
        $url = $this->api_url . $endpoint;

        $args = [
            'headers' => [
                'apikey' => $api_key,
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
     * Get Rebrandly settings fields
     *
     * @return array Settings fields
     */
    public static function get_settings_fields() {
        return [
            'rebrandly_api_key' => [
                'label' => __('Rebrandly API Key', 'wp-social-media-automation'),
                'type' => 'password',
                'description' => __('Enter your Rebrandly API key', 'wp-social-media-automation'),
            ],
        ];
    }

    /**
     * Validate Rebrandly API credentials
     *
     * @param string $api_key API key
     * @return bool Validation status
     */
    public function validate_credentials($api_key) {
        $response = $this->make_api_request('account', [], $api_key);

        return !empty($response) && isset($response['id']);
    }

    /**
     * Get Rebrandly link statistics
     *
     * @param string $rebrandly_link Rebrandly link
     * @return array Statistics data
     */
    public function get_link_statistics($rebrandly_link) {
        $settings = get_option('wpsma_settings', []);

        if (!isset($settings['rebrandly_api_key'])) {
            return [];
        }

        $api_key = $settings['rebrandly_api_key'];
        $link_id = basename(parse_url($rebrandly_link, PHP_URL_PATH));

        $response = $this->make_api_request('links/' . $link_id, [], $api_key);

        return is_array($response) ? $response : [];
    }
}