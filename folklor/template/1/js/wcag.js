
function wcag_tabindex() {
    let tabindex=1;

	console.log('TAB indexing');

    $('.skip-main').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('.logo img').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    $('.gsc-input input').attr('tabindex',tabindex++).attr('title','Wyszukiwarka w serwisie');
    
    $('.navbar .kmw_langs a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('.wcag button').attr('tabindex',tabindex++);
    
    
    $('#navbar>ul.navbar-nav>li>a').each (function(){
        
        if ($(this).closest('ul').hasClass('facebook'))
            return;
        $(this).attr('tabindex',tabindex++);
	
        $(this).parent().find('li').each(function(){
            $(this).attr('tabindex',tabindex++);
        });
    });
    
    $('#navbar>ul.navbar-nav.facebook').attr('tabindex',tabindex++);

    $('.bxslider-wrapper .bx-viewport li').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    $('#sidebar .article').each (function(){
        $(this).attr('tabindex',tabindex++);
    });

    
    $('.article-list').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    
    $('#content .kmw_article').each (function(){
        $(this).attr('tabindex',tabindex++);
        
        $(this).find('iframe').each(function(){
        	$(this).attr('tabindex',tabindex++);
        });
        
    });
    
    $('.gallery2 figure').each (function(){
        $(this).attr('tabindex',tabindex++);
        
    });
    
    
    
     $('#sidebar .kmw_article').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    
    //footer
    $('.footer-menu li a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
    $('#footer .footer-left a').each (function(){
        $(this).parent().attr('tabindex',tabindex++);
    });
    $('#footer .footer-center a').each (function(){
        $(this).parent().attr('tabindex',tabindex++);
    });
    $('#footer .footer-right a').each (function(){
        $(this).parent().attr('tabindex',tabindex++);
    });
    
    
   

}

setTimeout(wcag_tabindex,10000);
setTimeout(wcag_tabindex,1000);
