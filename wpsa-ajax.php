<?php

// Adding Frontend ajax support

function wpsa_ajax_suport(){

	add_action( 'wp_ajax_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );
    add_action( 'wp_ajax_nopriv_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );  // need this to serve non logged in users

    add_action( 'wp_ajax_wpsa_subscribe_author', 'wpsa_subscribe_author_handle' );
    add_action( 'wp_ajax_nopriv_wpsa_subscribe_author', 'wpsa_subscribe_author_handle' );  // need this to serve non logged in users
    
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
	
	$user_id = get_current_user_id();


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
		if($user_id ==0){
		?>
		<input type="email" name="wpsa-subcriber-mail" id"wpsa-subcriber-mail" value="" placeholder="<?php echo __('Enter your email to subscribe with author','wp-subscribe-author') ?>"> <button class="wpsa-subscribe-btn"><?php echo __('Subscribe','wp-subscribe-author') ?></button>
		<?php 
		}	
		else if($user_id != $authorID){ ?>
			<button class="wpsa-subscribe-btn"><?php echo __('Subscribe','wp-subscribe-author') ?></button>
		<?php } ?>
	<?php 
	
    die();
}


/*
 * Action to subscribe or unsubscribe
 * Action Method: Post
 */
function wpsa_subscribe_author_handle(){
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

