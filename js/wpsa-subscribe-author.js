
jQuery(function($){

	/*
	 * getAuthorID function used to extract the author id from author url
	*/
	
	$.getAuthorID = function(url) {
		var name = 'author';
	    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
	    if (!results) { 
	        return 0; 
	    }
	    return results[1] || 0;
	}
	
	/*
	 * hovercard will bring author information using ajax
	*/

    var hoverHTMLDemoAjax = '<p class="wpsa-authorInfo"></p>';
    var authorURL,authorID,$this;
	    
	    $("a[rel='author']").hovercard({
	        detailsHTML: hoverHTMLDemoAjax,
	        width: 350,
	        onHoverIn: function () {
	        	$this = $(this);
	        	 authorURL = $this.find("a[rel='author']").attr('href');
			 
	        	 authorID = $.getAuthorID(authorURL);
			 
			 if (authorID == 0 || typeof(authorID) =="undefined" ) {
				
				authorID = $this.attr('data-authorID');
			 }
		
			 
			 if (authorID == 0 || typeof(authorID) =="undefined") {
				// remove trailing slash from author url
				 if(authorURL.substr(-1) == '/') {					 
					authorURL = authorURL.substr(0, authorURL.length - 1);
					
				 }
				
				  
				authorID = authorURL.split('/').pop(); // nice name from url
			
			 }
			 		
			 
			

	     		$.get(wpsa_ajax_suport.ajaxurl,({action:'wpsa_getauthor_action','authorID':authorID}),function(response){
	    	        
	     			$this.find(".wpsa-authorInfo").html(response);
	    	        
	    		});		        	 
	     	
	        }
	    });
	    
	    	    
	    $(document).on('click','.wpsa-subscribe-btn',function(){	
	    	 var userID,authorID,$this,subscriber_email='0';
	    	 $this = $(this);
	    	 userID = $this.attr("data-userID");
	    	
	    	 /*
	    	  * @todo: email validation
	    	  */
	    	 
	    	 if(subscriber_email==0){ // unlogged in user
	    		 subscriber_email = $("#wpsa-subcriber-mail").val();
	    	 }
	    	
	    	
	  
	    	 authorID = $this.attr("data-authorID");
	    	 
	     	$.post(wpsa_ajax_suport.ajaxurl,({action:'wpsa_subscribe_author','author_id':authorID,'subscriber_id':userID,'subscriber_email':subscriber_email}),function(response){
	     		response = $.parseJSON(response);
	     		
	     		if(response.status == 2){
	     			$this.after(response.message).fadeOut('slow');
	     		}	     		
     			if(response.status==0){
    	        	$this.text("Subscribe");
    	        }
    	        else{
    	        	$this.text("Unsubscribe");
    	        }
	     	    	
	    	        
	    	});		
	    	
	    	
	    });
	    

});

