<?php

defined( 'ABSPATH' ) || exit();

if( ! class_exists( 'WSL_Social_Login_Frontend' ) ){
    /**
     * Opal Social Login Admin class
     *
     * @since 1.0.0
     */
    class WSL_Social_Login_Frontend {
        /**
         * Single instance of the class
         *
         * @var \WSL_Social_Login_Frontend
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \WSL_Social_Login_Frontend
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
            if( !empty(wsl_get_option( 'wsl_woo_form' )) ||  wsl_get_option( 'wsl_woo_form' ) == '1' ) {
                add_action('woocommerce_login_form', array( $this,'wsl_social_buttons') );
            }

            if( !empty(wsl_get_option( 'wsl_registration_form' )) ||  wsl_get_option( 'wsl_registration_form' ) == '1' ) {
                add_action('register_form', array( $this,'wsl_social_buttons') );
            }
           
            if( !empty(wsl_get_option( 'wsl_login_form' )) ||  wsl_get_option( 'wsl_login_form' ) == '1' ) {
                add_action('login_form', array( $this,'wsl_social_buttons'), 99999);
            }

            //custom styles and javascripts
             add_action('login_head', array( $this,'plugin_styles'), 100);
        }
        /**
         * Enqueue Scripts and Styles
         *
         */
        public function plugin_styles( ) {
            wp_enqueue_style('wsl-style', WSL_PLUGIN_URI. 'assets/css/style.css');
        }
        
	    /**
	     * Print social buttons
	     *
	     */
	    public function wsl_social_buttons( $template_part = '', $is_shortcode = false, $atts = array() ) {
		    $enabled_social = WSL_Social_Login()->enabled_social;
            
    		$template_part  = empty( $template_part ) ? 'social-buttons' : $template_part;
            
		    if ( $is_shortcode ) {
			    ob_start();
		    }

		    $args = array(
			    'label'          => wsl_get_option( 'wsl_social_label' ),
			    'socials'        => $enabled_social,
			    'label_checkout' => wsl_get_option( 'wsl_social_label_checkout' ),
                'redirect_to'    => WSL_Social_Login()->get_redirect_to(),
		    );

		    $args = wp_parse_args( $atts, $args );

		    if ( !is_user_logged_in() && !empty( $enabled_social ) ) {
			    wsl_get_template( $template_part . '.php', $args,  '', WSL_PLUGIN_TEMPLATE_DIR.'/');
		    }

		    if ( $is_shortcode ) {
			    return ob_get_clean();
		    }

	    }
    }

    /**
     * Unique access to instance of WSL_Social_Login_Frontend class
     *
     * @return \WSL_Social_Login_Frontend
     */
    function WSL_Social_Login_Frontend() {
        return WSL_Social_Login_Frontend::get_instance();
    }


}
