<?php

class WSL_METABOX extends WSL_METABOX_SETTINGS {
	/**
	 * Default options
	 * @var array
	 */
	public $defaultOptions = array(
		'slug' => '', // Name of the menu item
		'title' => '', // Title displayed on the top of the admin panel
		'page_title' => '',
		'parent' => '', // id of parent, if blank, then this is a top level menu
		'id' => '', // Unique ID of the menu item
		'capability' => 'manage_options', // User role
		'icon' => 'dashicons-admin-generic', // Menu icon for top level menus only http://melchoyce.github.io/dashicons/
		'position' => null, // Menu position. Can be used for both top and sub level menus
		'desc' => '', // Description displayed below the title
		'function' => ''
	);
	/**
	 * Gets populated on submenus, contains slug of parent menu
	 * @var null
	 */
	public $parent_id = '';
	/**
	 * Menu options
	 * @var array
	 */
	public $menu_options = array();
	function __construct( $options ) {
		
		$this->menu_options = array_merge( $this->defaultOptions, $options );
		if( $this->menu_options['slug'] == '' ){
			return;
		}
		$this->settings_id = $this->menu_options['slug'];
		$this->prepopulate();
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'wordpressmenu_page_save_' . $this->settings_id, array( $this, 'save_settings' ) );
	}
	//...
	
	 /**
	 * Populate some of required options
	 * @return void 
	 */
	public function prepopulate() {
		if( $this->menu_options['title'] == '' ) {
			$this->menu_options['title'] = ucfirst( $this->menu_options['slug'] );
		}
		if( $this->menu_options['page_title'] == '' ) {
			$this->menu_options['page_title'] = $this->menu_options['title'];
		}
	}
	/**
	 * Add the menu page using WordPress API
	 * @return [type] [description]
	 */
	public function add_page() {
		$functionToUse = $this->menu_options['function'];
		if( $functionToUse == '' ) {
			$functionToUse = array( $this, 'create_menu_page' );
		}
		add_submenu_page( 
		 	$this->menu_options['parent'],
			$this->menu_options['page_title'],
			$this->menu_options['title'],
			$this->menu_options['capability'],
			$this->menu_options['slug'],
			$functionToUse 
		);	
	}
	
	//...
	/**
	 * Create the menu page
	 * @return void 
	 */
	public function create_menu_page() {
		$this->save_if_submit();
		$tab = 'providers';
		if( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		}
		$this->init_settings();
		?>
		<div class="opal-metabox-form">
			<?php wsl_get_template_part('includes/admin/screens/class', 'header' ); ?>
			<?php
				if ( ! empty( $this->menu_options['desc'] ) ) {
					?><p class='description'><?php echo $this->menu_options['desc'] ?></p><?php
				}
				$this->render_tabs( $tab );
			?>
			<form class="tab_<?php echo $tab;?>" method="POST" action="">
				<div class="postbox">
					<div class="inside">
						<div class="form-table">
							<?php $this->render_fields( $tab ); ?>
						</div>
						<?php $this->save_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
	
	//...
	
	/**
	 * Render the registered tabs
	 * @param  string $active_tab the viewed tab
	 * @return void          
	 */
	public function render_tabs( $active_tab = 'providers' ) {
		if( count( $this->tabs ) > 1 ) {
			echo '<div class="nav-tab-wrapper woo-nav-tab-wrapper">';
				foreach ($this->tabs as $key => $value) {
					echo '<a href="' . admin_url('admin.php?page=' . $this->menu_options['slug'] . '&tab=' . $key ) . '" class="nav-tab ' .  ( ( $key == $active_tab ) ? 'nav-tab-active' : '' ) . ' ">' . $value . '</a>';
				}
			echo '</div>';
		}
	}
	/**
	 * Render the save button
	 * @return void 
	 */
	protected function save_button() { 
		?>
		<button type="submit" name="<?php echo $this->settings_id; ?>_save" class="button button-primary">
			<?php _e( 'Save Changes', 'opal-social-login' ); ?>
		</button>
		<?php
	}
	/**
	 * Save if the button for this menu is submitted
	 * @return void 
	 */
	protected function save_if_submit() {
		if( isset( $_POST[ $this->settings_id . '_save' ] ) ) {
			do_action( 'wordpressmenu_page_save_' . $this->settings_id );
		}
	}
	
	//...
}
class WSL_SUPMENU extends WSL_METABOX {
	function __construct( $options, WSL_METABOX $parent ){
		parent::__construct( $options );
		$this->parent_id = $parent->settings_id;
	}
}

class WSL_METABOXTAB {
	public $slug;
	public $title;
	public $menu;
	function __construct( $options, WSL_METABOX $menu ){
		$this->slug = $options['slug'];
		$this->title = $options['title'];
		$this->menu = $menu;
		$this->menu->add_tab( $options );
	}
	/**
	 * Add field to this tab
	 * @param [type] $array [description]
	 */
	public function add_field( $array ){
		$this->menu->add_field( $array, $this->slug );
	}
}
