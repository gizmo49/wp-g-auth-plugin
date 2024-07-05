<?php
/**
 * Google Auth block.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\App\Admin_Pages;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV\PluginTest\Base;
 
class PostsMaintenance extends Base {

	/**
	 * The page title.
	 *
	 * @var string
	 */
	private $page_title;

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	private $page_slug = 'wpmudev-last-scan';


	/**
	 * Initializes the page.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function init() {
		$this->page_title     = __( 'Posts Maintenance', 'wpmudev-plugin-test' );
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
	}

	public function register_admin_page() {
		$page = add_menu_page(
			'Posts Maintenance',
			$this->page_title,
			'manage_options',
			$this->page_slug,
			array( $this, 'wpmudev_last_scan_page' ),
			'dashicons-code-standards',
			7
		);

	}



	/**
	 * The admin page callback method.
	 *
	 * @return void
	 */
	public function wpmudev_last_scan_page() {
		if (!current_user_can('manage_options')) {
			return;
		}
	
		if (isset($_POST['wpmudev_scan_posts'])) {
			$post_types = isset($_POST['post_types']) ? $_POST['post_types'] : ['post', 'page'];
			// wpmudev_schedule_scan($post_types);
			echo '<div class="notice notice-success is-dismissible"><p>Posts scan scheduled successfully!</p></div>';
		}
	
		?>
		<div class="wrap">
			<h1><?php echo $this->page_title ?></h1>
			<form method="post" action="">
				<h2>Select Post Types to Scan</h2>
				<label>
					<input type="checkbox" name="post_types[]" value="post" checked> Posts
				</label><br>
				<label>
					<input type="checkbox" name="post_types[]" value="page" checked> Pages
				</label><br>
				<input type="hidden" name="wpmudev_scan_posts" value="1">
				<?php submit_button('Schedule Scan'); ?>
			</form>
		</div>
		<?php
	}



}
