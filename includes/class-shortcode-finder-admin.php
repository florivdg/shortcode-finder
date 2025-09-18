<?php
/**
 * Admin interface class for Shortcode Finder
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class ShortcodeFinder_Admin {

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
        // Register AJAX handlers
        add_action('wp_ajax_shortcode_finder_search', array($this, 'ajax_search_shortcode'));
    }

    /**
     * Render the admin page
     */
    public static function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="shortcode-finder-container">
                <div class="search-section">
                    <h2><?php esc_html_e('Search for Shortcode', 'shortcode-finder'); ?></h2>
                    <p class="description">
                        <?php esc_html_e('Enter a shortcode name (with or without brackets) to find all published pages and posts where it is used.', 'shortcode-finder'); ?>
                    </p>

                    <div class="search-form">
                        <input type="text"
                               id="shortcode-input"
                               placeholder="<?php esc_attr_e('e.g., gallery or [gallery]', 'shortcode-finder'); ?>"
                               class="regular-text" />
                        <button type="button"
                                id="search-button"
                                class="button button-primary">
                            <?php esc_html_e('Search', 'shortcode-finder'); ?>
                        </button>
                        <span class="spinner"></span>
                    </div>

                    <div id="search-message" class="notice" style="display: none;"></div>
                </div>

                <div class="results-section" style="display: none;">
                    <h2><?php esc_html_e('Search Results', 'shortcode-finder'); ?></h2>
                    <div id="results-container">
                        <!-- Results will be loaded here via AJAX -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX handler for shortcode search
     */
    public function ajax_search_shortcode() {
        // Verify nonce
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'shortcode_finder_nonce' ) ) {
            wp_die(esc_html__('Security check failed', 'shortcode-finder'));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions', 'shortcode-finder'));
        }

        // Get and sanitize shortcode
        $shortcode = isset( $_POST['shortcode'] ) ? sanitize_text_field( wp_unslash( $_POST['shortcode'] ) ) : '';

        if (empty($shortcode)) {
            wp_send_json_error(array('message' => __('Please enter a shortcode to search for.', 'shortcode-finder')));
        }

        // Perform search
        $search = new ShortcodeFinder_Search();
        $results = $search->find_shortcode($shortcode);

        if (empty($results)) {
            $no_results_message = sprintf(
                /* translators: %s: searched shortcode. */
                __('No posts found containing the shortcode "%s".', 'shortcode-finder'),
                esc_html($shortcode)
            );

            wp_send_json_success(array(
                'message' => $no_results_message,
                'html' => '',
                'count' => 0
            ));
        }

        // Build results HTML
        ob_start();
        ?>
        <div class="results-summary">
            <p><?php
            /* translators: 1: number of posts found, 2: searched shortcode. */
            $results_summary_template = _n(
                'Found <strong>%1$d post</strong> containing the shortcode "%2$s".',
                'Found <strong>%1$d posts</strong> containing the shortcode "%2$s".',
                count($results),
                'shortcode-finder'
            );

            printf(
                wp_kses(
                    $results_summary_template,
                    array('strong' => array())
                ),
                count($results),
                esc_html($shortcode)
            ); ?></p>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Title', 'shortcode-finder'); ?></th>
                    <th><?php esc_html_e('Post Type', 'shortcode-finder'); ?></th>
                    <th><?php esc_html_e('Status', 'shortcode-finder'); ?></th>
                    <th><?php esc_html_e('Actions', 'shortcode-finder'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $post) : ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" target="_blank">
                                    <?php echo esc_html($post->post_title ?: __('(no title)', 'shortcode-finder')); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <?php
                            $post_type_obj = get_post_type_object($post->post_type);
                            echo esc_html($post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type);
                            ?>
                        </td>
                        <td>
                            <?php echo esc_html(ucfirst($post->post_status)); ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>"
                               class="button button-small"
                               target="_blank">
                                <?php esc_html_e('Edit', 'shortcode-finder'); ?>
                            </a>
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"
                               class="button button-small"
                               target="_blank">
                                <?php esc_html_e('View', 'shortcode-finder'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'count' => count($results)
        ));
    }
}
