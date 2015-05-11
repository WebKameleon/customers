<?php

    $include=(isset($KAMELEON_MODE) && $KAMELEON_MODE)?$session['uincludes_ajax']:$INCLUDE_PATH;
    $ajax_str=$include.'/ajax/structure.php';

    $wyprawy_q=array();
    foreach ($_GET AS $k=>$v)
    {
        if (substr($k,0,7)=='wyprawy') $wyprawy_q[substr($k,8)]=$v;
    }

    
    include __DIR__.'/system/wyprawy-form.html';

?>

<script>
    var struktura;

    jQueryKam(function($){
        $.get('<?php echo $ajax_str;?>', function (data) {
            struktura=data;
            wyprawy_form_fill(data,{
                continent:'<?php if (isset($wyprawy_q['continent'])) echo $wyprawy_q['continent'];?>',
                country:'<?php if (isset($wyprawy_q['country'])) echo $wyprawy_q['country'];?>',
                d_from:'<?php if (isset($wyprawy_q['d_from'])) echo $wyprawy_q['d_from'];?>',
                d_to:'<?php if (isset($wyprawy_q['d_to'])) echo $wyprawy_q['d_to'];?>',
                confirm:'<?php if (isset($wyprawy_q['confirm'])) echo $wyprawy_q['confirm'];?>',
                pilot:'<?php if (isset($wyprawy_q['pilot'])) echo $wyprawy_q['pilot'];?>'
            });
        });
        
        $('.form-wyprawy select[name="wyprawy.continent"]').change(function() {
            wyprawy_form_fill(struktura,{continent:$(this).val()});
        });
    });
</script>