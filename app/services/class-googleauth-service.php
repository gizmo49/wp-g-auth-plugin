<?php
/**
 * Google Auth Service.
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
use WPMUDEV\PluginTest\Core\Google_Auth\Auth;
use Google\Service\Oauth2 as OAuth;
 
class GoogleServices extends Auth {
	
	public function __construct() {}
 
	public function init() {
		add_shortcode('google-login', array($this, 'vm_login_with_google'));
	}

	function vm_login_with_google(){
		$login_url = $this->get_auth_url();
		$btnContent = '
			<style>
				.googleBtn{
					display: table;
					margin: 0 auto;
					background: #4285F4;
					padding: 15px;
					border-radius: 3px;
					color: #fff;
				}
			</style>
		';
		if(!is_user_logged_in()){
			return $btnContent . '<a class="googleBtn" href="'.$login_url.'">Login With Google</a>';
		} else{
			$current_user = wp_get_current_user();
			return $btnContent . '<div class="googleBtn">Hi, ' . $current_user->first_name . '! - <a href="/wp-login.php?action=logout">Log Out</a></div>';
		}
	}

// add ajax action
public function vm_login_google($req){
	$gCode = $req['code'];
	$gClient = $this->set_up();

	// echo $gCode;
	// echo "gCode\n";

	// checking for google code
	if (!empty($gCode)) {
		$token = $gClient->fetchAccessTokenWithAuthCode($gCode);

		// // var_dump($token);
		// // echo "Token:\n";

		if(!isset($token["error"])){
			// get data from google
			$oAuth = new OAuth($gClient);
			$userData = $oAuth->userinfo_v2_me->get();
		}

	

		// check if user email already registered
		if(!email_exists($userData['email'])){
			// generate password
			$bytes = openssl_random_pseudo_bytes(2);
			$password = md5(bin2hex($bytes));
			$user_login = $userData['id'];


			$new_user_id = wp_insert_user(array(
				'user_login'		=> $user_login,
				'user_pass'	 		=> $password,
				'user_email'		=> $userData['email'],
				'first_name'		=> $userData['givenName'],
				'last_name'			=> $userData['familyName'],
				'user_registered'	=> date('Y-m-d H:i:s'),
				'role'				=> 'subscriber'
				)
			);
			if($new_user_id) {
				// send an email to the admin
				wp_new_user_notification($new_user_id);
				
				// log the new user in
				do_action('wp_login', $user_login, $userData['email']);
				wp_set_current_user($new_user_id);
				wp_set_auth_cookie($new_user_id, true);
				
				// send the newly created user to the home page after login
				wp_redirect(home_url()); exit;
			}
		}else{
			//if user already registered than we are just loggin in the user
			$user = get_user_by( 'email', $userData['email'] );
			do_action('wp_login', $user->user_login, $user->user_email);
			wp_set_current_user($user->ID);
			wp_set_auth_cookie($user->ID, true);
			wp_redirect(home_url()); exit;
		}


	}else{
		wp_redirect(home_url());
		exit();
	}
}


}
