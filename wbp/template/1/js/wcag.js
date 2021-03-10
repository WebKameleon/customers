
function wcag_tabindex() {
    $('.skip-main').attr('tabindex',1);
    $('.gsc-input input').attr('tabindex',2);
    let tabindex=3;
    
    $('.main-menu>ul.navbar-nav>li>a').each (function(){
        $(this).attr('tabindex',tabindex++);
    });
}

setTimeout(wcag_tabindex,2000);
$(document).ready(wcag_tabindex);
