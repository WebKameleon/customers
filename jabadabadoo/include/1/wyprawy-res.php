<?php

    $include=(isset($KAMELEON_MODE) && $KAMELEON_MODE)?$session['uincludes_ajax']:$INCLUDE_PATH;
    $ajax_str=$include.'/ajax/results.php?uimages='.urlencode($uimages);
    if (!isset($KAMELEON_MODE) || !$KAMELEON_MODE) $ajax_str.='&home_link='.urlencode($home_link);
    
    $flags=unserialize(base64_decode($costxt))?:[];
    $kafle=false;
    $hide_term=false;
    
    foreach($flags AS $k=>$v)
    {
        if ($k=='_kaf') {
            $kafle=true;
            continue;
        }
        if ($k=='_hide_term') {
            $hide_term=true;
            continue;
        }
        
        if ($v) $ajax_str.='&'.$k.'='.urlencode($v);
    }
    
    
    $wyprawy_q=array();
    foreach ($_GET AS $k=>$v)
    {
        if (substr($k,0,7)=='wyprawy') $wyprawy_q[substr($k,8)]=$v;
    }
    $wyprawy_q_req='';
    
    foreach ($wyprawy_q AS $k=>$v)
    {
        if ($wyprawy_q_req) $wyprawy_q_req.='&';
        $wyprawy_q_req.=$k.'='.urlencode($v);
    }
    

    if (!$kafle) include __DIR__.'/system/wyprawy-results.html';
    else include __DIR__.'/system/wyprawy-results-home.html';
    
    if (!$size) $size=9;
    
?>

<script>
    jQueryKam(function(){
        wyprawy_grid('wyprawy_form_id','wyprawy_results_template','wyprawy_results',<?php echo $size;?>,'<?php echo $ajax_str;?>',<?php echo $cos?'true':'false'; ?>,'<?php echo $wyprawy_q_req;?>');   
    });
</script>

<?php
    if (!$hide_term) return;
?>

<style>
    .term-price-line {display: none}
    .promo-subtitle {height:50px}
</style>

