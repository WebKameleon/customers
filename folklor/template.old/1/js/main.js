
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

