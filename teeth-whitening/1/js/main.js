
var userStat = function(u) {
	if (!u.id) {
		$('.googlelogout').fadeOut(800);
		$('.googlelogin > a').each(function(){
			var tag=$(this).attr('tag');
			if (tag) $(this).html(tag);
		});
		$('.googlelogin > a').click(function(){
			WebKameleonAuth.GoogleAuth(userStat);		
			return false;
		});
	} else {
		$('.googlelogout').fadeIn(500);
		$('.googlelogin > a').each(function(){
			$(this).attr('tag',$(this).text());
			$(this).html(u.first_name);
		});
		$('.googlelogout > a').click(function(){
			WebKameleonAuth.GoogleLogout(userStat);		
			return false;
		});
	}
	
}

function googleauth() {
	WebKameleonAuth.GoogleUser(function(u){
		userStat(u);
		if (typeof(WebKameleonAuthReady)=='function') WebKameleonAuthReady(u);
	});
	
}

$(document).ready(function () {
    setTimeout(function(){
        $(document).trigger('scroll');
        
        $('.info-block .img-wr img,.info-block_mod .img-wr img').each(function(){
		if ($(this).height()<400) return;
            $(this).closest('div.info-block,div.info-block_mod').css({
                'padding-bottom':0,
                'height':$(this).height()+'px'
            });
    
        });
    
	$('.box-transp .img-wr').fadeOut(500);    
    },50);

	var login=$("li.googlelogin,a.googlelogin");

	if (login.length>0) {
		var s = document.createElement("script");
		s.type = "text/javascript";
		s.src = "http://auth.webkameleon.com/client.js?callback=googleauth";	
		$("head").append(s);
	}

    
})
