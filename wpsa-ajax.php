<?php

// Adding Frontend ajax support

function wpsa_ajax_suport(){

	add_action( 'wp_ajax_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );
    add_action( 'wp_ajax_noprev_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );  // need this to serve non logged in users

    add_action( 'wp_ajax_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );
    add_action( 'wp_ajax_noprev_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );  // need this to serve non logged in users
    
}
add_action( 'init', 'wpsa_ajax_suport' );

$wpsamodel =new Wpsa_Model();

/*
 * Action to pull the author information
 * Action Method: GET
 */
function wpsa_getauthor_action_handle(){

	global $wpsamodel;
		
	$authorID =  $_GET['authorID'];
	
	$UserDetails = get_user_meta($authorID);

	$allowed = array('first_name','last_name','nickname','description');
	?>
		<ul>
			<?php foreach ($UserDetails as $meta=>$value): ?>
		
			<?php  if(in_array($meta, $allowed)): ?>
				<li class="<?php echo $meta; ?>"><?php echo $value[0]; ?></li>
				<?php endif; ?>
			<?php endforeach;?>
		</ul>
	<?php 
	
    die();
}


/*
 * Action to subscribe or unsubscribe
 * Action Method: Post
 */
function wpsa_subscribe_author(){
	global $wpsamodel;
	
	$author_id = $_POST['author_id'];
	$subscriber_id = $_POST['subscriber_id'];
	 
	if($wpsamodel->is_user_subscribed($author_id, $subscriber_id)){
		//unsubscribe
		$wpsamodel->unsubscribeAuthor($author_id, $subscriber_id);
		echo "0";
	}
	else{
		//subscribe		
		$wpsamodel->subscribeAuthor($author_id, $subscriber_id);
		echo "1";
	}
	 
	die();
}

