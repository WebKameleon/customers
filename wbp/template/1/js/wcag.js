
function wcag_tabindex() {
    let tabindex=1;


    $('.skip-main').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('article.logo img').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('.kmw_langs a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('.wcag button').attr('tabindex',tabindex++);
    $('.gsc-input input').attr('tabindex',tabindex++).attr('title','Wyszukiwarka w serwisie');
    
    $('.main-menu>ul.navbar-nav>li>a').each (function(){
        $(this).attr('tabindex',tabindex++);

	
        $(this).parent().find('li').each(function(){
            $(this).attr('tabindex',tabindex++);
        });
    });

    $('#banner .bx-viewport li').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('#sidebar .article').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('.carousel.slide').each (function(){
        $(this).find('.carousel-inner .item.active .articlelist-block').each(function(){
        	$(this).attr('tabindex',tabindex++);
        });
        
        $(this).find('.container-fluid').attr('tabindex',tabindex++).attr('title','Strony');
        
        $(this).find('.container-fluid li a').each(function(){
        	$(this).attr('tabindex',tabindex++);
        });
    });
    
    $('.kmw_articlelist_list').each (function(){
        $(this).find('.kmw_articlelist_nav ul li a').each(function(){
        	$(this).attr('tabindex',tabindex++);
        });
        $(this).find('.kmw_articlelist .kmw_articlelist_content').each(function(){
        	$(this).attr('tabindex',tabindex++);
        });
    });
    
    
    $('.article .kmw_article_content').each (function(){
        $(this).attr('tabindex',tabindex++);
        
    });
    
    $('.gallery2 figure').each (function(){
        $(this).attr('tabindex',tabindex++);
        
    });
    
    

    $('#wbp_shop_results li').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
     $('aside #side-nav .panel a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    //footer
    $('#footer p').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('#footer .menu-simple .img-list').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    
   

}

setTimeout(wcag_tabindex,10000);
$(document).ready(wcag_tabindex);
