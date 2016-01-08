
jQuery(function($){

	/*
	 * E-mail validation function
	 */
	var emailValidate = function(field) {
	    var filter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    if (!filter.test(field.val())) {
	    	field.focus;
	    	$(".wpsa-message").show().html("Please enter valid email!").delay('2000').fadeOut('slow');
	    	return false;
	    }
	    else{
	    	return true;
	    }
	 };
	    
	    
	
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
			 		
	        	 $this.find(".wpsa-authorInfo").html('loading...');
			

	     		$.get(wpsa_ajax_suport.ajaxurl,({action:'wpsa_getauthor_action','authorID':authorID}),function(response){
	    	        
	     			$this.find(".wpsa-authorInfo").html(response);
	    	        
	    		});		        	 
	     	
	     		//return false;
	        }
	    });
	    
	    	    
	    $(document).on('click','.wpsa-subscribe-btn',function(){
		
	    	 var userID,authorID,$this,subscriber_email=0,doaction;
	    	 $this = $(this);
	    	 userID = $this.attr("data-userID");
	    	 var message = $(".wpsa-message");
	    	 var emailField = $("#wpsa-subcriber-mail");
	    	
	    	 
	    	 if(userID==0){ // unlogged in user

	    		 if(emailValidate(emailField) !== false){
	    			 subscriber_email = emailField.val();	 
	    		 }
	    		 else{
	    			 return false;
	    		 }
 
	    	 }

	  
	    	 authorID = $this.attr("data-authorID");
		 doaction = $this.attr("data-doaction");
	    	 
	     	$.post(wpsa_ajax_suport.ajaxurl,({action:'wpsa_subscribe_author','author_id':authorID,'subscriber_id':userID,'subscriber_email':subscriber_email,'doaction':doaction}),function(response){
	     		response = $.parseJSON(response);
	     		
	     		if(response.status == 2){	     			
	     			message.html(response.message).show().delay('2000').fadeOut('slow');
	     		}
			
			if(response.status==0){
				$this.text("Subscribe");
				message.html(response.message).show().delay('2000').fadeOut('slow');
			}
			else{
				$this.attr('data-doaction','unsubscribe');
				$this.text("Unsubscribe");
				message.html(response.message).show().delay('2000').fadeOut('slow');
			}
	     	    	
	    	        
	    	});		
	    	
	    	
	    });
	    

});

