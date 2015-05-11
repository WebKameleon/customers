<?php

    
    $include=$KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    $ajax_path=$include.'/ajax/exhibition.php?current='.(0+$cos).'&kameleon='.($KAMELEON_MODE?urlencode(Bootstrap::$main->getRoot()):0).'&me='.urlencode($this->webpage['file_name']);
        
    Bootstrap::$main->tokens->set_wbp_js(array('grid.js'));

    
    $objects=WBP::get_file_db('objects');
    $wystawy_all=WBP::get_data('wystawy');
    
    $miasta=array();
    $obiekty=array();
    $wystawy=array();
    
    foreach($wystawy_all AS $w)
    {
        $w['object']=$objects[$w['object']];
        if (!$w['object']['nazwa']) continue;
        
        if ($w['object']['miasto']) $miasta[$w['object']['miasto']]=$w['object']['miasto'];
    
        $wystawy[$w['title'].$w['id']] = array('id'=>$w['id'],'title'=>$w['title']);
        $obiekty[$w['object']['nazwa'].$w['object']['miasto']]  =array('id'=>$w['object']['id'],'nazwa'=>$w['object']['nazwa'],'miasto'=>$w['object']['miasto']);
        
    }
    ksort($wystawy);
    ksort($miasta);
    ksort($obiekty);
    

    /*

    <form id="wbp_grid_form">
      
        <label class="wbp_grid_form_label">
        <select name="id">
            <option value="">Wybierz wystawę</option>
            <!-- loop:wystawy -->
            <option value="{id}">{title}</option>
            <!-- endloop:wystawy -->
        </select></label>
        <label class="wbp_grid_form_label">
        <select name="object">
            <option value="">Wybierz miejsce</option>
            <!-- loop:obiekty -->
            <option value="{id}">{nazwa}, {miasto}</option>
            <!-- endloop:obiekty -->            
            
        </select></label>
        <label class="wbp_grid_form_label">
        <select name="object_miasto">
            <option value="">Wybierz miasto</option>
            <!-- loop:miasta -->
            <option value="{loop}">{loop}</option>
            <!-- endloop:miasta -->  
        </select></label>
    </form>
    
    <div id="wbp_grid_template">
        <h4><a href="[href]">[title]</a></h4>
        <a href="javascript:" onclick="objmapid([object])"><strong>[object_miasto]</strong>, [object_nazwa]</a>
        <p>[from] &nbsp; - &nbsp; [to]</p>
        [trailer]
        [if:next_from]
            <br/>Najbliższe prezentacje dla:
            <a href="javascript:" onclick="objmapid([next_object_id])"><b>[next_object_nazwa]</b></a>
        [endif:next_from]
    </div>

    <div class="wbp_grid_navi">&nbsp;</div>
    <div id="wbp_grid_wyniki"></div>
    
    
    */
    
    include __DIR__.'/system/map.php';
?>
<script>
    
    window.onload = function () {
        wbp_grid('wbp_grid_form','wbp_grid_template','wbp_grid_wyniki',<?php echo $size?:10?>,'<?php echo $ajax_path?>',<?php echo $costxt?'true':'false'?>);
    };
    
    
</script>