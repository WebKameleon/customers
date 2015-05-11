var wbp_grid_limit;
var wbp_grid_offset=0;
var wbp_grid_form;
var wbp_grid_template;
var wbp_grid_results;
var wbp_grid_ajax;
var wbp_grid_lazyload=false;


function wbp_grid_load()
{
    var txt=$('#'+wbp_grid_form).serialize();
    var url=wbp_grid_ajax+'&limit='+wbp_grid_limit+'&offset='+wbp_grid_offset+'&'+txt;
    
    $.get(url,function (r) {
        var html=$('#'+wbp_grid_template).html();
        
        data=r.data;
        for(i=0;i<data.length;i++)
        {
            html2='<div style="display: none">'+smekta(html,data[i])+'</div>';
            $(html2).appendTo('#'+wbp_grid_results).fadeIn();
            wbp_grid_offset++;
            
        }
        var div='<div id="wbp_grid_scroll_to"></div>';
        
        if (r.total>wbp_grid_offset && wbp_grid_lazyload) $('#'+wbp_grid_results).append(div);
        
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
            navi+='<a href="javascript:wbp_grid_jump('+0+')">'+1+'</a></li>';
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
            navi+='<a href="javascript:wbp_grid_jump('+(i-1)*r.limit+')">'+i+'</a></li>';
        }

        if (to!=pages) {
            navi+='<li class="break">...</li>';
            navi+='<li class="last">';
            navi+='<a href="javascript:wbp_grid_jump('+(pages-1)*r.limit+')">'+pages+'</a></li>';
            
        }
        
        navi+='</ul>';
        if (pages==1) {
            navi='';
        }
        $('.wbp_grid_navi').html(navi);
    });   
}

function wbp_grid_jump(offset)
{
    wbp_grid_offset=offset;
    $('#'+wbp_grid_results).html('');
    wbp_grid_load();
}

function wbp_grid_scroll()
{
    var scroll_to = $('#wbp_grid_scroll_to');

    if (typeof(scroll_to.get(0))=='undefined') return; 
    
    var hT = scroll_to.offset().top,
        hH = scroll_to.outerHeight(),
        wH = $(window).height(),
        wS = $(window).scrollTop();
  
    if (wS > (hT+hH-wH)){
        $('#wbp_grid_scroll_to').remove();
        wbp_grid_load();
    }
}



function wbp_grid(form,template,results,limit,ajax,lazyload)
{
    wbp_grid_limit = limit;
    wbp_grid_ajax = ajax;
    wbp_grid_form = form;
    wbp_grid_template = template;
    wbp_grid_results = results;
    wbp_grid_lazyload = lazyload;
    
    $('#'+wbp_grid_form+' select').change(function() {
        wbp_grid_offset=0;
        $('#'+wbp_grid_results).html('');
        wbp_grid_load();
    });

    wbp_grid_load();

    $(window).scroll(wbp_grid_scroll);
    $('#preview-content').scroll(wbp_grid_scroll); //kameleon mode
}



