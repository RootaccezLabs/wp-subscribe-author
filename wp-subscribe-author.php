<?php
/*
Plugin Name: Wp Subscribe Author
Plugin URI: http://wordpress.org/extend/plugins/wp-subscribe-author/
Description: Wp Subscribe Author plugin is help subscriber to follow his/her favourite author. Once subscriber starts follow the author, he will get notified all new post of author by email.
Version: 1.0
Author: Gowri Sankar Ramasamy
Author URI: http://code-cocktail.in/author/gowrisankar/
Donate link: http://code-cocktail.in/donate-me/
License: GPL2
*/

/*  
	Copyright 2012  Gowri Sankar Ramasamy  (email : gchokeen@gmail.com)

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

//ini_set('display_errors', true);
//error_reporting(E_ERROR);


require_once (dirname(__FILE__) . '/classes/Model/wpsa_model.php');
require_once (dirname(__FILE__) . '/classes/wpsa_template.php');
require_once (dirname(__FILE__) . '/classes/wpsa_shortcode.php');

//require_once (dirname(__FILE__) . '/wpsa-ajax.php');

define('WPSA_PLUGIN_NAME', plugin_basename(__FILE__));
define('WPSA_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);


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

            ## Register plugin widgets
            
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
        
        
        
        ##
        ## Loading Scripts and Styles
        ##

        public function adminLoadStyles()
        {
        }

        public function adminLoadScripts()
        {

	
        }
        
        
        
        public function siteLoadStyles()
        {
	  wp_register_style( 'tipTip-css', plugins_url('/css/tipTip.min.css', __FILE__) );
	  wp_enqueue_style( 'tipTip-css' );	
            
        }
        
        
        public function siteLoadScripts()
        {
	  wp_enqueue_script( 'jquery' );
	  
	  	  
	  wp_register_script('tipTip-script',plugin_dir_url(__FILE__).'js/jquery.tipTip.minified.js');
	  wp_enqueue_script(
		'tipTip-script',
		plugins_url(plugin_dir_url(__FILE__).'js/jquery.tipTip.minified.js', __FILE__),
		array('tipTip-script')
	  );
	  
	  wp_register_script('wpsa-subscribe-author-script',plugin_dir_url(__FILE__).'js/wpsa-subscribe-author.js');
	  wp_enqueue_script(
		'wpsa-subscribe-author-script',
		plugins_url(plugin_dir_url(__FILE__).'js/wpsa-subscribe-author.js', __FILE__),
		array('wpsa-subscribe-author-script')
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
                
        
        }

        /**
         * Deactivate plugin
         * @return void
         */
        public function pluginDeactivate()
        {
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
    
    
  }

}


//instantiate the class
if (class_exists('Wp_Subscribe_Author')) {
  $Wp_Subscribe_Author =  Wp_Subscribe_Author::getInstance();
}


