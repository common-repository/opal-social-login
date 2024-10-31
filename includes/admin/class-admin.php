<?php
defined( 'ABSPATH' ) || exit();

/**
 * @Class Wpopal_Core_Admin_Menu
 * 
 * Entry point class to setup load all files and init working on frontend and process something logic in admin
 */

class WSL_Core_Admin {

	public function __construct() {

		// add_action('admin_init', array( $this, 'setup' ) , 1);
		$this->load();

	}

	
	
	/**
	 * Load 
	 */
	public function load(){
		global $wsl_options;

		$this->includes( ['admin/class-menu.php',] );
		$this->includes( ['admin/metabox/class-metabox.php',] );
		$this->includes( ['admin/metabox/class-extends.php',] );

		$wsl_options = wsl_get_settings();

	}

	
	/**
	 * Include list of collection files
	 *
	 * @var array $files
	 */
	public function includes ( $files ) {
		foreach ( $files as $file ) {
			$this->_include( $file );
		}
	}
	/**
	 * include single file if found 
	 * 
	 * @var string $file
	 */
	private function _include( $file = '' ) {
		$file = WSL_PLUGIN_INC_DIR  . $file;  
		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}
	 
}

new WSL_Core_Admin();
