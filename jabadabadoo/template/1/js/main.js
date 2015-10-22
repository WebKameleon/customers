function wyprawy_form_fill(data,req) {


    $('.form-wyprawy select').empty().append('<option value="">Wybierz</option>');
    
    var countries=data.countries;
    for(var key in data.struct)
    {   
        $('.form-wyprawy select[name="wyprawy.continent"]').append('<option>'+key+'</option>');
        if (typeof(req.continent)!='undefined' && key==req.continent) {
            countries=data.struct[key].countries;
            $('.form-wyprawy select[name="wyprawy.continent"]').val(key);
        }
    }
    
    for (var i=0;i<countries.length;i++)
    {
        $('.form-wyprawy select[name="wyprawy.country"]').append('<option>'+countries[i]+'</option>');
        if (typeof(req.country)!='undefined' && req.country==countries[i]) {
            $('.form-wyprawy select[name="wyprawy.country"]').val(countries[i]);   
        }
    }
    
    if (typeof(req.d_from)!='undefined')
    {
        $('.form-wyprawy input[name="d_from"]').attr('data-value',req.d_from);
    }   
    if (typeof(req.d_to)!='undefined')
        $('.form-wyprawy input[name="d_to"]').attr('data-value',req.d_to);
        
    if (typeof(req.confirm)!='undefined' && req.confirm=='1')
        $('.form-wyprawy input[name="wyprawy.confirm"]').attr('checked',true);
        
    if (typeof(req.pilot)!='undefined' && req.pilot!='')
    {
        $('.form-wyprawy input[name="wyprawy.pilot"]').attr('checked',true);
    }
    
    $('.dpick').pickadate({
        format: 'dddd, dd mmm',
        formatSubmit: 'yyyy-mm-dd',
        hiddenPrefix: 'wyprawy.',
        hiddenSuffix: '',
        selectYears: false,
    });
}



function smekta(pattern,vars) {
    
    for (key in vars)
    {
        if (vars[key]==null)  vars[key]='';
        
        re=new RegExp('\\[if:'+key+'\\](.|[\r\n])+\\[endif:'+key+'\\]',"g");
        if (vars[key].length==0 || vars[key]==null || vars[key]=='0') pattern=pattern.replace(re,'');
        
        re=new RegExp('\\['+key+'\\]',"g");
        pattern=pattern.replace(re,vars[key]);
        
        
        pattern=pattern.replace('[if:'+key+']','');
        pattern=pattern.replace('[endif:'+key+']','');
        
    }
    
    return pattern;

}
//sortuj tablice objektow po wskazanym kluczu
function sortByKey(array, key) {
    return array.sort(function(a, b) {
        var x = a[key]; var y = b[key];
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    });
}

var wyprawy_grid_limit;
var wyprawy_grid_offset=0;
var wyprawy_grid_form;
var wyprawy_grid_template;
var wyprawy_grid_results;
var wyprawy_grid_ajax;
var wyprawy_grid_lazyload=false;


function wyprawy_grid_load(txt)
{
    if (txt==null) {
        txt=$('#'+wyprawy_grid_form).serialize();
        var lh=location.href;
        var pyt=lh.indexOf('?');
        if (pyt>0) lh=lh.substr(0,pyt);
        history.pushState('', 'Wyprawy', lh+'?'+txt);
    }
    
    var url=wyprawy_grid_ajax+'&limit='+wyprawy_grid_limit+'&offset='+wyprawy_grid_offset+'&'+txt;
    
    $.get(url,function (r) {
        var html=$('#'+wyprawy_grid_template).html();
        
        data=sortByKey(r.data, 'country');
        for(i=0;i<data.length;i++)
        {
            html2=smekta(html,data[i]);
            $(html2).appendTo('#'+wyprawy_grid_results).fadeIn();
            wyprawy_grid_offset++;
            
        }
        var div='<div id="wyprawy_grid_scroll_to"></div>';
        
        if (r.total>wyprawy_grid_offset && wyprawy_grid_lazyload) $('#'+wyprawy_grid_results).append(div);
        
        var navi='<ul>';
        var pages=Math.ceil(r.total/r.limit);
        var current_page=1+Math.ceil(r.offset/r.limit);

        
        var from=current_page-4;
        var to=current_page+5;
        while (from<=0)
        {
            from++;to++;
        }
        while (to>pages)
        {
            to--;
            if (from>1) from--;
        }
        
        
        if (from!=1) {
            navi+='<li class="first">';
            navi+='<a href="javascript:wyprawy_grid_jump('+0+')">'+1+'</a></li>';
            navi+='<li class="break">...</li>';
        }
        
        for (i=from; i<=to; i++)
        {
            var c='';
            if (i==current_page) c='active'
            if (i==1) {
                if (c.length>0) c+=' ';
                c+='first';
            }
            if (i==pages) {
                if (c.length>0) c+=' ';
                c+='last';
            }
            navi+='<li class="'+c+'">';
            navi+='<a href="javascript:wyprawy_grid_jump('+(i-1)*r.limit+')">'+i+'</a></li>';
        }

        if (to!=pages) {
            navi+='<li class="break">...</li>';
            navi+='<li class="last">';
            navi+='<a href="javascript:wyprawy_grid_jump('+(pages-1)*r.limit+')">'+pages+'</a></li>';
            
        }
        
        navi+='</ul>';
        if (pages==1) {
            navi='';
        }
        $('.wyprawy_grid_navi').html(navi);
        
        
        $('button.ask').click(function(){
 
           var name=encodeURIComponent($('p.place-name a').html());
           var termin=encodeURIComponent($(this).parent().parent().find('.p.d_from').text());
       
           window.open('http://form.jotformpro.com/form/51124013104938?nazwaImprezy='+name+'&termin='+termin, 'blank',          'scrollbars=yes,toolbar=no,width=700,height=800');
       });

       
       waluty_get();
        
        
    });   
}

