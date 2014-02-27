<?php

// Adding Frontend ajax support

function wpsa_getauthor_action(){

	add_action( 'wp_ajax_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );
        add_action( 'wp_ajax_noprev_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );  // need this to serve non logged in users

}
add_action( 'init', 'wpsa_getauthor_action' );

function wpsa_getauthor_action_handle(){
	$authorID =  $_POST['authorID'];
	
	$UserDetails = get_user_meta($authorID);

	$allowed = array('first_name','last_name','nickname','description');
	?>
	<ul>
		<?php foreach ($UserDetails as $meta=>$value): ?>
		<?php if(in_array($meta, $allowed)): ?>
			<li class="<?php echo $meta; ?>"><?php echo $value[0]; ?></li>
			<?php endif; ?>
		<?php endforeach;?>
	</ul>
	<?php 
	
    die();
}