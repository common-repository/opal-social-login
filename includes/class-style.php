<?php

defined( 'ABSPATH' ) || exit();


if( !function_exists("wsl_css_styles") ){

	function wsl_css_styles( ) {
		
        wp_enqueue_style('wsl-style', WSL_PLUGIN_URI. 'assets/css/style.css');
	}
}
add_action( 'wp_enqueue_scripts', 'wsl_css_styles', 99 );

