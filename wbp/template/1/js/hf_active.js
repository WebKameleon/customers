var lh=location.href;
var lha=lh.split('?active=');
if (lha.length==2)
{
	$("a:contains('"+lha[1]+"')").parent().addClass('active');
}

$('#cse-search-form').hide();
$('.font-size').hide();
