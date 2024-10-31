<?php
abstract class WSL_METABOX_SETTINGS {
	/**
	 * ID of the settings
	 * @var string
	 */
	public $settings_id = '';
	/**
	 * Tabs for the settings page
	 * @var array
	 */
	public $tabs = array( 
		'providers' => 'Providers' );
	/**
	 * Settings from database
	 * @var array
	 */
	protected $settings = array();
	/**
	 * Array of fields for the general tab
	 * array(
	 * 	'tab_slug' => array(
	 * 		'field_name' => array(),
	 * 		),
	 * 	)
	 * @var array
	 */
	protected $fields = array();
	/** 
	 * Data gotten from POST
	 * @var array
	 */
	protected $posted_data = array();
	/**
	 * Get the settings from the database
	 * @return void 
	 */
	public function init_settings() {
		$this->settings = (array) get_option( $this->settings_id );
		foreach ( $this->fields as $tab_key => $tab ) {
			
			foreach ( $tab as $name => $field ) {
				
				if( isset( $this->settings[ $name ] ) ) {
					$this->fields[ $tab_key ][ $name ]['default'] = $this->settings[ $name ];
				}	
			
			}
		}
	}
	/**
	 * Save settings from POST
	 * @return [type] [description]
	 */
	public function save_settings(){
		
	 	$this->posted_data = $_POST;
	 	if( empty( $this->settings ) ) {
	 		$this->init_settings();
	 	}
	 	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'providers';
	 	foreach ($this->fields as $tab => $tab_data ) {

	 		if($current_tab == $tab) {
		 		foreach ($tab_data as $name => $field) {
		 			$this->settings[ $name ] = $this->{ 'validate_' . $field['type'] }( $name );
		 		}
		 	}

	 	}
	 	update_option( $this->settings_id, $this->settings );	
	}
	/**
	 * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key
	 * @param  mixed  $empty_value
	 * @return mixed  The value specified for the option or a default value for the option.
	 */
	public function get_option( $key, $empty_value = null ) {
		if ( empty( $this->settings ) ) {
			$this->init_settings();
		}
		// Get option default if unset.
		if ( ! isset( $this->settings[ $key ] ) ) {
			$form_fields = $this->fields;
			foreach ( $this->tabs as $tab_key => $tab_title ) {
				if( isset( $form_fields[ $tab_key ][ $key ] ) ) {
					$this->settings[ $key ] = isset( $form_fields[ $tab_key ][ $key ]['default'] ) ? $form_fields[ $tab_key ][ $key ]['default'] : '';
				}
			}
			
		}
		if ( ! is_null( $empty_value ) && empty( $this->settings[ $key ] ) && '' === $this->settings[ $key ] ) {
			$this->settings[ $key ] = $empty_value;
		}
		
		return $this->settings[ $key ];
	}	

	/**
    * Validate text field
	* @param  string $key name of the field
	* @return string     
	*/
    public function validate_color( $key ){
            $text  = $this->get_option( $key );
            if ( isset( $this->posted_data[ $key ] ) ) {
                    $text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
            }
            return $text;
    }

    /**
     * Validate image field
     * @param  string $key name of the field
     * @return string     
     */
    public function validate_image( $key ){
	    $text  = $this->get_option( $key );
	    if ( isset( $this->posted_data[ $key ] ) ) {
	        $text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
	    }
	    return $text;
    }

