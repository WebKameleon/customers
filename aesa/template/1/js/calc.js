var calc_data=[];

function reCALCulate()
{    
    var _kat=$('.calc input[type="radio"]:checked').val();
    var _from=$('.calc select.from').val();
    var _to=$('.calc select.to').val();
    
    data=calc_data[_kat];
    for (i=0;i<data.length;i++)
    {
        if (data[i]['Zjazd']==_from) {
            $('.calc .price').html(data[i][_to]);
        }
    }
    
    
}


if (typeof(calc_json)!='undefined') {
    var title=$('.calc').parent().parent().find('h1,h2,h3,h4').text();
    $('.calc').parent().parent().find('h1,h2,h3,h4').text('');
    if (title.length) {
        $('.calc .calc-title').text(title);
    }
    
    $.getJSON(calc_json,function (data) {
        calc_data=data;
        $('.calc input').click(reCALCulate);
        $('.calc select').change(reCALCulate);
        $(reCALCulate);
    });
    
    
}