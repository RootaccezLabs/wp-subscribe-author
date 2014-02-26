<?php

// Adding Frontend ajax support

function wpsa_getauthor_action(){

	add_action( 'wp_ajax_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );
        add_action( 'wp_ajax_noprev_wpsa_getauthor_action', 'wpsa_getauthor_action_handle' );  // need this to serve non logged in users

}
add_action( 'init', 'wpsa_getauthor_action' );

function wpsa_getauthor_action_handle(){
    
    die();
}