/*
 * @package: Wp Subscribe Author
 * @Version: 1.0
 * @License: GPL2
 * @package type: Developement
 * @Author: Gowri sankar Ramasamy
*/

jQuery(function(){
    
    jQuery(".wp-subcribe-author-url").tipTip();

    
    jQuery(".wp-subcribe-author-url").click(function(){
	var follow_link = jQuery(this);
	var type;
	
	if(follow_link.hasClass('notsubscribed')){     
	    type = "subscribe"; // Subcribe  	       
	}
	else if(follow_link.hasClass('subscribed')){    
	    type = "unsubscribe"; // Unsubcribe    
	}
       
       jQuery.post(wpsa_ajax_suport.ajaxurl,({action:'wpsa_subscribe_author',type:type,author_id:follow_link.attr('data-author'),subscriber_id:follow_link.attr('data-subscriber')}),function(response){
				
				jQuery(".wp-subcribe-author-url").each(function(){
				
                                if(response==0){				   
				    if(jQuery(this).attr('data-author') == follow_link.attr('data-author')){
				      jQuery(this).removeClass('subscribed').addClass('notsubscribed').text("Subscribe");
				    }
				}
				else if(response==1) {
				    if(jQuery(this).attr('data-author') == follow_link.attr('data-author')){   
				      jQuery(this).removeClass('notsubscribed').addClass('subscribed').text("Unsubscribe");
				    }
				}
								    
				    
				    });
				
				    
					   // alert(response);
					    
	    });		 
    
     
    });   
    
});