function wyprawy_grid_jump(offset)
{
    wyprawy_grid_offset=offset;
    $('#'+wyprawy_grid_results).html('');
    wyprawy_grid_load(null);
}

function wyprawy_grid_scroll()
{
    var scroll_to = $('#wyprawy_grid_scroll_to');

    if (typeof(scroll_to.get(0))=='undefined') return; 
    
    var hT = scroll_to.offset().top,
        hH = scroll_to.outerHeight(),
        wH = $(window).height(),
        wS = $(window).scrollTop();
  
    if (wS > (hT+hH-wH)){
        $('#wyprawy_grid_scroll_to').remove();
        wyprawy_grid_load(null);
    }
}



function wyprawy_grid(form,template,results,limit,ajax,lazyload,req)
{
    wyprawy_grid_limit = limit;
    wyprawy_grid_ajax = ajax;
    wyprawy_grid_form = form;
    wyprawy_grid_template = template;
    wyprawy_grid_results = results;
    wyprawy_grid_lazyload = lazyload;
    
    if (lazyload) {
        $('#'+wyprawy_grid_form+' select,#'+wyprawy_grid_form+' input').change(function() {
            wyprawy_grid_offset=0;
            $('#'+wyprawy_grid_results).html('');
            wyprawy_grid_load(null);
        });
        
        $(window).scroll(wyprawy_grid_scroll);
        $('#preview-content').scroll(wyprawy_grid_scroll); //kameleon mode       
    }
    
    

    wyprawy_grid_load(req);


}


$('a.button-col').click(function() {
    var name=encodeURIComponent($('h1').html());
    var termin=encodeURIComponent($('.booking select option:selected').text());


    window.open('http://form.jotformpro.com/form/51124013104938?nazwaImprezy='+name+'&termin='+termin, 'blank','scrollbars=yes,toolbar=no,width=700,height=800');
    
});

$('a.button-col-hotel').click(function(){
    var name=encodeURIComponent($('h1').html());
    var termin=encodeURIComponent($('.booking .termin-hotel').text());
    
    window.open('http://form.jotformpro.com/form/51124013104938?nazwaImprezy='+name+'&termin='+termin, 'blank','scrollbars=yes,toolbar=no,width=700,height=800');
});




$(document).ready(function() {
   
   $('#merlin-results-here').html($('.merlin_results').html());
   $('#merlin-results-nav-here').html($('.merlin_nav').html());
   
   if ($(".merlin-hotel")[0]){
    $(".merlin_results").css("display", "none");
    $(".merlin_nav").css("display", "none");
   }   

        
     $('a.hotels-btn').click(function() {

        var name=encodeURIComponent($('h1').html());
        var termin=encodeURIComponent($(this).parent().parent().find('.different-date').text());
        
         
        window.open('http://form.jotformpro.com/form/51124013104938?nazwaImprezy='+name+'&termin='+termin, 'blank','scrollbars=yes,toolbar=no,width=700,height=800');
     })
     

     
   
   var loc=location.href;
   if (loc.indexOf('merlin.page')>0) {
       $("#tab1").removeClass("active in");
       $("#tab2").addClass("active in");
       $("#first-tab").removeClass("active");
       $("#second-tab").addClass("active");
   }
   

    
    
   
 
});


waluty_set = function(waluty) {
    if (typeof(waluty)=='string') waluty=JSON.parse(waluty);
    
    $('.super-price').each(function() {
        var rel=$(this).attr('rel');
        var cena=parseFloat($(this).text());

/*        if (typeof(waluty[rel])=='undefined' || rel.length==0 || rel=='{currency}' || rel=='[currency]') {
            rel='EUR';
        }
*/

//console.log(cena+': '+rel);
       if (cena>0 && rel!='PLN') {

//console.log(waluty);
//console.log (waluty[rel.toLowerCase()]);

            if (parseFloat(waluty[rel.toLowerCase()])>0) {
                
                $(this).attr('title',cena+' '+rel);
                cena=Math.round(parseFloat(waluty[rel.toLowerCase()])*cena);
                $(this).html(cena);
		$(this).parent().find('.price-currency').html('PLN');
            	$(this).attr('rel','PLN');    
            }
            //$(this).attr('rel','PLN');    
        }
    });
}


function printFunction() {
    window.print();
}

