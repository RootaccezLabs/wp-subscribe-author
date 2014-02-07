<?php

class Wpsa_Template {
	

	function __construct(){
		
                
	}
	
	
	
	/*
	 * Possible template names:
	 * 1.default
	 */
	public function renderTemplate($name='default',$param=""){
		
	 	 $dir = WPSA_PLUGIN_DIR . 'templates' . DIRECTORY_SEPARATOR;

		 $file = $dir.$name.'.php';
		$data = array();
		
	
		$data['data'] = $param;
				
		
		ob_start ();
		include_once $file;
	 	$output = ob_get_contents ();
		ob_end_clean ();
		
		return $output;
	}
	
	
	
}