
function includeScript(scriptUrl) {
    var url=template_dir+'/'+scriptUrl;
    if (scriptUrl.substr(0,2)=='//') {
        url=scriptUrl;
    }
    //document.write('<script src="' + template_dir+'/'+scriptUrl + '"></script>');
    $('head').append('<script src="' + url + '"></script>');
}

function addCss(cssUrl) {
    var url=template_dir+'/'+cssUrl;
    if (cssUrl.substr(0,2)=='//') {
        url=cssUrl;
    }
    $('head').append('<link rel="stylesheet" type="text/css" href="'+url+'">');
}



function smekta(pattern,vars) {
    
    for (key in vars)
    {
        if (vars[key]==null)  vars[key]='';
        
        re=new RegExp('\\[if:'+key+'\\](.|[\r\n])+\\[endif:'+key+'\\]',"g");
        if (vars[key].length==0 || vars[key]==null) pattern=pattern.replace(re,'');
        
        re=new RegExp('\\['+key+'\\]',"g");
        pattern=pattern.replace(re,vars[key]);
        
        
        pattern=pattern.replace('[if:'+key+']','');
        pattern=pattern.replace('[endif:'+key+']','');
        
    }
    
    return pattern;

}

function search_choosen(args) {
    //console.log($('#search-www').prop('checked'));

    if ($('#search-www').prop('checked') && $('.gsc-control-searchbox-only').css('display')=='none' ) {
        $('#search-catalog-form').hide();      
        $('.gsc-control-searchbox-only input.gsc-input[name="search"]').val($('#search-catalog-form input[name="q"]').val());
        $('.gsc-control-searchbox-only').show();
        
        
    }
    if ($('#search-catalog').prop('checked') && $('#search-catalog-form').css('display')=='none' ) {
        $('.gsc-control-searchbox-only').hide();
        
        $('#search-catalog-form input[name="q"]').val($('.gsc-control-searchbox-only .gsc-input[name="search"]').val());
        $('#search-catalog-form').show();
    
        
    }

}

$('#search-www').click(search_choosen);
$('#search-catalog').click(search_choosen);

$('#search-catalog-form').submit(function () {
    $(this).find('input[name=plnk]').val('q__'+$(this).find('input[name=q]').val());
});


$('article.pg_faq :header').click(function() {
    $('article.pg_faq .kmw_article_plain').hide();
    $(this).siblings('.kmw_article_plain').fadeIn();
});

if(window.location.hash) {
    setTimeout(function(){
       $('a[name="'+decodeURI(window.location.hash).substr(1)+'"]').parent().find('div.kmw_article_plain').fadeIn();
    },1000)
  
} 


function wbp_articlelist_nav(a,letter)
{
    $('.articlelist-nav a').removeClass('active');
    $(a).addClass('active');
    

    
}

$('.pg_creator .kmw_articlelist:first,.pg_exhibition_list .kmw_articlelist:first').each(function() {
    
    $(this).parent().prepend('<ul class="articlelist-nav"><li class="active" rel="">A-Z</li></ul>');

    var letters=[];
    $('.kmw_articlelist :header a').each(function() {
        
        var letter=$(this).html().substr(0,1).toUpperCase();
        
        if (typeof(letters[letter])=='undefined' && letter!='„')
            $('.articlelist-nav').append('<li rel="'+letter+'">'+letter+'</li>');
        letters[letter]=true;
    });
    

    $('.articlelist-nav li').click(function() {
        $('.articlelist-nav li').removeClass('active');
        $(this).addClass('active');
        
        var letter=$(this).attr('rel');
        if (letter=='') {
            $('.kmw_articlelist').fadeIn();
        } else {
            $('.kmw_articlelist').hide();
            
            $('.kmw_articlelist :header a').each(function() {
                if ($(this).html().substr(0,1).toUpperCase()==letter) {
                    $(this).parent().parent().parent().fadeIn();
                }
            });
        }
    });
        
    
});

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}


