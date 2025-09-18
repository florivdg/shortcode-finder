=== Shortcode Finder ===
Contributors: florivdg
Donate link: https://flori.dev
Tags: shortcode, search, admin, tools, content management
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Search and find all pages, posts, and custom post types where specific shortcodes are used. Essential admin tool for tracking shortcode usage.

== Description ==

**Shortcode Finder** is a powerful WordPress admin tool that helps you locate where shortcodes are being used across your entire website. Whether you're cleaning up old shortcodes, debugging issues, or planning content migration, this plugin makes it easy to find every instance of any shortcode.

= Key Features =

* **Comprehensive Search**: Search through all published posts, pages, and custom post types
* **Flexible Input**: Enter shortcode names with or without brackets (e.g., "gallery" or "[gallery]")
* **Instant Results**: Fast AJAX-powered search with no page reloads
* **Detailed Results**: See post titles, post types, and direct edit/view links
* **Admin-Only Access**: Secure tool available only to administrators
* **Clean Interface**: Simple, intuitive design that matches WordPress admin styling
* **No Frontend Impact**: Zero impact on your site's frontend performance

= Use Cases =

* Find all pages using a specific plugin's shortcode before deactivating it
* Locate content that needs updating when changing shortcode parameters
* Audit your site's shortcode usage for optimization
* Debug shortcode-related issues by finding all instances
* Plan content migration when switching themes or plugins

= How It Works =

1. Navigate to **Shortcode Finder** in your WordPress admin menu
2. Enter the shortcode name you want to search for
3. Click "Search" to instantly find all uses
4. View results with direct links to edit each page or post

The plugin searches for all shortcode variations including:
* Simple shortcodes: `[shortcode]`
* Shortcodes with attributes: `[shortcode attr="value"]`
* Self-closing shortcodes: `[shortcode /]`
* Enclosing shortcodes: `[shortcode]content[/shortcode]`

== Installation ==

= From WordPress Admin =

1. Go to **Plugins > Add New**
2. Search for "Shortcode Finder"
3. Click "Install Now" and then "Activate"
4. Access the plugin via **Shortcode Finder** in the admin menu

= Manual Installation =

1. Download the plugin ZIP file
2. Upload to `/wp-content/plugins/shortcode-finder/`
3. Activate through the **Plugins** menu in WordPress
4. Access the plugin via **Shortcode Finder** in the admin menu

== Frequently Asked Questions ==

= Does this plugin affect my site's frontend performance? =

No. Shortcode Finder only runs in the WordPress admin area and has zero impact on your site's frontend performance.

= What user roles can access this plugin? =

Only administrators (users with the `manage_options` capability) can access and use Shortcode Finder.

= Can I search for multiple shortcodes at once? =

Currently, the plugin searches for one shortcode at a time. This ensures accurate results and optimal performance.

= Does it search in draft or private posts? =

By default, Shortcode Finder only searches in published posts to show you active content. Draft and private posts are excluded.

= Can I search for shortcodes with specific attributes? =

Enter just the shortcode name (e.g., "gallery") to find all instances regardless of their attributes.

= Is the search case-sensitive? =

No, the search is case-insensitive. Searching for "Gallery" will find [gallery], [GALLERY], and [Gallery].

= Does it work with custom post types? =

Yes! Shortcode Finder searches through all public post types registered in your WordPress installation.

= What happens if I deactivate the plugin? =

Simply deactivating removes the admin menu item. No data is stored in the database, so deactivation is clean and safe.

== Screenshots ==

1. Main search interface - clean and simple design
2. Search results showing posts containing the shortcode
3. Detailed results table with edit and view actions
4. Admin menu location for easy access

== Changelog ==

= 1.0.0 =
* Initial release
* Core search functionality for finding shortcodes
* AJAX-powered interface for instant results
* Support for all public post types
* Clean, intuitive admin interface
* Comprehensive shortcode pattern matching

== Upgrade Notice ==

= 1.0.0 =
Initial release of Shortcode Finder. Install to start tracking shortcode usage across your WordPress site.

== Privacy Policy ==

Shortcode Finder does not:
* Collect any personal data
* Store any data in the database
* Make any external API calls
* Use cookies or tracking
* Modify any content

The plugin only reads existing post content to search for shortcodes when you perform a search.

== Support ==

For support, feature requests, or bug reports, please visit:
* [GitHub Repository](https://github.com/florivdg/shortcode-finder)

== Credits ==

Developed by Florian van der Galiën
* Website: https://flori.dev
* WordPress: [@florivdg](https://profiles.wordpress.org/florivdg/)