<?php
/**
 * Plugin Name:       WPMU DEV Plugin Test
 * Description:       A plugin focused on testing coding skills.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.1.0
 * Author:            Chika Benjamin.
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpmudev-plugin-test
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Plugin version.
if ( ! defined( 'WPMUDEV_PLUGINTEST_VERSION' ) ) {
	define( 'WPMUDEV_PLUGINTEST_VERSION', '1.0.0' );
}

// Define WPMUDEV_PLUGINTEST_PLUGIN_FILE.
if ( ! defined( 'WPMUDEV_PLUGINTEST_PLUGIN_FILE' ) ) {
	define( 'WPMUDEV_PLUGINTEST_PLUGIN_FILE', __FILE__ );
}

// Plugin directory.
if ( ! defined( 'WPMUDEV_PLUGINTEST_DIR' ) ) {
	define( 'WPMUDEV_PLUGINTEST_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin url.
if ( ! defined( 'WPMUDEV_PLUGINTEST_URL' ) ) {
	define( 'WPMUDEV_PLUGINTEST_URL', plugin_dir_url( __FILE__ ) );
}

// Assets url.
if ( ! defined( 'WPMUDEV_PLUGINTEST_ASSETS_URL' ) ) {
	define( 'WPMUDEV_PLUGINTEST_ASSETS_URL', WPMUDEV_PLUGINTEST_URL . 'assets' );
}

// Shared UI Version.
if ( ! defined( 'WPMUDEV_PLUGINTEST_SUI_VERSION' ) ) {
	define( 'WPMUDEV_PLUGINTEST_SUI_VERSION', '2.12.23' );
}


/**
 * WPMUDEV_PluginTest class.
 */
class WPMUDEV_PluginTest {

	/**
	 * Holds the class instance.
	 *
	 * @var WPMUDEV_PluginTest $instance
	 */
	private static $instance = null;

	/**
	 * Return an instance of the class
	 *
	 * Return an instance of the WPMUDEV_PluginTest Class.
	 *
	 * @return WPMUDEV_PluginTest class instance.
	 * @since 1.0.0
	 *
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class initializer.
	 */
	public function load() {
		WPMUDEV\PluginTest\Loader::instance();
	}
}

// Init the plugin and load the plugin instance for the first time.
add_action(
	'plugins_loaded',
	function () {
		WPMUDEV_PluginTest::get_instance()->load();
	}
);



// Exists WP cli 
if (!(defined('WP_CLI') && WP_CLI)) {
    return;
}


// WP CLI Config
class WPMUDEV_Last_Scan_CLI {
	/**
	 * Scan posts and update the last scan timestamp.
	 *
	 * ## OPTIONS
	 *
	 * [--post_types=<post_types>]
	 * : Comma separated list of post types to scan. Default is 'post,page'.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpmudev last-scan --post_types=post,page
	 *     wp wpmudev last-scan --post_types=post
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function last_scan($args, $assoc_args) {
		$post_types = isset($assoc_args['post_types']) ? explode(',', $assoc_args['post_types']) : ['post', 'page'];
		WPMUDEV\PluginTest\App\Services\WPPostScanService::wpmudev_scan_and_update_last_scan($post_types);
		WP_CLI::success('Posts scanned and updated successfully!');
	}
}


WP_CLI::add_command( 'wpmudev last-scan', array('WPMUDEV_Last_Scan_CLI', 'last_scan'));