<?php 

class Wpcq_Model{
	
	private $tbl_wpsa_subscribe_author;

	private $prefix;
        
	function __construct(){
		global $wpdb;
		
		$this->prefix = $wpdb->prefix;
		
		$this->tbl_wpsa_subscribe_author = $this->prefix.'wpsa_subscribe_author';
	}
	
/*
 *  Model Functions for Test
 * 
 * 
 */	
	
// FUNCTION TO CHECK USER ALREADY SUBCRIBED SAME OTHER OR NOT

    public function is_user_subscribed($author_id,$subscriber_id){
       global $wpdb;
      
         $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND subscriber_id = %d",$author_id,$subscriber_id) );
      return  ($subscribe_count==0?false:true);
    }
    
    public function get_num_subscribers($author_id){
      global $wpdb;
      
      return $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(subscriber_id) FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND status = %s",$author_id,'active'));
    }	
	
	

}