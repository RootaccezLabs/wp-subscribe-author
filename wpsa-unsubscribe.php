<?php

$wpsamodel =new Wpsa_Model();

function wpsa_unsubscribe(){

	global $wpsamodel;
	if(isset($_GET['wpsa_unsubscribe']) && isset($_GET['author'])){
		
		$wpsa_unsubscribe = $_GET['wpsa_unsubscribe'];
		$author = $_GET['author'];
		$wpsamodel->unsubscribeMail($author, $wpsa_unsubscribe);				
	/*
	 * @todo :  Successful unsubscribe notification to user by e-mail.
	 */
	}
	
    
}
add_action( 'init', 'wpsa_unsubscribe' );


