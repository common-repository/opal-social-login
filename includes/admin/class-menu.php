<?php 
defined( 'ABSPATH' ) || exit();

/**
 * @Class WSL_Setting
 * 
 * Entry point class to setup load all files and init working on frontend and process something logic in admin
 */
class WSL_Setting {

	private $key = 'wsl_settings';

	 public function __construct () {

        add_action( 'admin_init', array( $this, 'init' ) );

        add_action( 'plugins_loaded' , array( $this, 'render'  ) );

        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 999);

	}


    public function enqueue_scripts(){
    	wp_enqueue_media();
        wp_enqueue_style('wsl-admin-style', WSL_PLUGIN_URI . 'assets/css/admin-styles.css');
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        wp_enqueue_script( 'wsl-admin-js', WSL_PLUGIN_URI.( 'includes/admin/metabox/assets/wsl-admin.js') , array( 'jquery' ), true );
    }

    /**
     * Register our setting to WP
     * @since  1.0
     */
    public function init() {
        register_setting( $this->key, $this->key );

    }

    public function helps() { ?>
    	<div class="wsl-admin-header">
    <h1>
        <img src="<?php echo  WSL_PLUGIN_URI.'assets/images/opal-logo.png'; ?>" width="auto" height="64"
             alt="Opal Social Login"/>
        Opal Social Login
    </h1>

   
</div>
    
    <?php	
    }

    /**
     *
     */
    public function render() {   

        $redirect_url = site_url() .'/wp-login.php';

        $custom_file = new WSL_METABOX( array(
			'parent'	=> 'options-general.php',
			'slug' 		=> MENU_SLUG,
			'title'		=> __( 'Opal Social Login', 'opal-social-login' ),
			'position' 	=> 99,
		));

        //Facebook settings
        $custom_file->add_field(array(
			'name' 		=> 'facebook_title',
			'title' 	=> __( 'Facebook Connections', 'opal-social-login' ),
			'type' 		=> 'heading',
			'desc' => 'Valid OAuth redirect URIs: <b>'.$redirect_url.'</b>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_facebook_enable',
			'title' 	=> __( 'Enable Facebook Login', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
			
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_facebook_id',
			'title' 	=> __( 'Facebook App Id', 'opal-social-login' ),
			'type' 		=> 'text',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_facebook_secret',
			'title' 	=> __( 'Facebook Secret', 'opal-social-login' ),
			'type' 		=> 'text',
			'desc'    => 'Create Facebook App: <a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_facebook_icon',
			'title' 	=> __( 'Facebook Icon', 'opal-social-login' ),
			'type' 		=> 'image',
			
		));

		//Twitter settings
		$custom_file->add_field(array(
			'name' 		=> 'twitter_title',
			'title' 	=> __( 'Twitter Connections', 'opal-social-login' ),
			'type' 		=> 'heading',
			'desc' => 'Add the following URL to the "Callback URLs" field: <b>'.$redirect_url.'</b>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_twitter_enable',
			'title' 	=> __( 'Enable Twitter Login', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_twitter_key',
			'title' 	=> __( 'Twitter App Id', 'opal-social-login' ),
			'type' 		=> 'text',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_twitter_secret',
			'title' 	=> __( 'Twitter Secret', 'opal-social-login' ),
			'type' 		=> 'text',
			'desc'    => 'Create Twitter App" <a href="https://developer.twitter.com/en/apps/create">https://developer.twitter.com/en/apps/create</a>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_twitter_icon',
			'title' 	=> __( 'Twitter Icon', 'opal-social-login' ),
			'type' 		=> 'image',
			
		));
		
		//Google settings
		$custom_file->add_field(array(
			'name' 		=> 'google_title',
			'title' 	=> __( 'Google Connections', 'opal-social-login' ),
			'type' 		=> 'heading',
			'desc' => 'Add the following URL to the "Callback URLs" field: <b>'.$redirect_url.'</b>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_google_enable',
			'title' 	=> __( 'Enable Google Login', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_google_id',
			'title' 	=> __( 'Google Id', 'opal-social-login' ),
			'type' 		=> 'text',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_google_secret',
			'title' 	=> __( 'Google Secret', 'opal-social-login' ),
			'type' 		=> 'text',
			'desc'    => 'Create Google App: <a href="https://console.developers.google.com/apis/">https://console.developers.google.com/apis/</a>',
		));

		$custom_file->add_field(array(
			'name' 		=> 'wsl_google_icon',
			'title' 	=> __( 'Google Icon', 'opal-social-login' ),
			'type' 		=> 'image',
			
		));
		
		// Settings Tab
		$settingsTab = new WSL_METABOXTAB( 
			array(
				'slug' 	=> 'settings', 
				'title' => __( 'Settings', 'opal-social-login' ),
			), 
			$custom_file );

		//Global settings
		$settingsTab->add_field(array(
			'name' 		=> 'global_title',
			'title' 	=> __( 'General settings', 'opal-social-login' ),
			'type' 		=> 'heading',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_social_label',
			'title' 	=> __( 'Label', 'opal-social-login' ),
			'desc'    => 'Change content of the label to display above social login buttons',
			'type'    	=> 'text',
			'default' 	=> 'Login with',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_button_style',
			'title' 	=> __( 'Login form button style', 'opal-social-login' ),
			'type'    	=> 'radio',
			'options'       => array(
                'default'   => __( 'Default', 'opal-social-login' ),
                'buttons'     => __( 'Buttons', 'opal-social-login' ),
            ),
            'default'       => 'default',
			
		));

		$settingsTab->add_field(array(
			'name' 		=> 'show_login_button_title',
			'title' 	=> __( 'Show login buttons in:', 'opal-social-login' ),
			'type' 		=> 'heading',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_login_form',
			'title' 	=> __( 'WordPress Login', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_registration_form',
			'title' 	=> __( 'Registration Form', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_woo_form',
			'title' 	=> __( 'Woocommerce Login', 'opal-social-login' ),
			'type' 		=> 'checkbox',
			'default'	=> '1',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_button_color',
			'title' 	=> __( 'Buttons Colors', 'opal-social-login' ),
			'type' 		=> 'heading',
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_facebook_color',
			'title' 	=> __( 'Facebook Color', 'opal-social-login' ),
			'type' 		=> 'color',
			'default'   => '#3b5998', 	
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_twitter_color',
			'title' 	=> __( 'Twitter Color', 'opal-social-login' ),
			'type' 		=> 'color',
			'default'   => '#00aced', 		
		));

		$settingsTab->add_field(array(
			'name' 		=> 'wsl_google_color',
			'title' 	=> __( 'Google Color', 'opal-social-login' ),
			'type' 		=> 'color',
			'default'   => '#dd4b39', 		
		));

		// Settings Tab
		$helpTab = new WSL_METABOXTAB( 
			array(
				'slug' 	=> 'help', 
				'title' => __( 'Helps', 'opal-social-login' ),
				'button' => 'no',
			), 
			$custom_file );

		$helpTab->add_field(array(
			'name' 		=> 'wsl_social_help',
			'file'    => 'help',
			'type'    	=> 'html',
			
		));
    }
    
} 

new WSL_Setting();