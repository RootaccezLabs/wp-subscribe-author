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

function change_avatar_css($class) {
	$class = preg_replace(":class='(.*.*)':", 'class="\1 hc-pic"', $class);
	//$class = str_replace("class='hc-pic avatar", 'class="avatar avatar-96 photo"', $class) ;
	return $class;
}

/*
 * Action to pull the author information
 * Action Method: GET
 */
function wpsa_getauthor_action_handle(){

	global $wpsamodel;
	
	$authorID =  $_GET['authorID'];
	
	if(!is_numeric($authorID)){
		$authorID = $wpsamodel->getAuthorIDbyNicename($authorID);
	}
	
	$UserDetails = get_user_meta($authorID);
	
	$user_id = get_current_user_id();
	
	
	$num_subscribers =  $wpsamodel->get_num_subscribers($authorID);
	
	$general_settings = (array)get_option('wpsa_general_settings');

	if(!empty($general_settings['show_on_card'])){
		$show_on_card = array_keys($general_settings['show_on_card']);
	}
	else{
		$show_on_card = array('first_name');
	}
	
	
	

	?>
			<?php 
		add_filter('get_avatar','change_avatar_css');		
		echo get_avatar($authorID);
		remove_filter('get_avatar','change_avatar_css');
		?>
		<ul style="list-style:none;">
			<?php foreach ($UserDetails as $meta=>$value): ?>
		
			<?php  if(in_array($meta, $show_on_card)): ?>
				<li class="<?php echo $meta; ?>"><?php echo $value[0]; ?></li>
				<?php endif; ?>
			<?php endforeach;?>
		</ul>

		<?php
		if($authorID != 0){
		?>
			<span><?php echo sprintf(_n('%1$s Subscriber','%1$s Subscribers',$num_subscribers,"wp-subscribe-author"),$num_subscribers); ?></span>
		<div class="wpsa-footer">
		<?php 
		
		if($user_id ==0){
	
		?>
		
		<input type="email" name="wpsa-subcriber-mail" id="wpsa-subcriber-mail" value="" placeholder="<?php echo __('Enter your email to subscribe with author','wp-subscribe-author') ?>"> 
		<button class="wpsa-subscribe-btn" data-authorID="<?php echo $authorID; ?>" data-userID="0"><?php echo __('Subscribe','wp-subscribe-author') ?></button>
	
		<?php 
		}	
		else if($user_id != $authorID){ 
			if($wpsamodel->is_user_subscribed($authorID, $user_id)){
				//unsubscribe
				$btn_txt = __('Unsubscribe','wp-subscribe-author');
		
			}
			else{
				//subscribe
				$btn_txt = __('Subscribe','wp-subscribe-author');

			}
			?>

			<button class="wpsa-subscribe-btn" data-authorID="<?php echo $authorID; ?>" data-userID="<?php echo $user_id; ?>" ><?php echo  $btn_txt; ?></button>
			
			
			
		<?php } ?>
			<div class="wpsa-message"></div>
		</div>
		
		<?php } ?>
		
	<?php 
	
    die();
}


/*
 * Action to subscribe/unsubscribe
 * Action Method: Post
 */
function wpsa_subscribe_author_handle(){
	global $wpsamodel;
	
	$author_id = $_POST['author_id'];
	$subscriber_id = $_POST['subscriber_id'];
	$subscriber_email = $_POST['subscriber_email'];
	
	
	if(is_user_logged_in()){
		// logged in user subscription 
		
		if($wpsamodel->is_user_subscribed($author_id, $subscriber_id)){
			//unsubscribe
			$wpsamodel->unsubscribeAuthor($author_id, $subscriber_id);
			echo json_encode(array('status'=>0,'message'=>'You have successfully unsubscribed!'));
		}
		else{
			//subscribe
			$wpsamodel->subscribeAuthor($author_id, $subscriber_id);
			echo json_encode(array('status'=>1,'message'=>'You have successfully subscribed!'));
		}

	}
	else{
		
		if($wpsamodel->is_user_subscribed_by_email($author_id, $subscriber_email)){
			
			if(!empty($_POST['doaction']) && $_POST['doaction'] == 'unsubscribe'){
				
				$wpsamodel->unsubscribeAuthorbyEmail($author_id, $subscriber_email);
				echo json_encode(array('status'=>0,'message'=>'You have successfully unsubscribed!'));
			die();
			}
			
			echo json_encode(array('status'=>2,'message'=>'You have already subscribed this author!'));

		}
		else{
			// subscribe process
			$wpsamodel->subscribeAuthorbyEmail($author_id, $subscriber_email);
			echo json_encode(array('status'=>1,'message'=>'You have successfully subscribed!'));
		}
		
		
		// unlogged in user subscription
		
	}
	 

	 
	die();
}

