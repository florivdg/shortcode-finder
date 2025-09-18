<?php
/**
 * Search functionality class for Shortcode Finder
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class ShortcodeFinder_Search {

    /**
     * Find posts containing a specific shortcode
     *
     * @param string $shortcode The shortcode to search for
     * @return array Array of post objects
     */
    public function find_shortcode($shortcode) {
        global $wpdb;

        // Clean the shortcode (remove brackets if present)
        $shortcode = $this->clean_shortcode($shortcode);

        if (empty($shortcode)) {
            return array();
        }

        // Get all public post types
        $post_types = get_post_types(array('public' => true), 'names');

        // Build SQL query
        $post_types_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));

        // Prepare search patterns for different shortcode formats
        $search_patterns = array(
            '[' . $shortcode . ']',           // Simple shortcode
            '[' . $shortcode . ' ',           // Shortcode with attributes
            '[ ' . $shortcode . ']',          // Shortcode with space before
            '[ ' . $shortcode . ' '           // Shortcode with spaces
        );

        $where_conditions = array();
        $values = array();

        // Add post types to values array
        foreach ($post_types as $post_type) {
            $values[] = $post_type;
        }

        // Build WHERE conditions for each pattern
        foreach ($search_patterns as $pattern) {
            $where_conditions[] = "post_content LIKE %s";
            $values[] = '%' . $wpdb->esc_like($pattern) . '%';
        }

        // Build the complete query
        $query = "
            SELECT DISTINCT ID, post_title, post_type, post_status, post_content
            FROM {$wpdb->posts}
            WHERE post_type IN ($post_types_placeholder)
            AND post_status = 'publish'
            AND (" . implode(' OR ', $where_conditions) . ")
            ORDER BY post_title ASC
        ";

        // Prepare and execute query
        $prepared_query = $wpdb->prepare($query, $values);
        $results = $wpdb->get_results($prepared_query);

        // Additional filtering to ensure we have exact shortcode matches
        $filtered_results = array();
        foreach ($results as $post) {
            if ($this->verify_shortcode_exists($post->post_content, $shortcode)) {
                $filtered_results[] = $post;
            }
        }

        return $filtered_results;
    }

    /**
     * Clean shortcode name (remove brackets and trim)
     *
     * @param string $shortcode
     * @return string
     */
    private function clean_shortcode($shortcode) {
        // Remove brackets if present
        $shortcode = str_replace(array('[', ']'), '', $shortcode);

        // Trim whitespace
        $shortcode = trim($shortcode);

        // Remove any attributes if they were included
        if (strpos($shortcode, ' ') !== false) {
            $parts = explode(' ', $shortcode);
            $shortcode = $parts[0];
        }

        return $shortcode;
    }

    /**
     * Verify that a shortcode actually exists in the content
     * This helps filter out false positives
     *
     * @param string $content
     * @param string $shortcode
     * @return bool
     */
    private function verify_shortcode_exists($content, $shortcode) {
        // Check for various shortcode patterns
        $patterns = array(
            '/\[' . preg_quote($shortcode, '/') . '\]/i',                    // [shortcode]
            '/\[' . preg_quote($shortcode, '/') . '\s+[^\]]*\]/i',          // [shortcode attr="value"]
            '/\[' . preg_quote($shortcode, '/') . '\].*?\[\/' . preg_quote($shortcode, '/') . '\]/is', // [shortcode]content[/shortcode]
            '/\[' . preg_quote($shortcode, '/') . '\s+[^\]]*\].*?\[\/' . preg_quote($shortcode, '/') . '\]/is' // [shortcode attr="value"]content[/shortcode]
        );

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get shortcode usage count for a specific post
     *
     * @param int $post_id
     * @param string $shortcode
     * @return int
     */
    public function get_shortcode_count($post_id, $shortcode) {
        $post = get_post($post_id);
        if (!$post) {
            return 0;
        }

        $shortcode = $this->clean_shortcode($shortcode);
        $pattern = '/\[' . preg_quote($shortcode, '/') . '(?:\s+[^\]]*)?(?:\]|\].*?\[\/' . preg_quote($shortcode, '/') . '\])/i';

        preg_match_all($pattern, $post->post_content, $matches);

        return count($matches[0]);
    }

    /**
     * Get all unique shortcodes used in the site
     *
     * @return array
     */
    public function get_all_shortcodes() {
        global $wpdb;

        $post_types = get_post_types(array('public' => true), 'names');
        $post_types_placeholder = implode(', ', array_fill(0, count($post_types), '%s'));

        $query = $wpdb->prepare(
            "SELECT post_content
             FROM {$wpdb->posts}
             WHERE post_type IN ($post_types_placeholder)
             AND post_status = 'publish'
             AND post_content LIKE %s",
            array_merge($post_types, array('%[%]%'))
        );

        $results = $wpdb->get_results($query);

        $shortcodes = array();
        $pattern = '/\[([a-zA-Z0-9_-]+)(?:\s+[^\]]*)?(?:\]|\].*?\[\/\1\])/';

        foreach ($results as $post) {
            preg_match_all($pattern, $post->post_content, $matches);
            if (!empty($matches[1])) {
                $shortcodes = array_merge($shortcodes, $matches[1]);
            }
        }

        return array_unique($shortcodes);
    }
}