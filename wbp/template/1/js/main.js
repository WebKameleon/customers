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
    $(this).find('input[name=plnk]').val('d__'+$(this).find('input[name=q]').val());
});


$('article.pg_faq :header').click(function() {
    $('article.pg_faq .kmw_article_plain').hide();
    $(this).siblings('.kmw_article_plain').fadeIn();
});


function wbp_articlelist_nav(a,letter)
{
    $('.articlelist-nav a').removeClass('active');
    $(a).addClass('active');
    

    
}

$('.pg_creator .kmw_articlelist:first').each(function() {
    
    $(this).parent().prepend('<ul class="articlelist-nav"><li class="active" rel="">A-Z</li></ul>');

    var letters=[];
    $('.pg_creator .kmw_articlelist :header a').each(function() {
        
        var letter=$(this).html().substr(0,1);
        
        if (typeof(letters[letter])=='undefined') $('.articlelist-nav').append('<li rel="'+letter+'">'+letter+'</li>');
        letters[letter]=true;
    });
    

    $('.articlelist-nav li').click(function() {
        $('.articlelist-nav li').removeClass('active');
        $(this).addClass('active');
        
        var letter=$(this).attr('rel');
        if (letter=='') {
            $('.pg_creator .kmw_articlelist').fadeIn();
        } else {
            $('.pg_creator .kmw_articlelist').hide();
            
            $('.pg_creator .kmw_articlelist :header a').each(function() {
                if ($(this).html().substr(0,1)==letter) {
                    $(this).parent().parent().parent().fadeIn();
                }
            });
        }
    });
        
    
});

