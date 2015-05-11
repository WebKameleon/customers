
$(function() {
    var monthNames = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
    var dayNames = ["Po", "Wt", "Śr", "Cz", "Pi", "So", "Ni"];
    
    
    
    $('.ptt_kalendarz').each(function () {
        var url=$(this).attr('rel');

        $(this).calendar({
            months: monthNames,
            days: dayNames,
            req_ajax: {
                    type: 'get',
                    url: url
            }
        });
    });
        

});

$('.kmw_article table').addClass('table table-responsive table-striped');
//$('.kmw_article  table:not([class]').addClass('table table-responsive table-striped');