
jQuery(function($) {
    if (LANG=='pl') {
    	var monthNames = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
    	var dayNames = ["Ni", "Po", "Wt", "Śr", "Cz", "Pi", "So"];
    } else {
    	var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    	var dayNames = ["Su","Mo", "Tu", "We", "Th", "Fr", "Sa"];
    }


    $('.folklor_kalendarz').each(function () {
        var url=$(this).attr('rel');

        $(this).calendar({
            months: monthNames,
            days: dayNames,
            weekStart: 1,
            req_ajax: {
                    type: 'get',
                    url: url
            }
        });
    });
    
});



$('.popup').click(function() {
	$(this).fadeOut(2000);
}).fadeIn(2000);



function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

$(document).ready(function ($) {
    
    $('.navbar .wcag button').click(function(){
        $('.wcag-contents').show();
        $('body').addClass('wcag-on');
        if (typeof(kmw_slideshowArray)!=='undefined' && kmw_slideshowArray.length>0)
            kmw_slideshowArray[0].slider.stopAuto();
    });
    
    $('.wcag-contents .wcag').click(function(){
        $('.wcag-contents').hide();
        $('body').removeClass('wcag-on');
    });
    
    var fontLevel = 0;

    function wcagCookie() {
        var cl=($('body').attr('class')||'').split(' ');
        for (let i=0; i<cl.length; i++)
           if (cl[i].indexOf('wcag')===-1)
            cl.splice(i--,1);
        document.cookie = 'wcag='+(cl.join(' '))+','+fontLevel+';path=/';
    }
    
    $('.wcag-contents .high-contrast-bw').click(function(){
        $('body').toggleClass('wcag-contrast-1');
        wcagCookie();
    });
    
    
    var fontDefault = {
        ".kmw_article_text": {
            "font-size": 14
        },
        ".navbar .navbar-nav > li > a":{
            "font-size": 16
        },
        ".dropdown-menu > li > a":{
            "font-size": 14
        },
        "#content h2, #content h3, #content h4, #content h5, #content h6, #content h7, #content h8, #content h9, #sidebar-right h1, #sidebar-right h2, #sidebar-right h3, #sidebar-right h4, #sidebar-right h5, #sidebar-right h6, #sidebar-right h7, #sidebar-right h8, #sidebar-right h9": {
            "font-size": 18
        },
        "#sidebar h5,#sidebar h4,#sidebar h3,#sidebar h2,#sidebar h1":{
            "font-size": 24
        },
        ".sidebar-menu a": {
            "font-size": 14
        },
        ".article-list .h1 a,.article-list .h2 a, .article-list .h3 a, .article-list .h4 a,.article-list .h5 a": {
            "font-size": 16
        },
        ".article-list .read-more":{
            "font-size": 12
        },
        ".article-list .articlelist-block-date":{
            "font-size": 11
        },
        
        "#content h1": {
            "font-size": 36
        },
        ".footer-menu a": {
            "font-size": 11
        }
        
    }
    
    function adjustFont(x) {
        if (x) 
            fontLevel+=x;
        else
            fontLevel = 0;
        
        
        if (parseInt(fontLevel)!==0) 
            $('body').addClass('wcag-on');
        else
            $('body').removeClass('wcag-on');
        
        wcagCookie();
        for (var k in fontDefault) {
            var css={};
            for (let l in fontDefault[k]) {
                css[l] = (fontDefault[k][l]+fontLevel*(l=='height'?5:2))+'px'; 
            }
        
            $(k).css(css);
        }
    }
    
    var wcagC=getCookie('wcag');
    if(wcagC) {
        var wcagA=wcagC.split(',');
        var wcagShow=false;
        if (wcagA[0].length>1) {
            $('body').addClass(wcagA[0]);
            wcagShow=true;
        }
        if (wcagA[1]) {
            adjustFont(parseInt(wcagA[1]));
            if (parseInt(wcagA[1])!==0) {
                wcagShow=true;
            }
            
        }
        
        if (wcagShow) {
            $('.wcag-contents').show();
        }
        
    
    }
    
    
    
    $(".defaultFont").click(function () {
        adjustFont();
    });

    $(".decreaseFont").click(function () {
        adjustFont(-1);
    });
    
    $(".increaseFont").click(function () {
        adjustFont(1);
    });
    
    $(".increaseMoreFont").click(function () {
        adjustFont(2);
    });
    

    $(document).on('keydown', '#navbar', function(e) { 
  		var keyCode = e.keyCode || e.which; 
		
  		if (keyCode == 9) { 
			$('.navbar .wcag button').trigger('click');			
  		} 
	});
	$(document).on('keydown', '.bxslider-wrapper', function(e) { 
  		var keyCode = e.keyCode || e.which; 
		
        if (keyCode === 9 && typeof(kmw_slideshowArray)!=='undefined' && kmw_slideshowArray.length>0) {
            kmw_slideshowArray[0].slider.stopAuto();
            kmw_slideshowArray[0].slider.goToNextSlide();
  		} 
	});
});