	/**
	 * Validate heading field
	 * @param  string $key name of the field
	 * @return string     
	 */
	public function validate_heading( $key ){
		$text  = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
		}
		return $text;
	}
  	
	/**
	 * Validate html field
	 * @param  string $key name of the field
	 * @return string     
	 */
	public function validate_html( $key ){
		$text  = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
		}
		return $text;
	}

  	/**
	 * Validate text field
	 * @param  string $key name of the field
	 * @return string     
	 */
	public function validate_text( $key ){
		$text  = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
		}
		return $text;
	}
	/**
	 * Validate textarea field
	 * @param  string $key name of the field
	 * @return string      
	 */
	public function validate_textarea( $key ){
		$text  = $this->get_option( $key );
		 
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses( trim( stripslashes( $this->posted_data[ $key ] ) ),
				array_merge(
					array(
						'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
					),
					wp_kses_allowed_html( 'post' )
				)
			);
		}
		return $text;
	}
	/**
	 * Validate WPEditor field
	 * @param  string $key name of the field
	 * @return string      
	 */
	public function validate_wpeditor( $key ){
		$text  = $this->get_option( $key );
		 
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses( trim( stripslashes( $this->posted_data[ $key ] ) ),
				array_merge(
					array(
						'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
					),
					wp_kses_allowed_html( 'post' )
				)
			);
		}
		return $text;
	}
	/**
	 * Validate select field
	 * @param  string $key name of the field
	 * @return string      
	 */
	public function validate_select( $key ) {
		$value = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$value = wsl_clean( stripslashes( $this->posted_data[ $key ] ) );
		}
		return $value;
	}
	/**
	 * Validate radio
	 * @param  string $key name of the field
	 * @return string      
	 */
	public function validate_radio( $key ) {
		$value = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$value = wsl_clean( stripslashes( $this->posted_data[ $key ] ) );
		}
		return $value;
	}
	/**
	 * Validate checkbox field
	 * @param  string $key name of the field
	 * @return string      
	 */
	public function validate_checkbox( $key ) {
		$status  = '0';
		if ( isset( $this->posted_data[ $key ] ) && ( 1 == $this->posted_data[ $key ] ) ) {
			$status  = '1';
		}
		return $status ;
	}
	/**
	 * Adding fields 
	 * @param array $array options for the field to add
	 * @param string $tab tab for which the field is
	 */
	public function add_field( $array, $tab = 'providers' ) {
		$allowed_field_types = array(
			'color',
			'image',
			'heading',
			'html',
			'text',
			'textarea',
			'wpeditor',
			'select',
			'radio',
			'checkbox' );
		// If a type is set that is now allowed, don't add the field
		if( isset( $array['type'] ) &&$array['type'] != '' && ! in_array( $array['type'], $allowed_field_types ) ){
			return;
		}
		$defaults = array(
			'name' => '',
			'title' => '',
			'default' => '',
			'placeholder' => '',
			'type' => 'text',
			'options' => array(),
			'desc' => '',
			'file' => '',
			);
		$array = array_merge( $defaults, $array );
		if( $array['name'] == '' ) {
			return;
		}
		foreach ( $this->fields as $tabs ) {
			if( isset( $tabs[ $array['name'] ] ) ) {
				trigger_error( 'There is alreay a field with name ' . $array['name'] );
				return;
			}
		}
		// If there are options set, then use the first option as a default value
		if( ! empty( $array['options'] ) && $array['default'] == '' ) {
			$array_keys = array_keys( $array['options'] );
			$array['default'] = $array_keys[0];
		}
		if( ! isset( $this->fields[ $tab ] ) ) {
			$this->fields[ $tab ] = array();
		}
		$this->fields[ $tab ][ $array['name'] ] = $array;
	}
	
	/**
	 * Adding tab
	 * @param array $array options
	 */
	public function add_tab( $array ) {
		$defaults = array(
			'slug' => '',
			'title' => '',
			'button' => '',
		);
		$array = array_merge( $defaults, $array );
		if( $array['slug'] == '' || $array['title'] == '' ){
			return;
		}
		$this->tabs[ $array['slug'] ] = $array['title'];
	}
	/**
	 * Rendering fields 
	 * @param  string $tab slug of tab
	 * @return void  
	 */
	public function render_fields( $tab ) {
		if( ! isset( $this->fields[ $tab ] ) ) {
			echo '<p>' . __( 'There are no settings on these page.', 'opal-social-login' ) . '</p>';
			return;
		}
		$loadColorPicker = false;

		foreach ( $this->fields[ $tab ] as $name => $field ) {
			if( $field['type'] == 'color' ) {
				$loadColorPicker = true;
			}
			$this->{ 'render_' . $field['type'] }( $field );
		} ?>
		<script type="text/javascript">

			jQuery(document).ready(function() {
				<?php if( $loadColorPicker ): ?>
					jQuery('.color-picker').wpColorPicker();
				<?php endif; ?>
				
			});

		</script>
	<?php
	}
	/**	         
	* Render checkbox field
     * @param  string $field options
     * @return void     
     */
    public function render_color( $field ) {
        extract( $field );
        ?>

        <div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
           	<div class="metabox-th">
                <label for="<?php echo $name; ?>"><?php echo $title; ?></label>
            </div>
            <div class="metabox-td">
                <input type="text" class="color-picker" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" placeholder="<?php echo $placeholder; ?>" />
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
            </div>
            
        </div>

        <?php
    }

    /**
     * Render Image field
     * @param  string $field options
     * @return void     
     */
    public function render_image( $field ){
        extract( $field );
        ?>

        <div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
            <div class="metabox-th">
                <label for="<?php echo $name; ?>"><?php echo $title; ?></label>
            </div>
            <div class="metabox-td">
            	<div class="metabox_upload_image">
		            <div class="metabox_option">
		                <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" class="upload-img-url"/>
		                <input type="hidden" id="<?php echo $name; ?>-attachment-id" value="<?php echo $hidden_val; ?>" />
		                <input type="button" value="<?php _e('Upload', 'opal-social-login') ?>" id="<?php echo $name; ?>-button" class="upload-attachment-button button"/>
		            </div>
		            <div class="clear"></div>
		            <span class="description"><?php echo $desc ?></span>
		            <div class="upload-img-preview" style="margin-top:10px;"></div>
		        </div>
	        </div>
        </div>

        <?php
    }

	/**
	 * Render heading field
	 * @param  string $field options
	 * @return void     
	 */
	public function render_heading( $field ){
		extract( $field );
		?>
		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-td">
				<h3 id="heading-<?php echo  $field['name']; ?>" class="metabox-heading"><?php echo $title; ?></h3>	
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}

	/**
	 * Render html field
	 * @param  string $field options
	 * @return void     
	 */
	public function render_html( $field ){
		extract( $field );
		?>
		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-td">
				<?php wsl_get_template_part('includes/admin/screens/class', $file );?>
			</div>
		</div>

		<?php
	}

	/**
	 * Render text field
	 * @param  string $field options
	 * @return void     
	 */
	public function render_text( $field ){
		extract( $field );
		?>
		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" placeholder="<?php echo $placeholder; ?>" />	
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}
	/**
	 * Render textarea field
	 * @param  string $field options
	 * @return void      
	 */
	public function render_textarea( $field ){
		extract( $field );
		?>

		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" ><?php echo $default; ?></textarea>	
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}
	/**
	 * Render WPEditor field
	 * @param  string $field  options
	 * @return void      
	 */
	public function render_wpeditor( $field ){
		
		extract( $field );
		?>

		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<?php wp_editor( $default, $name, array('wpautop' => false) ); ?>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}
	/**
	 * Render select field
	 * @param  string $field options
	 * @return void      
	 */
	public function render_select( $field ) {
		extract( $field );
		?>

		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<select name="<?php echo $name; ?>" id="<?php echo $name; ?>" >
					<?php 
						foreach ($options as $value => $text) {
							echo '<option ' . selected( $default, $value, false ) . ' value="' . $value . '">' . $text . '</option>';
						}
					?>
				</select>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}
	/**
	 * Render radio
	 * @param  string $field options
	 * @return void      
	 */
	public function render_radio( $field ) {
		extract( $field );
		?>

		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<?php 
					foreach ($options as $value => $text) {
						echo '<input name="' . $name . '" id="' . $name . '" type="'.  $type . '" ' . checked( $default, $value, false ) . ' value="' . $value . '">' . $text . '</option><br/>';
					}
				?>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</div>
		</div>

		<?php
	}
	/**
	 * Render checkbox field
	 * @param  string $field options
	 * @return void      
	 */
	public function render_checkbox( $field ) {
		extract( $field );
		?>

		<div class="metabox-row metabox-type-<?php echo  $field['type']; ?> metabox-id-<?php echo  $field['name']; ?>">
			<div class="metabox-th">
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</div>
			<div class="metabox-td">
				<input <?php checked( $default, '1', true ); ?> type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1" placeholder="<?php echo $placeholder; ?>" />
				<p><?php echo $desc; ?></p>
			</div>
		</div>

		<?php
	}
}