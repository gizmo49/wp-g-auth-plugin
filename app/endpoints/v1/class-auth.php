<?php
/**
 * Google Auth Shortcode.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\Endpoints\V1;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV\PluginTest\Endpoint;
use WPMUDEV\PluginTest\App\Services\GoogleServices;


use WP_REST_Server;

class Auth extends Endpoint {
	/**
	 * API endpoint for the current endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @var string $endpoint
	 */
	protected $endpoint = 'auth/auth-url';
	protected $confirmEndpoint = 'auth/confirm';


	/**
	 * Register the routes for handling auth functionality.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function register_routes() {
		// TODO
		// Add a new Route to logout.


		// Route to get auth url.
		register_rest_route(
			$this->get_namespace(),
			$this->get_endpoint(),
			array(
				array(
					'methods' => 'POST',
					'callback' => [ $this, 'save_credentials' ],
					'args'    => array(
						'client_id'     => array(
							'required'    => true,
							'description' => __( 'The client ID from Google API project.', 'wpmudev-plugin-test' ),
							'type'        => 'string',
						),
						'client_secret' => array(
							'required'    => true,
							'description' => __( 'The client secret from Google API project.', 'wpmudev-plugin-test' ),
							'type'        => 'string',
						),
					),
					'permission_callback' => [ $this, 'get_settings_permission' ]
				),
				array(
					'methods' => 'GET',
					'callback' => [ $this, 'get_credentials' ],
					'permission_callback' => [ $this, 'get_settings_permission' ]
				)
			)
		);

		register_rest_route(
			$this->get_namespace(),
			$this->confirmEndpoint,
			array(
				array(
					'methods' => 'GET',
					'callback' => array(new GoogleServices,'vm_login_google'),
					'args'    => array(
						'code'     => array(
							'required'    => true,
							'description' => __( 'The code from Google API response.', 'wpmudev-plugin-test' ),
							'type'        => 'string',
						),
					),
				)
			)
		);
	}


	/**
	 * Save the client id and secret.
	 *
	 *
	 * @since 1.0.0
	 */
	public function save_credentials($req) {

		$settings = array();

        $client_id = sanitize_text_field( $req['client_id'] );
        $client_secret = sanitize_text_field( $req['client_secret'] );

		$settings = [
            'client_id' => $client_id,
            'client_secret'  => $client_secret,
        ];
			
        update_option('wpmudev_plugin_test_settings', $settings);

        return $this->get_response( "Saved Successfully" );
	}

	public function get_credentials() {
		$response = get_option('wpmudev_plugin_test_settings');
        return $this->get_response( $response );
	}

	function get_settings_permission() {
		return current_user_can( 'manage_options' );
	}
}
