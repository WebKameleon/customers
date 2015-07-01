
$(function() {
    if (LANG=='pl') {
    	var monthNames = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
    	var dayNames = ["Ni", "Po", "Wt", "Śr", "Cz", "Pi", "So"];
    } else {
    	var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    	var dayNames = ["Su","Mo", "Tu", "We", "Th", "Fr", "Sa"];
    }
    
    
    
    $('.ptt_kalendarz').each(function () {
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

$('.kmw_article table').addClass('table table-responsive table-striped');
//$('.kmw_article  table:not([class]').addClass('table table-responsive table-striped');


if (typeof(ARTYSCI)!='undefined') {
    for (var i=0;i<ARTYSCI.length;i++) {
        var sel=".kmw_article :contains('"+ARTYSCI[i].nazwa+"'):not(:has('*')):not(a,script)";
        //console.log(sel);
        $(sel).html(function(_, html) {
            re=new RegExp('('+ARTYSCI[i].nazwa+')',"g");
            return html.replace(re, '<a href="'+ARTYSCI[i].href+'">$1</a>');
        });
    }
}
