<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Shortcode_Finder
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Currently, this plugin doesn't store any data in the database,
// so there's nothing to clean up on uninstall.
// This file exists for completeness and future extensibility.

// If we add options in the future, clean them up here
// Example:
// delete_option('shortcode_finder_options');
// delete_site_option('shortcode_finder_options');

// If we add custom database tables in the future, remove them here
// global $wpdb;
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}shortcode_finder_table");

// Clear any transients we might have set
// delete_transient('shortcode_finder_transient');