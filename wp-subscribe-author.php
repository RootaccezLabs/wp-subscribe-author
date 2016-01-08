<?php
/*
Plugin Name: Wp Subscribe Author
Plugin URI: http://wordpress.org/extend/plugins/wp-subscribe-author/
Description: Wp Subscribe Author plugin is help subscriber to follow his/her favourite author. Once subscriber starts follow the author, he will get notified all new post of author by email.
Version: 1.8
Author: Gowri Sankar Ramasamy
Author URI: http://code-cocktail.in/author/gowrisankar/
Donate link: http://code-cocktail.in/donate-me/
License: GPL2
Text Domain: wp-subscribe-author
*/

/*  
	Copyright 2014  Gowri Sankar Ramasamy  (email : gchokeen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// loads language files


define('WPSA_PLUGIN_NAME', plugin_basename(__FILE__));
define('WPSA_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('WPSA_PLUGIN_VERSION','1.8');

require_once (dirname(__FILE__) . '/classes/Model/wpsa_model.php');
require_once (dirname(__FILE__) . '/classes/wpsa_template.php');
require_once (dirname(__FILE__) . '/classes/wpsa_shortcode.php');
require_once (dirname(__FILE__) . '/classes/settings.php');


require_once (dirname(__FILE__) . '/wpsa-ajax.php');
require_once (dirname(__FILE__) . '/wpsa-unsubscribe.php');



if (!class_exists('Wp_Subscribe_Author')) {

	class Wp_Subscribe_Author{


		/**
		 * @var Wp_Subscribe_Author
		 */
		static private $_instance = null;

		/**
		 * Get Wp_Subscribe_Author object
		 *
		 * @return Wp_Subscribe_Author
		 */
		static public function getInstance()
		{
			if (self::$_instance == null) {
				self::$_instance = new Wp_Subscribe_Author();
			}

			return self::$_instance;
		}


		private function __construct()
		{

			register_activation_hook(WPSA_PLUGIN_NAME, array(&$this, 'pluginActivate'));
			register_deactivation_hook(WPSA_PLUGIN_NAME, array(&$this, 'pluginDeactivate'));
			register_uninstall_hook(WPSA_PLUGIN_NAME, array('wp-subscribe-author', 'pluginUninstall'));

			add_action('new_to_publish', array($this, 'wpsa_notify_author_subscribers'));
			add_action('draft_to_publish', array($this, 'wpsa_notify_author_subscribers'));
	
			
			
			## Register plugin widgets
			add_action('init', array($this, 'load_wpsa_transl'));
			add_action('plugins_loaded', array(&$this, 'pluginLoad'));

			if (is_admin()) {
			add_action('wp_print_scripts', array(&$this, 'adminLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'adminLoadStyles'));
			}
			else{

			add_action('wp_print_scripts', array(&$this, 'siteLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'siteLoadStyles'));


			}


		}

		public function load_wpsa_transl()
		{
			load_plugin_textdomain('wp-subscribe-author', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}

		##
		## Loading Scripts and Styles
		##
	
		public function adminLoadStyles()
		{
		}
	
		public function adminLoadScripts()
		{
	
	
		}
	
	
	
		public function siteLoadStyles(){
			

	
		}
	
	
		public function siteLoadScripts()
		{
			wp_enqueue_script( 'jquery' );
			 
			wp_enqueue_script(
			'hovercard-script',
			plugins_url('js/jquery.hovercard.min.js', __FILE__),
			array('jquery')
			);
			 
		
			wp_enqueue_script(
			'wpsa-subscribe-author-script',
			plugins_url('js/wpsa-subscribe-author.js', __FILE__),
			array('jquery','hovercard-script')
			);
	
			wp_localize_script( 'wpsa-subscribe-author-script', 'wpsa_ajax_suport', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	
	
		}



		##
		## Widgets initializations
		##

		public function widgetsRegistration()
		{
		 
		 
		}


		##
		## Plugin Activation and Deactivation
		##

		/**
		* Activate plugin
		* @return void
		*/
		public function pluginActivate()
		{
		 
			global $wpdb;
				
			$tbl_wpsa_subscribe_author = $wpdb->prefix.'wpsa_subscribe_author';
				
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			if($wpdb->get_var("show tables like '$tbl_wpsa_subscribe_author'") != $tbl_wpsa_subscribe_author)
			{
				$tbl_wpsa_subscribe_author_query = "CREATE TABLE IF NOT EXISTS $tbl_wpsa_subscribe_author (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`author_id` int(11) NOT NULL,
				`subscriber_id` int(11) NOT NULL,
				`subscriber_email` varchar(120) NOT NULL,
				`status` VARCHAR( 10 ) NOT NULL DEFAULT 'active' COMMENT '''active'',''pending''',
				`created_at` datetime NOT NULL,
				`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				KEY `author_id` (`author_id`,`subscriber_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				";


				dbDelta($tbl_wpsa_subscribe_author_query);
	
				}
				
				update_option("wpsa_subscribe_author_db_version", WPSA_PLUGIN_VERSION);
				
				
				

			}

			/**
			* Deactivate plugin
			* @return void
			*/
			public function pluginDeactivate(){
				
			}

			/**
			* Uninstall plugin
			* @return void
			*/
			static public function pluginUninstall()
			{

			}


			public function pluginLoad(){

			}
			
			
			public function  wpsa_notify_author_subscribers($post){
				
				$wpsamodel =new Wpsa_Model();
				$template =new Wpsa_Template();
				
				$author_id = $post->post_author;
				$Author_name = get_the_author_meta('display_name',$author_id);
				
				if($wpsamodel->get_num_subscribers($author_id) != 0){
					
					$subscribers = $wpsamodel->getAllSubscribers($author_id);
									
				
					foreach($subscribers as $subscriber){
						
						$subscriber_id = $subscriber->subscriber_id;
						
						if($subscriber_id != 0 ){
							$user = get_user_by('id',$subscriber_id);
							$subscriber_email = $user->user_email;
						}
						else{
							$subscriber_email = $subscriber->subscriber_email;
						}
					
						$postMessage = "";
						
						$param = array('post_id'=>$post->ID,'author_id'=>$author_id,'subscriber_email'=>$subscriber_email);
						
						$postMessage = $template->renderTemplate('default',$param);
					
					
						$mail_settings = get_option('wpsa_mail_settings');
					
						$parse = parse_url(get_option('siteurl'));
						$blog_host = $parse['host'];
						
						$sender_name = (!empty($mail_settings['sender_name'])?$mail_settings['sender_name']:get_bloginfo('name'));
						$sender_email = "no-reply@".$blog_host;
					
						$headers = '';
						
						$headers  = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
						$headers .= "From: ".$sender_name." <".$sender_email.">" . "\r\n";
						
						$subject ="New Post From $Author_name";
						

			
						if(wp_mail($subscriber_email,$subject , $postMessage, $headers)){
							
							//echo "sending success";
						}
						else{
							
							//echo "sending failed";
						}
					
					

						
					} //end loop
					
					
				
			
				
				
			}


		}

}
}


//instantiate the class
if (class_exists('Wp_Subscribe_Author')) {
	$Wp_Subscribe_Author =  Wp_Subscribe_Author::getInstance();
}

				




