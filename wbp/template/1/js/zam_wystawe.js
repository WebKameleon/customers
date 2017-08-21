
var flatpickr_opt1={
    minDate: new Date().fp_incr(1),
    locale: 'pl'
}

var flatpickr_opt2={
    minDate: new Date().fp_incr(7),
    locale: 'pl'
}

flatpickr.l10ns.default.firstDayOfWeek = 1;

new flatpickr(document.getElementById('date_since'),flatpickr_opt1);
new flatpickr(document.getElementById('date_till'),flatpickr_opt2);  

window.onload = function() {
 
 
    var lh=location.href;
    lh=lh.split('?');
    if (typeof(lh[1])!='undefined') {
        var q=lh[1].split('&');
        for(i=0;i<q.length;i++)
        {
            var qq=q[i].split('=');
            if (qq[0]=='id') {
                $('#exhibition-order select[name="exhibition"]').val(qq[1]);    
            }
            
        }
    }
    
    
    $('#exhibition-order input,#exhibition-order textarea,#exhibition-order select').change(function () {
        $(this).removeClass('error');
        $('#exhibition-order .warning').fadeOut();
    });
    
    $('#exhibition-order .order-button').click(function() {
        
        var orderButton=$(this);
        orderButton.val('Zamawianie, proszę czekać ...').addClass('order-button-gray');
        
        $('#exhibition-order .warning').hide();
        var data=$('#exhibition-order').serialize();
        $.post(ajax_exhibition,data,function (resp) {
            orderButton.val('Zamów').removeClass('order-button-gray');
            if (resp.error!=null) {
                $('#exhibition-order .warning').html(resp.error).fadeIn();
                $('#exhibition-order .'+resp.obj).addClass('error');
            } else {
                exhibition_success();
            }
        });
        
    });

} 