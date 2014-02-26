
jQuery(function($){
    
    // todo: need to extract author id from : http://localhost/press2/?author=1
 
	    var hoverHTMLDemoAjax = '<p>xxx</p>';

	    $("a[rel='author']").hovercard({
	        detailsHTML: hoverHTMLDemoAjax,
	        width: 350,
	        onHoverIn: function () {
	            // set your twitter id
	            var user = 'gchokeen';

	            $.ajax({
	                url: 'https://twitter.com/statuses/user_timeline.json?screen_name=' + user + '&count=5&callback=?',
	                type: 'GET',
	                dataType: 'json',
	                beforeSend: function () {
	                    $("#demo-cb-tweets").prepend('<p class="loading-text">Loading latest  tweets...</p>');
	                },
	                success: function (data) {
	                    $("#demo-cb-tweets").empty();
	                    $('#twitter-username').text(user);
	                    $.each(data, function (index, value) {
	                        $("#demo-cb-tweets").append('<li>' + value.text + '</li>');
	                    });
	                },
	                complete: function () {
	                    $('.loading-text').remove();
	                }
	            });

	        }
	    });

});

