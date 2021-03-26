
function wcag_tabindex() {
    let tabindex=1;


    $('.skip-main').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('.top article.logo img').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('.search__container input.search__input').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    $('.backet.right').attr('tabindex',tabindex++);
    $('li.register a').attr('tabindex',tabindex++);
    $('li.login a').attr('tabindex',tabindex++);
    
    $('.main-menu>ul.navbar-nav>li>a').each (function(){
        $(this).attr('tabindex',tabindex++);

	
        $(this).parent().find('li').each(function(){
            $(this).attr('tabindex',tabindex++);
        });
    });

    
    $('ul.slides').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('.leftcol .innerbox ul li a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    $('.leftcol #box_search input').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    
    $('.leftcol div.box_custom').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    $('.leftcol #box_bestsellers').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    $('.leftcol #box_recent').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    
    $('.centercol .products').each (function(){
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
