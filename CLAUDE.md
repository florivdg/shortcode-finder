# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Plugin Overview

This is a WordPress plugin called "Shortcode Finder" that allows administrators to search for shortcode usage across all published content in a WordPress site. It operates exclusively in the WordPress admin backend.

## Architecture

The plugin follows WordPress coding standards and uses a singleton pattern for initialization:

- **Main Entry Point**: `shortcode-finder.php` - Registers the plugin, defines constants, and bootstraps the admin interface
- **Admin Interface**: `includes/class-shortcode-finder-admin.php` - Handles the admin page rendering and AJAX endpoint for searches
- **Search Engine**: `includes/class-shortcode-finder-search.php` - Contains the core search logic using direct database queries to find shortcodes in post content
- **Frontend Assets**: `assets/admin.css` and `assets/admin.js` - Provides the AJAX-powered search interface

## Key Implementation Details

### Shortcode Detection
The search functionality (`ShortcodeFinder_Search::find_shortcode()`) searches for multiple shortcode patterns:
- `[shortcode]` - Simple shortcodes
- `[shortcode attr="value"]` - Shortcodes with attributes
- `[shortcode]content[/shortcode]` - Enclosing shortcodes
- Handles variations with spaces around the shortcode name

### Security
- All AJAX requests verify WordPress nonces (`shortcode_finder_nonce`)
- Capability checks require `manage_options` permission
- Direct file access is prevented with `ABSPATH` checks

### Database Queries
The plugin uses direct `$wpdb` queries with proper escaping to search post_content across all public post types. Only published posts are included in results.

## Development Commands

Since this is a WordPress plugin, there are no build or test commands. Development involves:
- Activating the plugin through WordPress admin
- Testing functionality in the WordPress backend at menu item "Shortcode Finder"
- Debugging via WordPress debug logs when `WP_DEBUG` is enabled

## WordPress Environment

The plugin expects to be installed in: `wp-content/plugins/shortcode-finder/`

Required WordPress hooks:
- `plugins_loaded` - Plugin initialization
- `admin_menu` - Menu registration
- `admin_enqueue_scripts` - Asset loading
- `wp_ajax_shortcode_finder_search` - AJAX handler