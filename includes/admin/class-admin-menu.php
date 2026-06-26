<?php
/**
 * Menu class for WP Social Media Automation plugin
 */

namespace WPSMA\Admin;

class Menu {
    /**
     * Initialize menu functionality
     */
    public function __construct() {
        add_action('admin_bar_menu', [$this, 'add_admin_bar_items'], 100);
        add_action('post_submitbox_misc_actions', [$this, 'add_post_meta_box']);
    }

    /**
     * Add items to admin bar
     *
     * @param WP_Admin_Bar $admin_bar Admin bar object
     */
    public function add_admin_bar_items($admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }

        $admin_bar->add_menu([
            'id' => 'wpsma-menu',
            'title' => __('Social Media', 'wp-social-media-automation'),
            'href' => admin_url('admin.php?page=wpsma-dashboard'),
            'meta' => [
                'title' => __('Social Media Automation', 'wp-social-media-automation'),
            ],
        ]);

        $admin_bar->add_menu([
            'id' => 'wpsma-schedule-post',
            'parent' => 'wpsma-menu',
            'title' => __('Schedule Post', 'wp-social-media-automation'),
            'href' => '#',
            'meta' => [
                'title' => __('Schedule Current Post', 'wp-social-media-automation'),
                'onclick' => 'event.preventDefault(); wpsmaSchedulePost();',
            ],
        ]);

        $admin_bar->add_menu([
            'id' => 'wpsma-analytics',
            'parent' => 'wpsma-menu',
            'title' => __('Analytics', 'wp-social-media-automation'),
            'href' => admin_url('admin.php?page=wpsma-analytics'),
            'meta' => [
                'title' => __('View Analytics', 'wp-social-media-automation'),
            ],
        ]);
    }

    /**
     * Add meta box to post editor
     */
    public function add_post_meta_box() {
        global $post;

        if (!$post) {
            return;
        }

        $settings = get_option('wpsma_settings', []);
        $enabled_platforms = isset($settings['enabled_platforms']) ? $settings['enabled_platforms'] : [];

        if (empty($enabled_platforms)) {
            return;
        }

        echo '<div class="misc-pub-section" id="wpsma-post-scheduling">';
        echo '<span>' . __('Social Media:', 'wp-social-media-automation') . '</span> ';
        echo '<a href="#" class="button" onclick="event.preventDefault(); wpsmaSchedulePost(' . esc_attr($post->ID) . ');">' . __('Schedule Post', 'wp-social-media-automation') . '</a>';
        echo '</div>';
    }

    /**
     * Enqueue admin bar scripts
     */
    public function enqueue_admin_bar_scripts() {
        wp_add_inline_script(
            'wpsma-admin',
            'function wpsmaSchedulePost(postId) {
                if (!postId) {
                    postId = jQuery("#post_ID").val();
                }
                alert("Schedule post " + postId + " functionality coming soon!");
            }'
        );
    }
}