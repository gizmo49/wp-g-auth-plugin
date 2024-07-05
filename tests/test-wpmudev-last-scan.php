<?php

// Require the main plugin file.
require_once dirname(__FILE__) . '/../wpmudev-plugin-test.php';

class WPMUDEV_Last_Scan_Tests extends WP_UnitTestCase {

    // Set up the testing environment.
    public function setUp() {
        parent::setUp();
        // Create a few sample posts and pages for testing.
        $this->post_id_1 = $this->factory->post->create(['post_type' => 'post', 'post_status' => 'publish']);
        $this->post_id_2 = $this->factory->post->create(['post_type' => 'post', 'post_status' => 'publish']);
        $this->page_id_1 = $this->factory->post->create(['post_type' => 'page', 'post_status' => 'publish']);
        $this->page_id_2 = $this->factory->post->create(['post_type' => 'page', 'post_status' => 'publish']);
    }

    // Test the scanning and updating of posts and pages.
    public function test_scan_and_update_last_scan() {
        // Ensure the post_meta doesn't exist before the scan.
        $this->assertFalse(get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertFalse(get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));
        $this->assertFalse(get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertFalse(get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));

        // Run the scan function.
        wpmudev_scan_and_update_last_scan(['post', 'page']);

        // Verify the post_meta is updated with the current timestamp.
        $current_time = current_time('mysql');
        $this->assertEquals($current_time, get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));
    }

    // Test the scanning with only 'post' post type.
    public function test_scan_and_update_last_scan_posts_only() {
        // Run the scan function with only 'post' post type.
        wpmudev_scan_and_update_last_scan(['post']);

        // Verify the post_meta is updated for posts.
        $current_time = current_time('mysql');
        $this->assertEquals($current_time, get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));

        // Verify the post_meta is not updated for pages.
        $this->assertFalse(get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertFalse(get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));
    }

    // Test the scanning with only 'page' post type.
    public function test_scan_and_update_last_scan_pages_only() {
        // Run the scan function with only 'page' post type.
        wpmudev_scan_and_update_last_scan(['page']);

        // Verify the post_meta is updated for pages.
        $current_time = current_time('mysql');
        $this->assertEquals($current_time, get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));

        // Verify the post_meta is not updated for posts.
        $this->assertFalse(get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertFalse(get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));
    }

    // Test scheduling of the daily scan.
    public function test_schedule_scan() {
        // Clear any existing scheduled events.
        wp_clear_scheduled_hook('wpmudev_daily_scan_event');

        // Schedule the scan.
        wpmudev_schedule_scan(['post', 'page']);

        // Check if the event is scheduled.
        $timestamp = wp_next_scheduled('wpmudev_daily_scan_event');
        $this->assertNotFalse($timestamp);

        // Run the scheduled event manually.
        do_action('wpmudev_daily_scan_event', ['post', 'page']);

        // Verify the post_meta is updated with the current timestamp.
        $current_time = current_time('mysql');
        $this->assertEquals($current_time, get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));
    }

    // Test WP-CLI command for scanning posts.
    public function test_wp_cli_command() {
        if (!class_exists('WP_CLI')) {
            $this->markTestSkipped('WP-CLI not available.');
            return;
        }

        // Mock the WP_CLI::runcommand function.
        WP_CLI::runcommand('wpmudev last-scan --post_types=post,page');

        // Verify the post_meta is updated with the current timestamp.
        $current_time = current_time('mysql');
        $this->assertEquals($current_time, get_post_meta($this->post_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->post_id_2, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_1, 'wpmudev_test_last_scan', true));
        $this->assertEquals($current_time, get_post_meta($this->page_id_2, 'wpmudev_test_last_scan', true));
    }
}
?>