$(document).ready(function ($) {
    
    $('.top .wcag button').click(function(){
        $('.wcag-contents').show();
        $('body').addClass('wcag-on');
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
        ".article": {
            "font-size": 14
        },
        ".navbar .navbar-nav > li > a":{
            "font-size": 16
        },
        ".dropdown-menu > li > a":{
            "font-size": 14
        },
        "#sidebar-left h1, #sidebar-left h2, #sidebar-left h3, #sidebar-left h4, #sidebar-left h5, #sidebar-left h6, #sidebar-left h7, #sidebar-left h8, #sidebar-left h9, #sidebar-right h1, #sidebar-right h2, #sidebar-right h3, #sidebar-right h4, #sidebar-right h5, #sidebar-right h6, #sidebar-right h7, #sidebar-right h8, #sidebar-right h9": {
            "font-size": 18
        },
        "#sidebar-right #side-nav .panel-heading a, #sidebar-left #side-nav .panel-heading a":{
            "font-size": 14
        },
        ".librarian-reader-zone > li > a":{
            "font-size": 16
        },
        ".articlelist-block": {
            "height": 361
        },
        ".articlelist-block h3": {
            "height": 41
        },
        ".articlelist-block h3 a": {
            "font-size": 16,
            "line-height": 15
        },
        ".articlelist-block .articlelist-block-date":{
            "font-size": 11
        },
        ".articlelist-block .articlelist-block-text":{
            "font-size": 12,
            "height": 112
        },
        ".articlelist-block a.articlelist-block-more":{
            "font-size": 11
        },
        "#main-can1 h1, #main-can1 h2, #main-can1 h3": {
            "font-size": 18
        },
        ".articlelist-statement h3 a": {
            "font-size": 16  
        },
        ".articlelist-statement .articlelist-statement-date": {
            "font-size": 11
        },
        ".articlelist-statement-text": {
            "font-size": 14
        },
        ".articlelist-statement-more": {
            "font-size": 12
        },
        "#main-can2 h1, #main-can2 h2, #main-can2 h3": {
            "font-size": 18
        },
        "ul#wbp_shop_results li p.title": {
            "font-size": 14,
            "height": 40
        },
        "ul#wbp_shop_results li p.author": {
            "font-size": 12
        },
        "ul#wbp_shop_results li a:last-child":{
            "font-size": 11
        },
        "#newsletter h1, #newsletter h2, #newsletter h3":{
            "font-size": 18
        },
        "#newsletter p":{
            "font-size": 11
        },
        "#newsletter input#freshmail_btn": {
            "font-size": 12
        },
        ".breadcrumbs": {
            "font-size": 11
        },
        ".article_like_list h1, .article_like_list h1 a": {
            "font-size": 16
        },
        ".article_like_list .kmw_article_plain": {
            "font-size": 12
        },
        ".magicmore": {
            "font-size": 11
        },
        ".kmw_articlelist_content h3, .kmw_articlelist_content h3 a":{
            "font-size": 16
        },
        ".kmw_articlelist_text":{
            "font-size": 12
        },
        ".kmw_articlelist_more": {
            "font-size": 11
        },
        ".carousel .article-list-indicators li a": {
            "font-size": 12,
            "width": 25
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
                css[l] = (fontDefault[k][l]+fontLevel*(l=='height'?10:2))+'px'; 
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
    
    
    var kmw_articlelist_grid=$('.kmw_articlelist_grid');
    var wows=$('.wow');
    

    if (wows.length>0) {
        
        includeScript('js/wow.js');
        addCss('css/animate.css');
        
        var kmw_articlelist_grid_list_toggle_click = function() {
            $(this).toggleClass('kmw_articlelist_grid_list_list');
            $(this).toggleClass('kmw_articlelist_grid_list_grid');
            
            if ($(this).hasClass('kmw_articlelist_grid_list_list')) {
                
                $('.kmw_articlelist_grid').hide();
                $('.kmw_articlelist_list').show();                
                
            } else{
                $('.wow').removeClass('animated');
                $('.kmw_articlelist_grid').show();
                $('.kmw_articlelist_list').hide();
            }
        }
        
    
        
        //$('.pg_exhibition_list .kmw_articlelist_grid_list_toggle').each(kmw_articlelist_grid_list_toggle_click);
        
        
        var grid_glasses=['fadeInLeft','fadeInDown','fadeInRight'];
        var i=0;
        $('.kmw_articlelist_grid .thumb-container').each(function(){
        
            $(this).addClass(grid_glasses[(i++)%3]);
        });
        $('.kmw_articlelist_grid_list_toggle').click(kmw_articlelist_grid_list_toggle_click);


	var runWow=function() {
		if (typeof(WOW)=='undefined') {
			setTimeout(runWow,200);
			return;
		}
                new WOW().init();
	}
	runWow();
        
    }
    
    
    
    $('.facebook .facebook-menu li').mouseenter(function(){

        $(this).find('div.txt').animate({'margin-right':0});
    });
        $('.facebook .facebook-menu li').mouseleave(function(){

        $(this).find('div.txt').animate({'margin-right':-200});
    })

	$(':required').on('change invalid',function(){
		var textfield = $(this).get(0);
		var title = $(this).attr('title');
		if (!textfield || !title)
			return;

		textfield.setCustomValidity('');
		if (!textfield.validity.valid) {
			textfield.setCustomValidity(title);
		}
	});

});


