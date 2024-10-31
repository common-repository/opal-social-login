<?php
/**
 * @package  opal-social-login
 * @category Plugins
 * @author   WpOpal
 * Plugin Name: Opal Social Login
 * Plugin URI: http://www.wpopal.com/
 * Version: 1.0.0
 * Description: Opal Social Login displays social login buttons for Facebook, Google and Twitter.
 * http://www.gnu.org/licenses/gpl-3.0.html
 */
defined( 'ABSPATH' ) || exit();

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @package Opal Social Login
 * @since 1.0.0
 */
function opal_social_login_load_textdomain() {
	load_plugin_textdomain( 'opal-social-login', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'opal_social_login_load_textdomain');

class OpalSocialLogin {
	private $version = '1.0.0';

	public static $instance;

	/**
	 * instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * constructor
	 */
	public function __construct() {
		$this->set_constants();
		$this->includes();
		$this->init_hooks();

		add_action('init', array($this, 'load') );
	}

	/**
	 * set all constants
	 */
	private function set_constants() {
		$this->define( 'WSL_PLUGIN_FILE', __FILE__ );
		$this->define( 'WSL_VERSION', $this->version );
		$this->define( 'WSL_PLUGIN_URI', plugin_dir_url( WSL_PLUGIN_FILE ) );
		$this->define( 'WSL_PLUGIN_DIR', plugin_dir_path( WSL_PLUGIN_FILE ) );
		$this->define( 'WSL_PLUGIN_ASSET_URI', trailingslashit( WSL_PLUGIN_URI . 'assets' ) );
		$this->define( 'WSL_PLUGIN_INC_DIR', trailingslashit( WSL_PLUGIN_DIR . 'includes' ) );
		$this->define( 'WSL_PLUGIN_TEMPLATE_DIR', trailingslashit( WSL_PLUGIN_DIR . 'templates' ) );

		$this->define( 'MENU_SLUG', 'opal-social-login');
	}

	/**
	 * set define
	 *
	 * @param string name
	 * @param string | boolean | anythings
	 * @since 1.0.0
	 */
	private function define( $name = '', $value = '' ) {
		defined( $name ) || define( $name, $value );
	}

	/**
	 * Load and init
	 */
	public function load() {

		
	}

	/**
	 * include all required files
	 */
	private function includes() {
		
		$this->_include( 'includes/hook-functions.php' );

		$this->_include('includes/class-social-login.php');

		$this->_include('includes/class-social-login-frontend.php');
        WSL_Social_Login_Frontend();
        WSL_Social_Login();

		if ( ! is_admin() ) {
			$this->_include( 'includes/class-style.php' );
		}

		//-- include admin setting
		$this->_include('includes/admin/class-admin.php');

	}

	/**
	 * Include list of collection files
	 *
	 * @var array $files
	 */
	public function include_files ( $files ) {
		foreach ( $files as $file ) {
			$this->_include( $file );
		}
	}

	/**
	 * include single file
	 */
	private function _include( $file = '' ) {
		$file = WSL_PLUGIN_DIR . $file;
		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}

	/**
	 * init main plugin hooks
	 */
	private function init_hooks() {
		// trigger init hooks
		
	}

}

function Opal_Social_Login() {
	return OpalSocialLogin::instance();
}
Opal_Social_Login();
