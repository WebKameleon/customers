if (typeof(calc_json)!='undefined') {
    var title=$('.calc').parent().parent().find('h1,h2,h3,h4').text();
    $('.calc').parent().parent().find('h1,h2,h3,h4').text('');
    if (title.length) {
        $('.calc .calc-title').text(title);
    }
    
    
}