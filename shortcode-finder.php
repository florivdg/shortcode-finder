<?php
/**
 * Plugin Name: Shortcode Finder
 * Plugin URI: https://wordpress.org/plugins/shortcode-finder/
 * Description: Search and find all pages, posts, and custom post types where specific shortcodes are used. Essential tool for WordPress administrators to track shortcode usage across their site.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Author: Florian van der Galiën
 * Author URI: https://flori.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: shortcode-finder
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SHORTCODE_FINDER_VERSION', '1.0.0');
define('SHORTCODE_FINDER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SHORTCODE_FINDER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class ShortcodeFinder {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the plugin
     */
    private function init() {
        // Load plugin text domain for translations
        add_action('init', array($this, 'load_textdomain'));

        // Load required files
        $this->load_dependencies();

        // Hook into WordPress
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Initialize admin class
        if (is_admin()) {
            ShortcodeFinder_Admin::get_instance();
        }
    }

    /**
     * Load plugin text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'shortcode-finder',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once SHORTCODE_FINDER_PLUGIN_DIR . 'includes/class-shortcode-finder-admin.php';
        require_once SHORTCODE_FINDER_PLUGIN_DIR . 'includes/class-shortcode-finder-search.php';
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Shortcode Finder', 'shortcode-finder'),
            __('Shortcode Finder', 'shortcode-finder'),
            'manage_options',
            'shortcode-finder',
            array('ShortcodeFinder_Admin', 'render_admin_page'),
            'dashicons-search',
            100
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin page
        if ('toplevel_page_shortcode-finder' !== $hook) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'shortcode-finder-admin',
            SHORTCODE_FINDER_PLUGIN_URL . 'assets/admin.css',
            array(),
            SHORTCODE_FINDER_VERSION
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'shortcode-finder-admin',
            SHORTCODE_FINDER_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            SHORTCODE_FINDER_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('shortcode-finder-admin', 'shortcode_finder_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('shortcode_finder_nonce')
        ));
    }
}

// Initialize the plugin
add_action('plugins_loaded', array('ShortcodeFinder', 'get_instance'));