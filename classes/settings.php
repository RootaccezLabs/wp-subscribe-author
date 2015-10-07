<?php

class Settings_API_Tabs_WPSA_Plugin{
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $wpsa_general_settings_key = 'wpsa_general_settings';
	private $wpsa_mail_settings_key = 'wpsa_mail_settings';
	private $plugin_options_key = 'wpsa_plugin_options';
	private $plugin_settings_tabs = array();

	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
	
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_wpsa_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_wpsa_mail_settings' ) );
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
		$this->wpsa_mail_settings = (array) get_option( $this->wpsa_mail_settings_key );

	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_wpsa_general_settings() {
		$this->plugin_settings_tabs[$this->wpsa_general_settings_key] = __('General','wp-subscribe-author');
		
		register_setting( $this->wpsa_general_settings_key, $this->wpsa_general_settings_key );
		add_settings_section( 'wpsa_section_general',__('General Settings','wp-subscribe-author'), array( &$this, 'wpsa_section_general_desc' ), $this->wpsa_general_settings_key );
		add_settings_field( 'show_on_card',__('Show on card','wp-subscribe-author') , array( &$this, 'field_show_on_card' ), $this->wpsa_general_settings_key, 'wpsa_section_general' );
		
	}

	
	function register_wpsa_mail_settings() {
		$this->plugin_settings_tabs[$this->wpsa_mail_settings_key] = __('Mail','wp-subscribe-author');
		
		register_setting( $this->wpsa_mail_settings_key, $this->wpsa_mail_settings_key );
		add_settings_section( 'wpsa_section_mail',__('Mail Settings','wp-subscribe-author'), array( &$this, 'wpsa_section_mail_desc' ), $this->wpsa_mail_settings_key );
		add_settings_field( 'sender_name',__('Sender Name','wp-subscribe-author') , array( &$this, 'field_sender_name' ), $this->wpsa_mail_settings_key, 'wpsa_section_mail' );
		add_settings_field( 'sender_email',__('Sender Email','wp-subscribe-author') , array( &$this, 'field_sender_email' ), $this->wpsa_mail_settings_key, 'wpsa_section_mail' );
		
	}	

	
	
	
	
	
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function wpsa_section_general_desc() { echo ''; }
	function wpsa_section_mail_desc() { echo ''; }
	
	

	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_show_on_card() {
            
            $show_on_card = (isset($this->wpsa_general_settings['show_on_card'])?( $this->wpsa_general_settings['show_on_card'] ):'');
            
	    $fields = array('first_name'=>'First Name','last_name'=>'Last Name','nickname'=>'Nick Name','description'=>'About');
	    
	    
		?>
		
		<ul>
			<?php foreach($fields as $key=>$row){ ?>
				
				<li><input type="checkbox" name="<?php echo $this->wpsa_general_settings_key; ?>[show_on_card][<?php echo $key; ?>]" value="<?php echo $key; ?>" <?php  echo (!empty($show_on_card[$key])?'checked="checked"':''); ?> /><label><?php echo $row; ?></label></li>
				
			<?php } ?>
			
			
		</ul>
		

           
		<?php
	}

  
	function field_sender_name(){
		$sender_name = (isset($this->wpsa_mail_settings['sender_name'])?( $this->wpsa_mail_settings['sender_name'] ):get_bloginfo('name'));
		
		
		?>
		<input type="text" name="<?php echo $this->wpsa_mail_settings_key; ?>[sender_name]" value="<?php echo $sender_name; ?>" />
		<?php 
	}
	
	
	function field_sender_email(){
		
		$parse = parse_url(get_option('siteurl'));
		$blog_host = $parse['host'];
		$default_sender_email = "no-reply@".$blog_host;
						
						
		$sender_email = (isset($this->wpsa_mail_settings['sender_email'])?( $this->wpsa_mail_settings['sender_email'] ):$default_sender_email);
		
		
		?>
		<input type="email" name="<?php echo $this->wpsa_mail_settings_key; ?>[sender_email]" value="<?php echo $sender_email; ?>" />
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