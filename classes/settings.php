<?php

class Settings_API_Tabs_WPSA_Plugin{
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $wpsa_general_settings_key = 'wpsa_general_settings';
	private $wpsa_template_settings_key = 'wpsa_template_settings';
	private $wpsa_help_settings_key = 'wpsa_help_settings';
	private $plugin_options_key = 'wpsa_plugin_options';
	private $plugin_settings_tabs = array();

	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
		parent::__construct();
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_wpsa_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_wpsa_template_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_wpsa_help_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		
		add_filter( 'plugin_action_links_'.WPSA_PLUGIN_NAME, array( &$this, 'pluginSettingsLink' ) );

	}
	
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 *
	 * To get settings, use a new WPSACore class and
	 *  call getGeneralOptions and getAdvancedOptions from there
	 */
	function load_settings() {
		$this->wpsa_general_settings = (array) get_option( $this->wpsa_general_settings_key );
		$this->wpsa_template_settings = (array) get_option( $this->wpsa_template_settings_key );
		$this->wpsa_help_settings = (array) get_option( $this->wpsa_help_settings_key );

	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_wpsa_general_settings() {
		$this->plugin_settings_tabs[$this->wpsa_general_settings_key] = __('General','wp-subscribe-author');
		
		register_setting( $this->wpsa_general_settings_key, $this->wpsa_general_settings_key );
		add_settings_section( 'wpsa_section_general',__('WPSA Profit Plugin','wp-subscribe-author'), array( &$this, 'wpsa_section_general_desc' ), $this->wpsa_general_settings_key );
		add_settings_field( 'wpsa_featured_product',__('Default Number of Featured Products','wp-subscribe-author') , array( &$this, 'field_wpsa_featured_product' ), $this->wpsa_general_settings_key, 'wpsa_section_general' );
		
	}

	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_wpsa_template_settings() {
		$this->plugin_settings_tabs[$this->wpsa_template_settings_key] = __('Advanced','wp-subscribe-author');
		
		register_setting( $this->wpsa_template_settings_key, $this->wpsa_template_settings_key,array(&$this,'wpsa_template_settings_callback') );
		add_settings_section( 'wpsa_section_advanced',__('WPSA Profit Plugin','wp-subscribe-author'), array( &$this, 'wpsa_section_template_desc' ), $this->wpsa_template_settings_key );
	
		
	}

	
	
	function register_wpsa_help_settings(){
		
		$this->plugin_settings_tabs[$this->wpsa_help_settings_key] = __('Help','wp-subscribe-author');

		register_setting( $this->wpsa_help_settings_key, $this->wpsa_help_settings_key);
		add_settings_section( 'wpsa_section_buyitnow',__('Buy it Now Links','wp-subscribe-author'), array( &$this, 'wpsa_section_help_desc' ), $this->wpsa_help_settings_key );

		
	}
	
	
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function wpsa_section_general_desc() { echo ''; }
	
	
	function wpsa_section_template_desc() {
		echo '';
	}

	function wpsa_section_help_desc(){
		echo '';
	
	}
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_wpsa_featured_product() {
            
            $wpsa_featured_product = (isset($this->wpsa_general_settings['wpsa_featured_product'])?esc_attr( $this->wpsa_general_settings['wpsa_featured_product'] ):'6');
            
		?>
	     <select name="<?php echo $this->wpsa_general_settings_key; ?>[wpsa_featured_product]">
                        <option value=""><?php echo __('Choose','wp-subscribe-author') ?></option>
                       <?php foreach($this->num_featured_products as $num_featured_product): ?>
                                <option value="<?php echo $num_featured_product; ?>" <?php selected($num_featured_product,$wpsa_featured_product); ?>><?php echo $num_featured_product; ?></option>
                       <?php endforeach; ?>	 
            </select>
           
		<?php
	}

  


	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {
		add_options_page(__('Subscribe Auhtor','wp-subscribe-author'),__('Subscribe Auhtor','wp-subscribe-author'), 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}
	
	
	#
	# Plugin Settings link
	#

	public function pluginSettingsLink($links){
	   $settings_link = '<a href="options-general.php?page='.$this->plugin_options_key.'.php">'.__('Settings').'</a>'; 
	   array_unshift($links, $settings_link); 
	  return $links; 
	}
	

	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->wpsa_general_settings_key;
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				
				<?php
				if($tab != 'wpsa_help_settings'){
					submit_button();	
				}								
				?>
			</form>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->wpsa_general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
};

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_api_tabs_wpsa_plugin = new Settings_API_Tabs_WPSA_Plugin;' ) );