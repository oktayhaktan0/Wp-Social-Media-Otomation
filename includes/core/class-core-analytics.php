<?php
/**
 * Analytics class for WP Social Media Automation plugin
 */

namespace WPSMA\Core;

class Analytics {
    /**
     * Initialize analytics
     */
    public function __construct() {
        add_action('wpsma_update_analytics', [$this, 'update_analytics_data']);
        add_action('wp_ajax_wpsma_get_analytics', [$this, 'ajax_get_analytics']);
    }

    /**
     * Get analytics for a specific post
     *
     * @param int $post_id Post ID
     * @return array Analytics data
     */
    public static function get_post_analytics($post_id) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wpsma_analytics "
                . "WHERE post_id = %d "
                . "ORDER BY post_date DESC",
                $post_id
            )
        );

        $analytics = [];
        foreach ($results as $result) {
            if (!isset($analytics[$result->platform])) {
                $analytics[$result->platform] = [
                    'impressions' => 0,
                    'engagements' => 0,
                    'clicks' => 0,
                    'shares' => 0,
                    'dates' => [],
                ];
            }

            $analytics[$result->platform]['impressions'] += $result->impressions;
            $analytics[$result->platform]['engagements'] += $result->engagements;
            $analytics[$result->platform]['clicks'] += $result->clicks;
            $analytics[$result->platform]['shares'] += $result->shares;
            $analytics[$result->platform]['dates'][] = $result->post_date;
        }

        return $analytics;
    }

    /**
     * Update analytics data (cron job)
     */
    public function update_analytics_data() {
        $scheduled_posts = $this->get_published_posts();

        foreach ($scheduled_posts as $post) {
            $this->fetch_platform_analytics($post);
        }
    }

    /**
     * Get published posts
     *
     * @return array Published posts
     */
    private function get_published_posts() {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT DISTINCT post_id FROM {$wpdb->prefix}wpsma_scheduled_posts "
            . "WHERE status = 'published' "
            . "AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }

    /**
     * Fetch analytics from social media platforms
     *
     * @param object $post Published post
     */
    private function fetch_platform_analytics($post) {
        $platforms = explode(',', $post->platforms);
        $post_data = get_post($post->post_id);

        if (!$post_data) {
            return;
        }

        foreach ($platforms as $platform) {
            $analytics = $this->get_platform_specific_analytics($post_data, $platform);
            $this->store_analytics_data($post->post_id, $platform, $analytics);
        }
    }

    /**
     * Get platform specific analytics
     *
     * @param WP_Post $post Post object
     * @param string $platform Platform name
     * @return array Analytics data
     */
    private function get_platform_specific_analytics($post, $platform) {
        // In a real implementation, this would connect to each platform's API
        // For now, return mock data
        return [
            'impressions' => rand(100, 1000),
            'engagements' => rand(10, 100),
            'clicks' => rand(5, 50),
            'shares' => rand(1, 20),
        ];
    }

    /**
     * Store analytics data in database
     *
     * @param int $post_id Post ID
     * @param string $platform Platform name
     * @param array $data Analytics data
     */
    private function store_analytics_data($post_id, $platform, $data) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpsma_analytics',
            [
                'post_id' => $post_id,
                'platform' => $platform,
                'post_date' => current_time('mysql'),
                'impressions' => $data['impressions'],
                'engagements' => $data['engagements'],
                'clicks' => $data['clicks'],
                'shares' => $data['shares'],
            ],
            ['%d', '%s', '%s', '%d', '%d', '%d', '%d']
        );
    }

    /**
     * Get overall analytics summary
     *
     * @return array Summary data
     */
    public static function get_analytics_summary() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT platform, SUM(impressions) as total_impressions,
                    SUM(engagements) as total_engagements,
                    SUM(clicks) as total_clicks,
                    SUM(shares) as total_shares
             FROM {$wpdb->prefix}wpsma_analytics
             GROUP BY platform"
        );

        $summary = [
            'total_impressions' => 0,
            'total_engagements' => 0,
            'total_clicks' => 0,
            'total_shares' => 0,
            'by_platform' => [],
        ];

        foreach ($results as $result) {
            $summary['total_impressions'] += $result->total_impressions;
            $summary['total_engagements'] += $result->total_engagements;
            $summary['total_clicks'] += $result->total_clicks;
            $summary['total_shares'] += $result->total_shares;

            $summary['by_platform'][$result->platform] = [
                'impressions' => $result->total_impressions,
                'engagements' => $result->total_engagements,
                'clicks' => $result->total_clicks,
                'shares' => $result->total_shares,
            ];
        }

        return $summary;
    }

    /**
     * AJAX handler for getting analytics
     */
    public function ajax_get_analytics() {
        check_ajax_referer('wpsma_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized', 'wp-social-media-automation')], 403);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $analytics = $this->get_post_analytics($post_id);

        wp_send_json_success(['analytics' => $analytics]);
    }
}