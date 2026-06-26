<?php
/**
 * Main plugin class
 */

namespace WPSMA;

class Plugin {
    /**
     * Plugin version
     *
     * @var string
     */
    protected $version = WPSMA_VERSION;

    /**
     * Initialize plugin
     */
    public function run() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        Autoloader::register();

        // Admin classes
        new Admin\Admin();
        new Admin\Settings();
        new Admin\Menu();

        // Core classes
        new Core\Scheduler();
        new Core\Analytics();
        new Core\Platforms();
        new Core\API();

        // Integrations
        new Integrations\Bitly();
        new Integrations\Rebrandly();
    }

    /**
     * Set plugin locale
     */
    private function set_locale() {
        load_plugin_textdomain(
            'wp-social-media-automation',
            false,
            dirname(WPSMA_PLUGIN_BASE) . '/languages/'
        );
    }

    /**
     * Define admin hooks
     */
    private function define_admin_hooks() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Define public hooks
     */
    private function define_public_hooks() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            'wpsma-admin',
            WPSMA_PLUGIN_URL . 'assets/css/admin.css',
            [],
            $this->version,
            'all'
        );

        wp_enqueue_script(
            'wpsma-admin',
            WPSMA_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-util'],
            $this->version,
            true
        );

        wp_localize_script(
            'wpsma-admin',
            'wpsmaAdmin',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpsma_ajax_nonce')
            ]
        );
    }

    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'wpsma-public',
            WPSMA_PLUGIN_URL . 'assets/css/public.css',
            [],
            $this->version,
            'all'
        );

        wp_enqueue_script(
            'wpsma-public',
            WPSMA_PLUGIN_URL . 'assets/js/public.js',
            ['jquery'],
            $this->version,
            true
        );
    }

    /**
     * Plugin activation
     */
    public static function activate() {
        // Create database tables
        self::create_tables();

        // Set default options
        self::set_default_options();

        // Schedule cron jobs
        self::schedule_cron_jobs();
    }

    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Clear scheduled cron jobs
        self::clear_cron_jobs();
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Scheduled posts table
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpsma_scheduled_posts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            platforms varchar(255) NOT NULL,
            scheduled_date datetime NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY scheduled_date (scheduled_date)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Analytics table
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpsma_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            platform varchar(50) NOT NULL,
            post_date datetime NOT NULL,
            impressions bigint(20) DEFAULT 0,
            engagements bigint(20) DEFAULT 0,
            clicks bigint(20) DEFAULT 0,
            shares bigint(20) DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY platform (platform),
            KEY post_date (post_date)
        ) $charset_collate;";

        dbDelta($sql);
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        $options = get_option('wpsma_settings', []);

        if (empty($options)) {
            $defaults = [
                'enabled_platforms' => ['twitter', 'facebook'],
                'default_schedule' => '09:00',
                'url_shortener' => 'none',
                'analytics_enabled' => true,
                'ai_suggestions_enabled' => false
            ];

            update_option('wpsma_settings', $defaults);
        }
    }

    /**
     * Schedule cron jobs
     */
    private static function schedule_cron_jobs() {
        if (!wp_next_scheduled('wpsma_check_scheduled_posts')) {
            wp_schedule_event(time(), 'hourly', 'wpsma_check_scheduled_posts');
        }

        if (!wp_next_scheduled('wpsma_update_analytics')) {
            wp_schedule_event(time(), 'daily', 'wpsma_update_analytics');
        }
    }

    /**
     * Clear cron jobs
     */
    private static function clear_cron_jobs() {
        wp_clear_scheduled_hook('wpsma_check_scheduled_posts');
        wp_clear_scheduled_hook('wpsma_update_analytics');
    }
}