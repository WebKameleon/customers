var wbp_url_prefix;
var wbp_active_suffix;



function wbp_inject(part)
{
  $.ajax({
    url: wbp_url_prefix+part+'.js',
    dataType: 'jsonp',
    jsonpCallback: part+'back',
    cache: 'true',
    success: function (ret) {
      
      var website=ret[0];
      var data=ret[1];
      
      var re=/href="http/g;
      data=data.replace(re,'kajhskjsfsdjfvyegrdjfhgdf3423dwefer');
      
      re=/src="/g;
      data=data.replace(re,'src="'+website);
      
      re=/href="/g;
      data=data.replace(re,'href="'+website);
      
      re=/kajhskjsfsdjfvyegrdjfhgdf3423dwefer/g;
      data=data.replace(re,'href="http');
      
      if (part=='header') $('body').prepend(data);
      else $('body').append(data);
    }
  });
}

function wbp_active()
{
  if ($("a:contains('"+wbp_active_suffix+"')").length==0)
  {
    setTimeout(wbp_active,300);
    return;
  }
  $("a:contains('"+wbp_active_suffix+"')").parent().addClass('active');
}


function wbp_hf()
{
  $('script').each (function() {
    var src=$(this).attr('src')+'';
    srca=src.split('hf.js?active=');
    if (srca.length==2) {
      wbp_active_suffix=srca[1];
      wbp_url_prefix=srca[0].substr(0,srca[0].length-3);
      wbp_inject('header');
      wbp_inject('footer');
      wbp_active();
    }
  });  
  
}



if (typeof $ == "undefined") {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js";
	script.onload = wbp_hf;
	document.getElementsByTagName("head")[0].appendChild(script);
} else {
	$(document).ready(wbp_hf);
}