$(document).ready(function ($) {
    
    $(".defaultFont").click(function () {
        $(".article").css("font-size", "14px");
        $(".navbar .navbar-nav > li > a").css("font-size", "16px");
        $(".dropdown-menu > li > a").css("font-size", "14px");
        $("#sidebar-left h1, #sidebar-left h2, #sidebar-left h3, #sidebar-left h4, #sidebar-left h5, #sidebar-left h6, #sidebar-left h7, #sidebar-left h8, #sidebar-left h9, #sidebar-right h1, #sidebar-right h2, #sidebar-right h3, #sidebar-right h4, #sidebar-right h5, #sidebar-right h6, #sidebar-right h7, #sidebar-right h8, #sidebar-right h9").css("font-size", "18px");
        $("#sidebar-right #side-nav .panel-heading a, #sidebar-left #side-nav .panel-heading a").css("font-size", "14px");
        $(".librarian-reader-zone > li > a").css("font-size", "16px");
        $(".articlelist-block h3 a").css("font-size", "16px");
        $(".articlelist-block .articlelist-block-date").css("font-size", "11px");
        $(".articlelist-block .articlelist-block-text").css("font-size", "12px");
        $(".articlelist-block a.articlelist-block-more").css("font-size", "11px");
        $("#main-can1 h1, #main-can1 h2, #main-can1 h3").css("font-size", "18px");
        $(".articlelist-statement h3 a").css("font-size", "16px");
        $(".articlelist-statement .articlelist-statement-date").css("font-size", "11px");
        $(".articlelist-statement-text").css("font-size", "14px");
        $(".articlelist-statement-more").css("font-size", "12px");
        $("#main-can2 h1, #main-can2 h2, #main-can2 h3").css("font-size", "18px");
        $("ul#wbp_shop_results li p.title").css("font-size", "14px");
        $("ul#wbp_shop_results li p.title").css("height", "40px");
        $("ul#wbp_shop_results li p.author").css("font-size", "12px");
        $("ul#wbp_shop_results li a:last-child").css("font-size", "11px");
        $("#newsletter h1, #newsletter h2, #newsletter h3").css("font-size", "18px");
        $("#newsletter p").css("font-size", "11px");
        $("#newsletter input#freshmail_btn").css("font-size", "12px");
        $(".breadcrumbs").css("font-size", "11px");
        $(".article_like_list h1, .article_like_list h1 a").css("font-size", "16px");
        $(".article_like_list .kmw_article_plain").css("font-size", "12px");
        $(".magicmore").css("font-size", "11px");
        $(".kmw_articlelist_content h3, .kmw_articlelist_content h3 a").css("font-size", "16px");
        $(".kmw_articlelist_text").css("font-size", "12px");
        $(".kmw_articlelist_more").css("font-size", "11px");
    
    });

    $(".increaseFont").click(function () {
        $(".article").css("font-size", "16px");
        $(".navbar .navbar-nav > li > a").css("font-size", "18px");
        $(".dropdown-menu > li > a").css("font-size", "16px");
        $("#sidebar-left h1, #sidebar-left h2, #sidebar-left h3, #sidebar-left h4, #sidebar-left h5, #sidebar-left h6, #sidebar-left h7, #sidebar-left h8, #sidebar-left h9, #sidebar-right h1, #sidebar-right h2, #sidebar-right h3, #sidebar-right h4, #sidebar-right h5, #sidebar-right h6, #sidebar-right h7, #sidebar-right h8, #sidebar-right h9").css("font-size", "20px");
        $("#sidebar-right #side-nav .panel-heading a, #sidebar-left #side-nav .panel-heading a").css("font-size", "16px");
        $(".librarian-reader-zone > li > a").css("font-size", "18px");
        $(".articlelist-block h3 a").css("font-size", "18px");
        $(".articlelist-block .articlelist-block-date").css("font-size", "13px");
        $(".articlelist-block .articlelist-block-text").css("font-size", "14px");
        $(".articlelist-block a.articlelist-block-more").css("font-size", "13px");
        $("#main-can1 h1, #main-can1 h2, #main-can1 h3").css("font-size", "20px");
        $(".articlelist-statement h3 a").css("font-size", "18px");
        $(".articlelist-statement .articlelist-statement-date").css("font-size", "13px");
        $(".articlelist-statement-text").css("font-size", "16px");
        $(".articlelist-statement-more").css("font-size", "14px");
        $("#main-can2 h1, #main-can2 h2, #main-can2 h3").css("font-size", "20px");
        $("ul#wbp_shop_results li p.title").css("font-size", "16px");
        $("ul#wbp_shop_results li p.title").css("height", "45px");
        $("ul#wbp_shop_results li p.author").css("font-size", "14px");
        $("ul#wbp_shop_results li a:last-child").css("font-size", "13px");
        $("#newsletter h1, #newsletter h2, #newsletter h3").css("font-size", "20px");
        $("#newsletter p").css("font-size", "13px");
        $("#newsletter input#freshmail_btn").css("font-size", "14px");
        $(".breadcrumbs").css("font-size", "13px");
        $(".article_like_list h1, .article_like_list h1 a").css("font-size", "18px");
        $(".article_like_list .kmw_article_plain").css("font-size", "16px");
        $("a.magicmore").css("font-size", "13px");
        $(".kmw_articlelist_content h3, .kmw_articlelist_content h3 a").css("font-size", "18px");
        $(".kmw_articlelist_text").css("font-size", "14px");
        $(".kmw_articlelist_more").css("font-size", "13px");
    });

    $(".increaseMoreFont").click(function () {
        $(".article").css("font-size", "18px");
        $(".navbar .navbar-nav > li > a").css("font-size", "20px");
        $(".dropdown-menu > li > a").css("font-size", "18px");
        $("#sidebar-left h1, #sidebar-left h2, #sidebar-left h3, #sidebar-left h4, #sidebar-left h5, #sidebar-left h6, #sidebar-left h7, #sidebar-left h8, #sidebar-left h9, #sidebar-right h1, #sidebar-right h2, #sidebar-right h3, #sidebar-right h4, #sidebar-right h5, #sidebar-right h6, #sidebar-right h7, #sidebar-right h8, #sidebar-right h9").css("font-size", "22px");
        $("#sidebar-right #side-nav .panel-heading a, #sidebar-left #side-nav .panel-heading a").css("font-size", "18px");
        $(".librarian-reader-zone > li > a").css("font-size", "20px");
        $(".articlelist-block h3 a").css("font-size", "20px");
        $(".articlelist-block .articlelist-block-date").css("font-size", "15px");
        $(".articlelist-block .articlelist-block-text").css("font-size", "16px");
        $(".articlelist-block a.articlelist-block-more").css("font-size", "15px");
        $("#main-can1 h1, #main-can1 h2, #main-can1 h3").css("font-size", "22px");
        $(".articlelist-statement h3 a").css("font-size", "20px");
        $(".articlelist-statement .articlelist-statement-date").css("font-size", "15px");
        $(".articlelist-statement-text").css("font-size", "18px");
        $(".articlelist-statement-more").css("font-size", "16px");
        $("#main-can2 h1, #main-can2 h2, #main-can2 h3").css("font-size", "22px");
        $("ul#wbp_shop_results li p.title").css("font-size", "18px");
        $("ul#wbp_shop_results li p.title").css("height", "50px");
        $("ul#wbp_shop_results li p.author").css("font-size", "16px");
        $("ul#wbp_shop_results li a:last-child").css("font-size", "15px");
        $("#newsletter h1, #newsletter h2, #newsletter h3").css("font-size", "22px");
        $("#newsletter p").css("font-size", "15px");
        $("#newsletter input#freshmail_btn").css("font-size", "16px");
        $(".breadcrumbs").css("font-size", "15px");
        $(".article_like_list h1, .article_like_list h1 a").css("font-size", "20px");
        $(".article_like_list .kmw_article_plain").css("font-size", "18px");
        $("a.magicmore").css("font-size", "15px");
        $(".kmw_articlelist_content h3, .kmw_articlelist_content h3 a").css("font-size", "20px");
        $(".kmw_articlelist_text").css("font-size", "16px");
        $(".kmw_articlelist_more").css("font-size", "15px");
    });
    
    
    var kmw_articlelist_grid=$('.kmw_articlelist_grid');
    

    if (kmw_articlelist_grid.length>0) {
        
        
        var kmw_articlelist_grid_list_toggle_click = function() {
            $(this).toggleClass('kmw_articlelist_grid_list_list');
            $(this).toggleClass('kmw_articlelist_grid_list_grid');
            
            if ($(this).hasClass('kmw_articlelist_grid_list_list')) {
                
                $('.kmw_articlelist_grid').hide();
                $('.kmw_articlelist_list').show();                
                
            } else{
                $('.kmw_articlelist_grid').show();
                $('.kmw_articlelist_list').hide();
            }
        }
        
    
        
        $('.pg_exhibition_list .kmw_articlelist_grid_list_toggle').each(kmw_articlelist_grid_list_toggle_click);
        
        
        var grid_glasses=['fadeInLeft','fadeInDown','fadeInRight'];
        var i=0;
        $('.kmw_articlelist_grid .thumb-container').each(function(){
        
            $(this).addClass(grid_glasses[(i++)%3]);
        });
        
        new WOW().init();
        
        
        $('.kmw_articlelist_grid_list_toggle').click(kmw_articlelist_grid_list_toggle_click);
        
    }
    
    

});


