
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

    var hoverHTMLDemoAjax = '<p class="wpsa-authorInfo"></p><button>Subscribe</button>';
    var authorURL,authorID,$this;
	    
	    $("a[rel='author']").hovercard({
	        detailsHTML: hoverHTMLDemoAjax,
	        width: 350,
	        onHoverIn: function () {
	        	$this = $(this);
	        	 authorURL = $this.find("a[rel='author']").attr('href');
	        	 console.log(authorURL);
	        	 authorID = $.getAuthorID(authorURL);

	     		$.post(wpsa_ajax_suport.ajaxurl,({action:'wpsa_getauthor_action','authorID':authorID}),function(response){
	    	        
	     			$this.find(".wpsa-authorInfo").html(response);
	    	        
	    		});		        	 

	        }
	    });

});

