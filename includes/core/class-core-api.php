<?php
/**
 * API class for WP Social Media Automation plugin
 */

namespace WPSMA\Core;

class API {
    /**
     * API endpoints
     *
     * @var array
     */
    private $endpoints = [];

    /**
     * Initialize API
     */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('wp_ajax_wpsma_api_request', [$this, 'handle_api_request']);
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('wpsma/v1', '/schedule-post/', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_schedule_post'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route('wpsma/v1', '/scheduled-posts/', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_scheduled_posts'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route('wpsma/v1', '/analytics/', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_analytics'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route('wpsma/v1', '/platforms/', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_platforms'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
    }

    /**
     * Check API permission
     *
     * @return bool Permission status
     */
    public function check_permission() {
        return current_user_can('manage_options');
    }

    /**
     * REST endpoint: Schedule a post
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response object
     */
    public function rest_schedule_post($request) {
        $parameters = $request->get_params();
        $post_id = isset($parameters['post_id']) ? intval($parameters['post_id']) : 0;
        $platforms = isset($parameters['platforms']) ? $parameters['platforms'] : [];
        $scheduled_date = isset($parameters['scheduled_date']) ? sanitize_text_field($parameters['scheduled_date']) : '';

        if (!$post_id || empty($platforms) || !$scheduled_date) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Invalid parameters', 'wp-social-media-automation'),
            ], 400);
        }

        $result = Scheduler::schedule_post($post_id, $platforms, $scheduled_date);

        if ($result) {
            return new \WP_REST_Response([
                'success' => true,
                'scheduled_post_id' => $result,
                'message' => __('Post scheduled successfully', 'wp-social-media-automation'),
            ], 200);
        }

        return new \WP_REST_Response([
            'success' => false,
            'message' => __('Failed to schedule post', 'wp-social-media-automation'),
        ], 500);
    }

    /**
     * REST endpoint: Get scheduled posts
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response object
     */
    public function rest_get_scheduled_posts($request) {
        global $wpdb;

        $parameters = $request->get_params();
        $post_id = isset($parameters['post_id']) ? intval($parameters['post_id']) : 0;
        $status = isset($parameters['status']) ? sanitize_text_field($parameters['status']) : '';

        $query = "SELECT * FROM {$wpdb->prefix}wpsma_scheduled_posts";
        $where = [];
        $params = [];

        if ($post_id) {
            $where[] = 'post_id = %d';
            $params[] = $post_id;
        }

        if ($status) {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= ' ORDER BY scheduled_date DESC';

        if ($params) {
            $posts = $wpdb->get_results($wpdb->prepare($query, $params));
        } else {
            $posts = $wpdb->get_results($query);
        }

        return new \WP_REST_Response([
            'success' => true,
            'posts' => $posts,
        ], 200);
    }

    /**
     * REST endpoint: Get analytics
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response object
     */
    public function rest_get_analytics($request) {
        $parameters = $request->get_params();
        $post_id = isset($parameters['post_id']) ? intval($parameters['post_id']) : 0;

        if ($post_id) {
            $analytics = Analytics::get_post_analytics($post_id);
        } else {
            $analytics = Analytics::get_analytics_summary();
        }

        return new \WP_REST_Response([
            'success' => true,
            'analytics' => $analytics,
        ], 200);
    }

    /**
     * REST endpoint: Get available platforms
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response object
     */
    public function rest_get_platforms($request) {
        $platforms = new Platforms();
        $available_platforms = $platforms->get_available_platforms();

        return new \WP_REST_Response([
            'success' => true,
            'platforms' => $available_platforms,
        ], 200);
    }

    /**
     * Handle AJAX API requests
     */
    public function handle_api_request() {
        check_ajax_referer('wpsma_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized', 'wp-social-media-automation')], 403);
        }

        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : [];

        switch ($action) {
            case 'schedule_post':
                $result = $this->ajax_schedule_post($data);
                break;
            case 'get_scheduled_posts':
                $result = $this->ajax_get_scheduled_posts($data);
                break;
            default:
                wp_send_json_error(['message' => __('Invalid action', 'wp-social-media-automation')], 400);
                return;
        }

        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(['message' => __('Request failed', 'wp-social-media-automation')], 500);
        }
    }

    /**
     * AJAX: Schedule a post
     *
     * @param array $data Request data
     * @return array Result data
     */
    private function ajax_schedule_post($data) {
        $post_id = isset($data['post_id']) ? intval($data['post_id']) : 0;
        $platforms = isset($data['platforms']) ? $data['platforms'] : [];
        $scheduled_date = isset($data['scheduled_date']) ? sanitize_text_field($data['scheduled_date']) : '';

        if (!$post_id || empty($platforms) || !$scheduled_date) {
            return ['message' => __('Invalid parameters', 'wp-social-media-automation')];
        }

        $result = Scheduler::schedule_post($post_id, $platforms, $scheduled_date);

        if ($result) {
            return [
                'scheduled_post_id' => $result,
                'message' => __('Post scheduled successfully', 'wp-social-media-automation'),
            ];
        }

        return ['message' => __('Failed to schedule post', 'wp-social-media-automation')];
    }

    /**
     * AJAX: Get scheduled posts
     *
     * @param array $data Request data
     * @return array Scheduled posts
     */
    private function ajax_get_scheduled_posts($data) {
        global $wpdb;

        $post_id = isset($data['post_id']) ? intval($data['post_id']) : 0;
        $status = isset($data['status']) ? sanitize_text_field($data['status']) : '';

        $query = "SELECT * FROM {$wpdb->prefix}wpsma_scheduled_posts";
        $where = [];
        $params = [];

        if ($post_id) {
            $where[] = 'post_id = %d';
            $params[] = $post_id;
        }

        if ($status) {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= ' ORDER BY scheduled_date DESC';

        if ($params) {
            $posts = $wpdb->get_results($wpdb->prepare($query, $params));
        } else {
            $posts = $wpdb->get_results($query);
        }

        return ['posts' => $posts];
    }
}