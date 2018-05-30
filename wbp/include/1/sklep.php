<?php
    if (!isset($KAMELEON_MODE) || $KAMELEON_MODE) $session['include_path']=$INCLUDE_PATH;
    
    $include=(isset($KAMELEON_MODE) && $KAMELEON_MODE)?$session['uincludes_ajax']:$session['include_path'];
    $ajax=$include.'/ajax/shoper.php?main_page';

?>


<script type="text/javascript">
  
window.onload = function() {
    $.get('<?php echo $ajax?>',function(data)
    {
        var html=$('#wbp_shop_template').html();
        
        for(i=0;i<data.length && i<<?php echo $size?:3;?>;i++)
        {
            html2='<li style="display: none">'+html+'</li>';
            for (key in data[i])
            {
                if (data[i][key]==null)  data[i][key]='';
                
                re=new RegExp('\\[if:'+key+'\\](.|[\r\n])+\\[endif:'+key+'\\]',"g");
                if (data[i][key].length==0 || data[i][key]==null) html2=html2.replace(re,'');
                
                re=new RegExp('\\['+key+'\\]',"g");
                html2=html2.replace(re,data[i][key]);
                
                
                html2=html2.replace('[if:'+key+']','');
                html2=html2.replace('[endif:'+key+']','');
                
            }

            $(html2).appendTo('#wbp_shop_results').fadeIn();

            
        }
        
    
    });
}
    
</script>
<?php return; ?>


<div id="wbp_shop_template">
    <img alt="[alt]" src="https://www.wbp.poznan.pl/inc/system/proxy.php?proxy=[img]" title="[alt]" />
    <p class="author">[author]</p>
    <p class="title">[title]</p>
    
</div>

<ul id="wbp_shop_results"></ul>
