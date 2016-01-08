<?php 

class Wpsa_Model{
	
	private $tbl_wpsa_subscribe_author;

	private $prefix;
        
	function __construct(){
		global $wpdb;
		
		$this->prefix = $wpdb->prefix;
		
		$this->tbl_wpsa_subscribe_author = $this->prefix.'wpsa_subscribe_author';
		$this->tbl_users = $this->prefix.'users';
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
     * @param: int $author_id 
     * @param: string $subscriber_email
     */
    public function is_user_subscribed_by_email($author_id,$subscriber_email){
    	global $wpdb;
    
    	$subscribe_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND subscriber_email = %s",$author_id,$subscriber_email) );
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
     *  subscribeAuthor is used to subscribe a author
     *  @param: int $author_id
     *  @param: string $subscriber_email
     */
    public function subscribeAuthorbyEmail($author_id,$subscriber_email){
    	global $wpdb;
    	 
    	$wpdb->insert($this->tbl_wpsa_subscribe_author,array('author_id' =>$author_id,'subscriber_email' =>$subscriber_email,'created_at'=>current_time('mysql', 1)),array('%d','%s','%s'));
    	 
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
     *  unsubscribeAuthorbyEmail is used to unsubscribe a author
     *  @param: int $author_id
     *  @param: int $subscriber_email
     */
    public function unsubscribeAuthorbyEmail($author_id,$subscriber_email){
    	global $wpdb;
    	
    	$wpdb->query($wpdb->prepare("DELETE FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND subscriber_email = %s",$author_id,$subscriber_email));
 
    }
    
    

    /*
     * unsubscribeMail is used to unsubscribe a author from by email
     *  @param: int $author_id
     *  @param: string $email_md5 
     * 
     */
    public function unsubscribeMail($author_id,$email_md5){
    	global $wpdb;
    
    	$wpdb->query($wpdb->prepare("DELETE FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND md5(subscriber_email) = %s",$author_id,$email_md5));
    	
    }
        
    /*
     *  getAllSubscribers will get he list of subscribers
     *  @param: int $author_id
     *  @param: int $subscriber_id
     */    
    public function getAllSubscribers($author_id){
    	global $wpdb;
    	
    	return $subscribers = $wpdb->get_results($wpdb->prepare( "SELECT subscriber_id,subscriber_email FROM $this->tbl_wpsa_subscribe_author WHERE author_id = %d AND status = %s",$author_id,'active'));
    	
    }
     
     /*
      * @method getAuthorIDbyNicename will extract the author id from user nicename
      * @param string $nicename
      */
     public function getAuthorIDbyNicename($nicename){
		global $wpdb;
	
		return $authorID = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $this->tbl_users WHERE user_nicename = %s ",$nicename));

	}  

	/*
	 * @method getFavouriteAuthors will extract the favourite author ids of subscriber
	 * @param int $subscriber_id
	 * @since 1.6.5
	 * @return object
	 */	
	public function getFavouriteAuthors($subscriber_id){		
		global $wpdb;
		
		return $authors = $wpdb->get_results($wpdb->prepare( "SELECT author_id FROM $this->tbl_wpsa_subscribe_author WHERE subscriber_id = %d AND status = %s",$subscriber_id,'active'));

	}
	

}