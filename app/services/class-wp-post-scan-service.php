<?php
/**
 * WP Post Scan Service
 * 
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\App\Services;

// Abort if called directly.
defined( 'WPINC' ) || die;


use WPMUDEV\PluginTest\Base;


class WPPostScanService extends Base {

    public function init() {
        add_action('wpmudev_daily_scan_event', array($this, 'wpmudev_scan_and_update_last_scan'));
    }

    public static function wpmudev_schedule_scan($post_types = ['post', 'page']) {
        if (!wp_next_scheduled('wpmudev_daily_scan_event')) {
            wp_schedule_event(time(), 'daily', 'wpmudev_daily_scan_event', [$post_types]);
        }
    }

    public static function wpmudev_scan_and_update_last_scan($post_types = ['post', 'page']) {
        // Ensure $post_types is an array
        if (!is_array($post_types)) {
            $post_types = [$post_types];
        }
    
        // Query for all public posts of the specified types
        $query = new \WP_Query([
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => -1, // Get all posts
        ]);
    
        // Current timestamp
        $current_time = current_time('mysql');
    
        // Loop through the posts and update the meta
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                update_post_meta($post_id, 'wpmudev_test_last_scan', $current_time);
            }
        }
    
        // Reset post data
        wp_reset_postdata();
    }
    
}

?>
