<?php
/*
Plugin Name: Wp Subscribe Author
Plugin URI: http://wordpress.org/extend/plugins/wp-subscribe-author/
Description: Wp Subscribe Author plugin is help subscriber to follow his/her favourite author. Once subscriber starts follow the author, he will get notified all new post of author by email.
Version: 1.1
Author: Gowri Sankar Ramasamy
Author URI: http://code-cocktail.in/author/gowrisankar/
Donate link: http://code-cocktail.in/donate-me/
License: GPL2
Text Domain: wp-subscribe-author
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

function wpsa_init() {	
 load_plugin_textdomain('wp-subscribe-author', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'wpsa_init');


// Adding table in 

global $wpsa_subscribe_author_db_version;
$wpsa_subscribe_author_db_version = "1.0";

function wpsa_subscribe_author_install() {
   global $wpdb;
   global $wpsa_subscribe_author_db_version;

   $table_name = $wpdb->prefix . "wpsa_subscribe_author";
 
    
 $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("wpsa_subscribe_author_db_version", $wpsa_subscribe_author_db_version);
}

register_activation_hook(__FILE__,'wpsa_subscribe_author_install');


// Adding Frontend ajax support

function wpsa_ajax_suport(){

 add_action( 'wp_ajax_wpsa_subscribe_author', 'wpsa_subscribe_author' );
 add_action( 'wp_ajax_nopriv_wpsa_subscribe_author', 'wpsa_subscribe_author' ); // need this to serve non logged in users
 
 //add_action( 'wp_ajax_wpsa_unsubscribe_author', 'wpsa_unsubscribe_author' );
 //add_action( 'wp_ajax_nopriv_wpsa_unsubscribe_author', 'wpsa_unsubscribe_author' ); // need this to serve non logged in users	
 
}
add_action( 'init', 'wpsa_ajax_suport' );



function wpsa_script(){


  wp_enqueue_script( 'jquery' );

  
  wp_register_style( 'tipTip-css', plugins_url('/css/tipTip.min.css', __FILE__) );
  wp_enqueue_style( 'tipTip-css' );	
  
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

add_action('wp_enqueue_scripts', 'wpsa_script');	


function wpsa_subscribe_author(){
   global $wpdb;
   $table_name = $wpdb->prefix . "wpsa_subscribe_author";
   
   $type 	  =  $_POST['type'];
   $author_id 	  =  $_POST['author_id'];
   $subscriber_id =  $_POST['subscriber_id'];
   
   if($type=="subscribe"){  // Subcribe
     
    If(!is_user_subscribed($author_id,$subscriber_id)){
     
     $wpdb->insert($table_name,array('author_id' =>$author_id,'subscriber_id' =>$subscriber_id,'created_at'=>current_time('mysql', 1)),array('%d','%d','%s'));
     
     echo "1";
     }  

   }
   else{  // Unsubcribe
     $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE author_id = %d AND subscriber_id = %d",$author_id,$subscriber_id));
      echo "0";
   }
  
   
 die();    
}

// FUNCTION TO CHECK USER ALREADY SUBCRIBED SAME OTHER OR NOT

function is_user_subscribed($author_id,$subscriber_id){
   global $wpdb;
   $table_name = $wpdb->prefix . "wpsa_subscribe_author";
  
  
     $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE author_id = %d AND subscriber_id = %d",$author_id,$subscriber_id) );
  return  ($subscribe_count==0?false:true);
}

function get_num_subscribers($author_id){
  global $wpdb;
  $table_name = $wpdb->prefix . "wpsa_subscribe_author";
  

  return $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(subscriber_id) FROM $table_name WHERE author_id = %d AND status = %s",$author_id,'active'));
}

function get_subcribe_link(){

 if(get_current_user_id() != get_the_author_meta( "ID" ) && get_current_user_id() != 0){
	       if(is_user_subscribed(get_the_author_meta( "ID" ),get_current_user_id())){
	       
	       printf('<a href="javascript:void(0);" class="wp-subcribe-author-url subscribed" title=" %4$d Subscribers | %5$d posts " data-author="%2$s" data-subscriber="%3$s">%1$s</a>',__('Unsubscribe ', 'wp-subscribe-author').esc_attr( get_the_author() ),get_the_author_meta( "ID" ) ,get_current_user_id(),get_num_subscribers(get_the_author_meta( "ID" )),number_format_i18n( get_the_author_posts() ));	
	       }
	       else{				
	       printf('<a href="javascript:void(0);" class="wp-subcribe-author-url notsubscribed" title=" %4$d Subscribers | %5$d posts " data-author="%2$s" data-author="%2$s" data-subscriber="%3$s">%1$s</a>',__('Subscribe ', 'wp-subscribe-author').esc_attr( get_the_author() ),get_the_author_meta( "ID" ) ,get_current_user_id(),get_num_subscribers(get_the_author_meta( "ID" )),number_format_i18n( get_the_author_posts() ));	
	       }

	 
 } 
 
}




function wpsa_notify_author_subscribers($post) {
     global $wpdb;
     $table_name = $wpdb->prefix . "wpsa_subscribe_author";
     
     $post_author_id = $post->post_author;
     $Author_name =  get_the_author_meta('display_name',$post_author_id);
    
    if(get_num_subscribers($post_author_id) != 0){
        $subscribers = $wpdb->get_results($wpdb->prepare( "SELECT subscriber_id FROM $table_name WHERE author_id = %d AND status = %s",$post_author_id,'active'));
     
     foreach($subscribers as $subscriber){
	$subscriber_id  = $subscriber->subscriber_id;
	$user = get_user_by('id',$subscriber_id);	
	$subscriber_email = $user->user_email;
	
	$postMessage = "";
	$headers = "";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: Wp Subscribe Author <noreply@example.com>" . "\r\n";
		
	$postMessage = get_emailtemplate($post->ID,$post_author_id);

      if (wp_mail($subscriber_email, "New Post From $Author_name", $postMessage, $headers)) {
      
      }
     }     
    }
}
add_action('new_to_publish', 'wpsa_notify_author_subscribers');
add_action('draft_to_publish', 'wpsa_notify_author_subscribers');



function get_emailtemplate($post_id,$author_id){

$post = get_post( $post_id );
$post_title 	= $post->post_title;
$post_content 	= wp_trim_words( $post->post_content );

$author_name 	= get_userdata($author_id)->display_name;
$blogname 	= get_bloginfo('name');
$author_url 	= esc_url( get_author_posts_url($post_id));

$emailtemplate = <<<EOD

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>New Post From $author_name</title>
		<style type="text/css">
			/* Client-specific Styles */
			#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
			body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
			body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
			
			/* Reset Styles */
			body{margin:0; padding:0;}
			img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
			table td{border-collapse:collapse;}
			#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
			
			/* Template Styles */
			
			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: COMMON PAGE ELEMENTS /\/\/\/\/\/\/\/\/\/\ */

			/**
			* @tab Page
			* @section background color
			* @tip Set the background color for your email. You may want to choose one that matches your company's branding.
			* @theme page
			*/
			body, #backgroundTable{
				/*@editable*/ background-color:#FAFAFA;
			}
			
			/**
			* @tab Page
			* @section email border
			* @tip Set the border for your email.
			*/
			#templateContainer{
				/*@editable*/ border:0;
			}
			
			/**
			* @tab Page
			* @section heading 1
			* @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
			* @style heading 1
			*/
			h1, .h1{
				/*@editable*/ color:#202020;
				display:block;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:40px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				margin-top:2%;
				margin-right:0;
				margin-bottom:1%;
				margin-left:0;
				/*@editable*/ text-align:left;
			}

			/**
			* @tab Page
			* @section heading 2
			* @tip Set the styling for all second-level headings in your emails.
			* @style heading 2
			*/
			h2, .h2{
				/*@editable*/ color:#404040;
				display:block;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:18px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				margin-top:2%;
				margin-right:0;
				margin-bottom:1%;
				margin-left:0;
				/*@editable*/ text-align:left;
			}

			/**
			* @tab Page
			* @section heading 3
			* @tip Set the styling for all third-level headings in your emails.
			* @style heading 3
			*/
			h3, .h3{
				/*@editable*/ color:#606060;
				display:block;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:16px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				margin-top:2%;
				margin-right:0;
				margin-bottom:1%;
				margin-left:0;
				/*@editable*/ text-align:left;
			}

			/**
			* @tab Page
			* @section heading 4
			* @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
			* @style heading 4
			*/
			h4, .h4{
				/*@editable*/ color:#808080;
				display:block;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:14px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				margin-top:2%;
				margin-right:0;
				margin-bottom:1%;
				margin-left:0;
				/*@editable*/ text-align:left;
			}
			
			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: PREHEADER /\/\/\/\/\/\/\/\/\/\ */
			
			/**
			* @tab Header
			* @section preheader style
			* @tip Set the background color for your email's preheader area.
			* @theme page
			*/
			#templatePreheader{
				/*@editable*/ background-color:#FAFAFA;
			}
			
			/**
			* @tab Header
			* @section preheader text
			* @tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
			*/
			.preheaderContent div{
				/*@editable*/ color:#707070;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:10px;
				/*@editable*/ line-height:100%;
				/*@editable*/ text-align:left;
			}
			
			/**
			* @tab Header
			* @section preheader link
			* @tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
			*/
			.preheaderContent div a:link, .preheaderContent div a:visited, /* Yahoo! Mail Override */ .preheaderContent div a .yshortcuts /* Yahoo! Mail Override */{
				/*@editable*/ color:#336699;
				/*@editable*/ font-weight:normal;
				/*@editable*/ text-decoration:underline;
			}
			
			/**
			* @tab Header
			* @section social bar style
			* @tip Set the background color and border for your email's footer social bar.
			*/
			#social div{
				/*@editable*/ text-align:right;
			}

			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: HEADER /\/\/\/\/\/\/\/\/\/\ */

			/**
			* @tab Header
			* @section header style
			* @tip Set the background color and border for your email's header area.
			* @theme header
			*/
			#templateHeader{
				/*@editable*/ background-color:#FFFFFF;
				/*@editable*/ border-bottom:5px solid #505050;
			}
			
			/**
			* @tab Header
			* @section left header text
			* @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
			*/
			.leftHeaderContent div{
				/*@editable*/ color:#202020;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:32px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				/*@editable*/ text-align:left;
				/*@editable*/ vertical-align:middle;
			}
			
			/**
			* @tab Header
			* @section right header text
			* @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
			*/
			.rightHeaderContent div{
				/*@editable*/ color:#202020;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:32px;
				/*@editable*/ font-weight:bold;
				/*@editable*/ line-height:100%;
				/*@editable*/ text-align:left;
				/*@editable*/ vertical-align:middle;
			}

			/**
			* @tab Header
			* @section header link
			* @tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
			*/
			.leftHeaderContent div a:link, .leftHeaderContent div a:visited, .rightHeaderContent div a:link, .rightHeaderContent div a:visited{
				/*@editable*/ color:#336699;
				/*@editable*/ font-weight:normal;
				/*@editable*/ text-decoration:underline;
			}

			#headerImage{
				height:auto;
				max-width:180px !important;
			}
			
			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: MAIN BODY /\/\/\/\/\/\/\/\/\/\ */
			
			/**
			* @tab Body
			* @section body style
			* @tip Set the background color for your email's body area.
			*/
			#templateContainer, .bodyContent{
				/*@editable*/ background-color:#FDFDFD;
			}
			
			/**
			* @tab Body
			* @section body text
			* @tip Set the styling for your email's main content text. Choose a size and color that is easy to read.
			* @theme main
			*/
			.bodyContent div{
				/*@editable*/ color:#505050;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:14px;
				/*@editable*/ line-height:150%;
				/*@editable*/ text-align:left;
			}
			
			/**
			* @tab Body
			* @section body link
			* @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
			*/
			.bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
				/*@editable*/ color:#336699;
				/*@editable*/ font-weight:normal;
				/*@editable*/ text-decoration:underline;
			}
			
			.bodyContent img{
				display:inline;
				height:auto;
			}
			
			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: FOOTER /\/\/\/\/\/\/\/\/\/\ */
			
			/**
			* @tab Footer
			* @section footer style
			* @tip Set the background color and top border for your email's footer area.
			* @theme footer
			*/
			#templateFooter{
				/*@editable*/ background-color:#FAFAFA;
				/*@editable*/ border-top:3px solid #909090;
			}
			
			/**
			* @tab Footer
			* @section footer text
			* @tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
			* @theme footer
			*/
			.footerContent div{
				/*@editable*/ color:#707070;
				/*@editable*/ font-family:Arial;
				/*@editable*/ font-size:11px;
				/*@editable*/ line-height:125%;
				/*@editable*/ text-align:left;
			}
			
			/**
			* @tab Footer
			* @section footer link
			* @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
			*/
			.footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
				/*@editable*/ color:#336699;
				/*@editable*/ font-weight:normal;
				/*@editable*/ text-decoration:underline;
			}
			
			.footerContent img{
				display:inline;
			}
			
			/**
			* @tab Footer
			* @section social bar style
			* @tip Set the background color and border for your email's footer social bar.
			* @theme footer
			*/
			#social{
				/*@editable*/ background-color:#FFFFFF;
				/*@editable*/ border:0;
			}
			
			/**
			* @tab Footer
			* @section social bar style
			* @tip Set the background color and border for your email's footer social bar.
			*/
			#social div{
				/*@editable*/ text-align:left;
			}
			
			/**
			* @tab Footer
			* @section utility bar style
			* @tip Set the background color and border for your email's footer utility bar.
			* @theme footer
			*/
			#utility{
				/*@editable*/ background-color:#FAFAFA;
				/*@editable*/ border-top:0;
			}

			/**
			* @tab Footer
			* @section utility bar style
			* @tip Set the background color and border for your email's footer utility bar.
			*/
			#utility div{
				/*@editable*/ text-align:left;
			}
			
			#monkeyRewards img{
				max-width:170px !important;
			}
		</style>
	</head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<center>
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
            	<tr>
                	<td align="center" valign="top">
                        <!-- // Begin Template Preheader \\ -->
                        <table border="0" cellpadding="10" cellspacing="0" width="600" id="templatePreheader">
                            <tr>
                                <td valign="top" class="preheaderContent">
                                
                                	
                                    
                                </td>
                            </tr>
                        </table>
                        <!-- // End Template Preheader \\ -->
                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateContainer">
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- // Begin Template Header \\ -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateHeader">
                                        <tr>
                                            <td class="headerContent">
                                            
                                                <!-- // Begin Module: Letterhead, Center Header Image \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                    	<td class="leftHeaderContent">
                                                            <div mc:edit="header_content_left">
                                                            	$blogname
                                                            </div>
                                                       
                                                    </tr>
                                                </table>
                                                <!-- // End Module: Letterhead, Center Header Image \\ -->

                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Header \\ -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- // Begin Template Body \\ -->
                                	<table border="0" cellpadding="10" cellspacing="0" width="600" id="templateBody">
                                    	<tr>
                                        	<td valign="top" class="bodyContent">
                                            
                                                <!-- // Begin Module: Standard Content \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top">
                                                            <div mc:edit="std_content00">
                                                            	<h2 class="h2">New Post From $author_name</h2>
                                                            	<h3 class="h3">$post_title</h3>
                                                                $post_content
                                                                <br />
      
							       </div>
						       </td>
                                                    </tr>
                                                </table>
                                                <!-- // End Module: Standard Content \\ -->
                                            
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Body \\ -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- // Begin Template Footer \\ -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateFooter">
                                    	<tr>
                                        	<td valign="top" class="footerContent">
                                            
                                                <!-- // Begin Module: Standard Footer \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
  
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="utility">
                                                            <div mc:edit="std_utility">
                                                                &nbsp;<a href="$author_url">'.__('unsubscribe from this list','wp-subscribe-author').'</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- // End Module: Standard Footer \\ -->
                                            
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Footer \\ -->
                                </td>
                            </tr>
                        </table>
                        <br />
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>


EOD;

return $emailtemplate;

 
}


