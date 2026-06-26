<?php
/**
 * Scheduler class for WP Social Media Automation plugin
 */

namespace WPSMA\Core;

class Scheduler {
    /**
     * Initialize scheduler
     */
    public function __construct() {
        add_action('wpsma_check_scheduled_posts', [$this, 'process_scheduled_posts']);
        add_action('wp_ajax_wpsma_schedule_post', [$this, 'ajax_schedule_post']);
        add_action('wp_ajax_wpsma_delete_scheduled_post', [$this, 'ajax_delete_scheduled_post']);
    }

    /**
     * Schedule a post for social media sharing
     *
     * @param int $post_id WordPress post ID
     * @param array $platforms Platforms to share on
     * @param string $scheduled_date Date/time to share
     * @return int|false Scheduled post ID or false on failure
     */
    public static function schedule_post($post_id, $platforms, $scheduled_date) {
        global $wpdb;

        $platforms = implode(',', $platforms);

        $result = $wpdb->insert(
            $wpdb->prefix . 'wpsma_scheduled_posts',
            [
                'post_id' => $post_id,
                'platforms' => $platforms,
                'scheduled_date' => $scheduled_date,
                'status' => 'pending',
            ],
            ['%d', '%s', '%s', '%s']
        );

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Process scheduled posts (cron job)
     */
    public function process_scheduled_posts() {
        global $wpdb;

        $current_time = current_time('mysql');
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wpsma_scheduled_posts "
                . "WHERE status = 'pending' AND scheduled_date <= %s "
                . "ORDER BY scheduled_date ASC",
                $current_time
            )
        );

        foreach ($posts as $post) {
            $this->publish_to_social_media($post);
        }
    }

    /**
     * Publish post to social media platforms
     *
     * @param object $scheduled_post Scheduled post data
     */
    private function publish_to_social_media($scheduled_post) {
        $post = get_post($scheduled_post->post_id);

        if (!$post) {
            $this->mark_as_failed($scheduled_post->id, 'Post not found');
            return;
        }

        $platforms = explode(',', $scheduled_post->platforms);
        $settings = get_option('wpsma_settings', []);

        foreach ($platforms as $platform) {
            if (!in_array($platform, isset($settings['enabled_platforms']) ? $settings['enabled_platforms'] : [])) {
                continue;
            }

            $result = $this->publish_to_platform($post, $platform);

            if ($result) {
                $this->mark_as_published($scheduled_post->id, $platform);
            } else {
                $this->mark_as_failed($scheduled_post->id,