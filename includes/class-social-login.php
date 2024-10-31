<?php

defined( 'ABSPATH' ) || exit();
use Hybridauth\Exception\Exception;
use Hybridauth\Hybridauth;
use Hybridauth\Storage\Session;
session_start();

if( ! class_exists( 'WSL_Social_Login' ) ){

	global $wsl_options;

	$wsl_options = wsl_get_settings();

	/**
	 * Opal Social Login main class
	 *
	 * @since 1.0.0
	 */
	class WSL_Social_Login {

		/**
		 * Single instance of the class
		 *
		 * @var \WSL_Social_Login
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Array with accessible variables
		 */
		protected $_data = array();

		/**
		 * Array with config parameters
		 */
		protected $config = array();

		/**
		 * HybridAuth Object
		 */
		protected $hybridauth;

		/**
		 * Returns single instance of the class
		 *
		 * @return \WSL_Social_Login
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/* plugin */

			require_once(WSL_PLUGIN_INC_DIR.'hybridauth/autoload.php');

			$this->_set_config();
			$this->_set_social_list();
			$this->_set_social_list_enabled();

			add_action( 'init', array( $this, 'get_login_request' ) );
			add_action( 'wp_logout', array( $this, 'logout' ), 11 );

			add_shortcode( 'opal-social-login', array( $this, 'social_login_shortcode' ) );
		}

		/**
		 * Return a $property defined in this class
		 */
		public function __get( $property ){
			if ( isset( $this->_data[$property] ) ) {
				return $this->_data[$property];
			}
		}

		/**
		 * Set the configuration array for Hybrid Class
		 */
		private function _set_config() {
			
			$this->config = include( WSL_PLUGIN_INC_DIR . '/options/config.php' );
		}

		/**
		 * Set an array with the social list
		 */
		private function _set_social_list() {
			$social_list = include( WSL_PLUGIN_INC_DIR . '/options/socials.php' );

            $this->_data['social_list'] = $social_list;
		}


		/**
		 * Main function to login with social providers
		 */
		public function get_login_request(){

            if (isset($_GET['wsl_social'])) {
                $social = $_REQUEST['wsl_social'];
                $this->config = apply_filters('wsl_alter_config', $this->config, $_REQUEST['wsl_social']);
            }
			try {
				$hybridauth = new Hybridauth($this->config);
			    $storage = new Session();
			    //
			    // Event 1: User clicked SIGN-IN link
			    //
			    if (isset($_GET['wsl_social'])) {

			    	
			        // Validate provider exists in the $config
			        if (in_array($_GET['wsl_social'], $hybridauth->getProviders())) {
			            // Store the provider for the callback event
			            $storage->set('wsl_social', $_GET['wsl_social']);
			        }
			    }
			    //
			    // Event 2: Provider returns via CALLBACK
			    //
			    if ($provider = $storage->get('wsl_social')) {

			        $hybridauth->authenticate($provider);
			        $storage->delete('wsl_social');
			        //$storage->set('wsl_social', null);
			        // Retrieve the provider record
			        $adapter = $hybridauth->getAdapter($provider);
			        $user_profile = $adapter->getUserProfile();


			        $registration_check       = $this->verify_user( $social, $user_profile->identifier );
					$hyb_email                = sanitize_email($user_profile->email);
					$hyb_user_login           = sanitize_user($user_profile->displayName, true);
					//$hyb_user_avatar          = $user_profile->photoURL;
				
					if ( is_user_logged_in() ) {
						
						$current_user = wp_get_current_user();
						$current_customer_id = $current_user->ID;
						//link account
						add_user_meta( $current_customer_id, $social.'_login_id', $user_profile->identifier, true );
						add_user_meta( $current_customer_id, $social.'_login_data', (array) $user_profile, true );
						$storage->delete('wsl_social');
						wp_redirect( $this->get_redirect_to() );
						exit;
					}

					if ( $current_customer_id = $this->verify_email_exists( $hyb_email ) ){
						
						//link account
						add_user_meta( $current_customer_id, $social.'_login_id', $user_profile->identifier, true );
						add_user_meta( $current_customer_id, $social.'_login_data', (array) $user_profile, true );
						$this->add_user_meta( $current_customer_id, $user_profile, $hyb_email );
						wp_set_auth_cookie( $current_customer_id, true );
						$storage->delete('wsl_social');
						wp_redirect( $this->get_redirect_to() );
						exit;

					}else {

						$wsl_user_login          = $this->get_username( $hyb_user_login, $hyb_email );
						$wsl_user_email          = $this->get_email( $hyb_email );


						$wsl_user_login_validate = validate_username ( $wsl_user_login );
						$wsl_user_email_validate = filter_var( $wsl_user_email, FILTER_VALIDATE_EMAIL ) ;


						if( empty( $wsl_user_login ) ) $wsl_user_login_validate = false;
						if( empty( $wsl_user_email ) ) $wsl_user_email_validate = false;



						$show_form        = false;
						$show_email       = false;
						$show_username    = false;
						$show_form_errors = array();

	                    if(  ! $wsl_user_email && ! is_user_logged_in()  ){
	                        $show_form          = true;
	                        $show_email         = true;
	                        $show_form_errors[] = __('Add your email address', 'opal-social-login') ;
	                    }

	                    if(  $wsl_user_email && ! $wsl_user_email_validate ){
	                        $show_form          = true;
	                        $show_email         = true;
	                        $show_form_errors[] = __('Your email address is not valid!', 'opal-social-login') ;
	                    }

	                    if ( $wsl_user_email_validate && $this->verify_email_exists( $wsl_user_email ) && ! is_user_logged_in() ) {
	                        $show_form          = true;
	                        $show_email         = true;
	                        $show_form_errors[] = __( 'This email already exists', 'opal-social-login' );
	                    }

						if( ! $wsl_user_login || ! $wsl_user_login_validate ){
							$show_form          = true;
							$show_username      = true;
							$show_form_errors[] = __('Username is not valid!', 'opal-social-login') ;
						}

						
						if ( !is_user_logged_in() ) {
							if ( username_exists( $wsl_user_login ) ) {
								$wsl_user_login = $this->get_username( $wsl_user_login, $wsl_user_email );
							}
							$current_customer_id = $this->add_user( $wsl_user_login, $wsl_user_email, $user_profile );

							$storage->delete('wsl_social');
						}

						//link account
						add_user_meta( $current_customer_id, $social.'_login_id', $user_profile->identifier, true );
						add_user_meta( $current_customer_id, $social.'_login_data', (array) $user_profile, true );

						wp_set_auth_cookie( $current_customer_id, true );
						

						wp_redirect( $this->get_redirect_to() );

						$storage->delete('wsl_social');

						exit;
					}
			    }

			} catch (Exception $e) {
			    error_log( $e->getMessage());
			    echo $e->getMessage();
			    $storage->delete('wsl_social');
			}
				
				
				
			
		}

		/**
		 * Return the username of user
		 */
		function get_username( $hyb_user_login, $hyb_user_email ) {
			
			$wsl_user_login = sanitize_user( $hyb_user_login, true );
			$wsl_user_login = trim( str_replace( array( ' ', '.' ), '_', $wsl_user_login ) );
			$wsl_user_login = trim( str_replace( '__', '_', $wsl_user_login ) );
			
			return apply_filters('social_login_get_username', $wsl_user_login, $hyb_user_login, $hyb_user_email);
		}

		/**
		 * Return the email of user
		 */
		function get_email( $hyb_user_email ) {

			$wsl_user_email = $hyb_user_email;

			return $wsl_user_email;

		}

		/**
		 * Check if the customer has a connection with the provider
		 */
		public function verify_user( $social, $identifier ) {
			global $wpdb;

			$query = $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key = "%s" AND  meta_value= "%s"', $social . '_login_id', $identifier );

			$user_id = $wpdb->get_var( $query );
			if ( $user_id ) {
				return $user_id;
			} else {
				return false;
			}

		}

		/**
		 * Check if exists an user with an email like $user_email
		 */
		public function verify_email_exists( $user_email ) {
			global $wpdb;
			$query     = $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->users . ' WHERE user_email = "%s"', $user_email );
			$user_id   = $wpdb->get_var( $query );
			if ( $user_id ) {
				return $user_id;
			} else {
				return false;
			}
		}

		/**
		 * Add a new user
		 */
		public function add_user( $username, $user_email, $user_info ){

			$password = wp_generate_password();
			$args = array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $user_email,
				'role'       => apply_filters('wsl_new_user_role','customer')
			);
			$customer_id = wp_insert_user( $args );

			$this->add_user_meta( $customer_id, $user_info, $user_email );

			do_action( 'user_register', $customer_id );
			//do_action( 'wsl_locate_template', $customer_id, $args, $password );

			return $customer_id;
		}

		/**
		 * Add meta to user from provider's user info
		 */
		public function add_user_meta( $user_id, $user_info, $user_email ){

			if( get_user_meta( $user_id, 'billing_email', true ) == '' ){
				update_user_meta( $user_id, 'billing_email', $user_email );
			}

			if ( isset( $user_info->description ) && $user_info->description == '' ) {
				update_user_meta( $user_id, 'description', $user_info->description);
			}

			if ( isset( $user_info->firstName ) ) {
				if( get_user_meta( $user_id, 'first_name', true ) == '' ){
					update_user_meta( $user_id, 'first_name', $user_info->firstName );
				}

				if( get_user_meta( $user_id, 'billing_first_name', true ) == '' ){
					update_user_meta( $user_id, 'billing_first_name', $user_info->firstName );
				}

				if( get_user_meta( $user_id, 'shipping_first_name', true ) == '' ){
					update_user_meta( $user_id, 'shipping_first_name', $user_info->firstName );
				}

			}
			if ( isset( $user_info->lastName ) ) {
				if( get_user_meta( $user_id, 'last_name', true ) == '' ){
					update_user_meta( $user_id, 'last_name', $user_info->lastName );
				}

				if( get_user_meta( $user_id, 'billing_last_name', true ) == '' ){
					update_user_meta( $user_id, 'billing_last_name', $user_info->lastName );
				}

				if( get_user_meta( $user_id, 'shipping_last_name', true ) == '' ){
					update_user_meta( $user_id, 'shipping_last_name', $user_info->lastName);
				}

			}
			if ( isset( $user_info->phone ) && get_user_meta( $user_id, 'billing_phone', true ) == ''  ) {
				update_user_meta( $user_id, 'billing_phone', $user_info->phone);
			}
			if ( isset( $user_info->address ) ) {

				if( get_user_meta( $user_id, 'billing_address_1', true ) == '' ){
					update_user_meta( $user_id, 'billing_address_1', $user_info->address );
				}

				if( get_user_meta( $user_id, 'shipping_address_1', true ) == '' ){
					update_user_meta( $user_id, 'shipping_address_1', $user_info->address);
				}
			}
			if ( isset( $user_info->country ) ) {

				if( get_user_meta( $user_id, 'billing_country', true ) == '' ){
					update_user_meta( $user_id, 'billing_country', $user_info->country );
				}

				if( get_user_meta( $user_id, 'shipping_country', true ) == '' ){
					update_user_meta( $user_id, 'shipping_country', $user_info->country);
				}

			}
			if ( isset( $user_info->region ) ) {

				if( get_user_meta( $user_id, 'billing_state', true ) == '' ){
					update_user_meta( $user_id, 'billing_state', $user_info->region );
				}

				if( get_user_meta( $user_id, 'shipping_state', true ) == '' ){
					update_user_meta( $user_id, 'shipping_state', $user_info->region);
				}

			}
			if ( isset( $user_info->city ) ) {

				if( get_user_meta( $user_id, 'billing_city', true ) == '' ){
					update_user_meta( $user_id, 'billing_city', $user_info->city );
				}

				if( get_user_meta( $user_id, 'shipping_city', true ) == '' ){
					update_user_meta( $user_id, 'shipping_city', $user_info->city );
				}

			}
			if ( isset( $user_info->zip ) ) {

				if( get_user_meta( $user_id, 'billing_postcode', true ) == '' ){
					update_user_meta( $user_id, 'billing_postcode', $user_info->zip);
				}

				if( get_user_meta( $user_id, 'shipping_postcode', true ) == '' ){
					update_user_meta( $user_id, 'shipping_postcode', $user_info->zip );
				}

			}

			do_action( 'wsl_add_additional_user_info', $user_id, $user_info, $user_email );

		}

		/**
		 * Return the page to redirect the user
		 */
		function get_redirect_to() {

			$redirect_to = site_url();

			return apply_filters( 'wsl_redirect_to_after_login', $redirect_to );
		}

		/**
		 * Set the social providers enabled
		 */
		private function _set_social_list_enabled(){
			$enabled_social = array();
			foreach( $this->social_list as $key => $value){
				$enabled = wsl_get_option( 'wsl_' . $key . '_enable' );
				if( $enabled == '1' ) {
					$enabled_social[$key] = $value;
				}

			}
			$this->_data['enabled_social'] = $enabled_social;
		}
		/**
		 * Return the base url for the library hybrid
		 */
		public function get_base_url(  ) {
			return ( wsl_get_option('wsl_callback_url' ) == 'root'  ) ? site_url() : WSL_PLUGIN_URI . 'includes/hybridauth/';
		}


		/**
		 * Return if a provider is enabled
		 */
        public function is_enabled( $provider ) {
            $enabled_list = $this->enabled_social;
            return isset( $enabled_list[$provider] );
        }

		/**
		 * Clear the session at logout
		 */
		public function logout() {
			if ( isset( $_SESSION ) ) {
				@session_destroy();
			}
			clearstatcache();
			unset( $this->hybridauth );

		}

		/**
         * Shortcode the Social Login Buttons
         */
        public function social_login_shortcode( $atts  ){
            return WSL_Social_Login_Frontend()->wsl_social_buttons( '', true);
        }

	}

	/**
	 * Unique access to instance of WSL_Social_Login class
	 *
	 * @return \WSL_Social_Login
	 */
	function WSL_Social_Login() {
		return WSL_Social_Login::get_instance();

	}
}

