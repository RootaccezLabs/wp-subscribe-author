<?php 

class Wpsa_Model{
	
	private $tbl_wpsa_subscribe_author;

	private $prefix;
        
	function __construct(){
		global $wpdb;
		
		$this->prefix = $wpdb->prefix;
		
		$this->tbl_wpsa_subscribe_author = $this->prefix.'wpsa_subscribe_author';
	}
	
	/*
	 *  FUNCTION TO CHECK USER ALREADY SUBCRIBED SAME OTHER OR NOT
	 * 
	 * 
	 */	
	
    public function is_user_subscribed($author_id,$subscriber_id){
       global $wpdb;
      
         $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND subscriber_id = %d",$author_id,$subscriber_id) );
      return  ($subscribe_count==0?false:true);
    }
    
    /*
     * It gives the number of author subscribers 
     * @param: int $author_id 
     */
    public function get_num_subscribers($author_id){
      global $wpdb;
      
      return $subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(subscriber_id) FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND status = %s",$author_id,'active'));
    }	
    
    /*
     *  subscribeAuthor is used to subscribe a author
     *  @param: int $author_id
     *  @param: int $subscriber_id
     */
    public function subscribeAuthor($author_id,$subscriber_id){
    	global $wpdb;
    	
    	$wpdb->insert($this->tbl_wpsa_subscribe_author,array('author_id' =>$author_id,'subscriber_id' =>$subscriber_id,'created_at'=>current_time('mysql', 1)),array('%d','%d','%s'));
   
    }

    /*
     *  unsubscribeAuthor is used to unsubscribe a author
     *  @param: int $author_id
     *  @param: int $subscriber_id
    */
    public function unsubscribeAuthor($author_id,$subscriber_id){
    	global $wpdb;
    	
    	$wpdb->query($wpdb->prepare("DELETE FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND subscriber_id = %d",$author_id,$subscriber_id));
 
    }
    
    /*
     *  getAllSubscribers will get he list of subscribers
     *  @param: int $author_id
     *  @param: int $subscriber_id
     */    
    public function getAllSubscribers($author_id){
    	global $wpdb;
    	
    	return $subscribers = $wpdb->get_results($wpdb->prepare( "SELECT subscriber_id FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND status = %s",$author_id,'active'));
    	
    }
        
	

}