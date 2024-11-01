var $ = jQuery;
jQuery(document).ready(function(){
        $(window).load(function() {
	var curr_url = window.location.href;
          document.getElementsByTagName("body")[0].onclick=function(){
           var cookies = get_cookies_array();
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "ab_visitor_click_count","curr_url":curr_url,'cookie_list':cookies}, // Sending data variation_id to post_word_count function.
                success: function(data){ // Show returned data using the function.      
                }
            });
	}
        });
});
function get_cookies_array() {
    var cookies = { };
    if (document.cookie && document.cookie != '') {
        var split = document.cookie.split(';');
        for (var i = 0; i < split.length; i++) {
            var name_value = split[i].split("=");
            name_value[0] = name_value[0].replace(/^ /, '');
            cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
        }
    }
    return cookies;
}