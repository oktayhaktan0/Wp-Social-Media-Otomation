<?php
/**
 * Autoloader class for WP Social Media Automation plugin
 */

namespace WPSMA;

class Autoloader {
    /**
     * Register autoloader
     */
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Autoload classes
     *
     * @param string $class Class name
     */
    public static function autoload($class) {
        // Only autoload classes in our namespace
        if (0 !== strpos($class, __NAMESPACE__ . '\')) {
            return;
        }

        // Remove namespace from class name
        $class_name = str_replace(__NAMESPACE__ . '\', '', $class);

        // Convert namespace to path
        $file = WPSMA_PLUGIN_DIR . 'includes/class-' . str_replace('\', '-', strtolower($class_name)) . '.php';

        // Load the file if it exists
        if (file_exists($file)) {
            require $file;
        }
    }
}