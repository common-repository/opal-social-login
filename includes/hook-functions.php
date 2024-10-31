<?php

defined( 'ABSPATH' ) || exit();


function wsl_get_template_part($slug, $name = null) {
	do_action("ccm_get_template_part_{$slug}", $slug, $name);
	$templates = array();
	if (isset($name))
	  	$templates[] = "{$slug}-{$name}.php";
	$templates[] = "{$slug}.php";
	wsl_get_template_path($templates, true, false);
}

/* Extend locate_template from WP Core 
* Define a location of your plugin file dir to a constant in this case = PLUGIN_DIR_PATH 
* Note: PLUGIN_DIR_PATH - can be any folder/subdirectory within your plugin files 
*/ 
function wsl_get_template_path($template_names, $load = false, $require_once = true ) {
    $located = ''; 
    foreach ( (array) $template_names as $template_name ) { 
      if ( !$template_name ) 
        continue; 
      /* search file within the PLUGIN_DIR_PATH only */ 
      if ( file_exists(WSL_PLUGIN_DIR . $template_name)) { 
        $located = WSL_PLUGIN_DIR . $template_name; 
        break; 
      } 
    }
    if ( $load && '' != $located )
        load_template( $located, $require_once );
    return $located;
}


/**
 * Wrapwslr function around cmb2_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function wsl_get_option( $key = '', $default = false ) {
    global $wsl_options;
    $value = ! empty( $wsl_options[ $key ] ) ? $wsl_options[ $key ] : $default;
    $value = apply_filters( 'wsl_get_option', $value, $key, $default );

    return apply_filters( 'wsl_get_option_' . $key, $value, $key, $default );
}

/**
 * Get Settings
 *
 * Retrieves all wsl plugin settings
 *
 * @since 1.0
 * @return array wsl settings
 */
function wsl_get_settings() {

    $settings = get_option( MENU_SLUG );

    return (array) apply_filters( 'wsl_get_settings', $settings );

}


function wsl_curPageURL() {
    $pageURL = 'http';
    if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

if ( ! function_exists( 'wsl_check_wpengine' ) ) {
    /**
     * Check if the website is stored on wp engine
     */
    function wsl_check_wpengine() {
        $is_wp_engine = defined( 'WPE_APIKEY' );

        if ( $is_wp_engine && ! defined( 'WSL_FINAL_SLASH' ) ) {
            define( 'WSL_FINAL_SLASH', true );
        }

        return $is_wp_engine;
    }
}
$enabled = wsl_get_option( 'wsl_facebook_enable' );

/**
 * Get other templates passing attributes and including the file.
 */
function wsl_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args ); // @codingStandardsIgnoreLine
    }

    $located = wsl_locate_template( $template_name, $template_path, $default_path );
    if ( ! file_exists( $located ) ) {
        /* translators: %s template */
        wc_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'opal-social-login' ), '<code>' . $located . '</code>' ), '2.1' );
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters( 'wsl_get_template', $located, $template_name, $args, $template_path, $default_path );

    include $located;
}

function wsl_locate_template( $template_name, $template_path = '', $default_path = '' ) {

    $template = $default_path . $template_name;
    
    // Return what we found.
    return apply_filters( 'wsl_locate_template', $template, $template_name, $template_path );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 */
function wsl_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'wsl_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

function wsl_do_shortcode( $tag, array $atts = array(), $content = null ) {
    global $shortcode_tags;

    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
        return false;
    }
    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
 * Hook the Social Login Buttons
 */
function add_social_login(){
    echo wsl_do_shortcode( 'opal-social-login' );
}
