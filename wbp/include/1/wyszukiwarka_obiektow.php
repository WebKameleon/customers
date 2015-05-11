<?php

    $select=unserialize(base64_decode($costxt));
    
    $select['kategoria']=array();
    
    $kategorie=array();
    
    if (isset($select['kat'])) foreach ($select['kat'] AS $k=>$v)
    {
        if ($v)
        {
            $select['kategoria'][]=array('id'=>$k,'nazwa'=>$v);
            $kategorie[]=$k;
        }
    }
    
    $include=$KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    $ajax_path=$include.'/ajax/data.php?obj='.$select['table'];
    
    Bootstrap::$main->tokens->set_wbp_js(array('grid.js'));
    
    
    
    /*

    <form id="wbp_grid_form">
        <input type="hidden" name="kategorie" value="{kategorie}" />
        <label class="wbp_grid_form_label">
        <select name="kategoria">
            <option value="">Wybierz kategorię</option>
            <!-- loop:select.kategoria -->
            <option value="{id}">{nazwa}</option>
            <!-- endloop:select.kategoria -->
        </select></label>
        <label class="wbp_grid_form_label">
        <select name="powiat">
            <option value="">Wybierz powiat</option>
            <!-- loop:select.powiat -->
            <option value="{loop}">{loop}</option>
            <!-- endloop:select.powiat -->            
            
        </select></label>
        <label class="wbp_grid_form_label">
        <select name="miasto">
            <option value="">Wybierz miasto</option>
            <!-- loop:select.miasto -->
            <option value="{loop}">{loop}</option>
            <!-- endloop:select.miasto -->  
        </select></label>
    </form>

    
    <div id="wbp_grid_template">
        <h4><a href="javascript:" onclick="objmapid([id])">[nazwa]</a></h4>
        <strong>Adres:</strong> [kod] [miasto], [ulica] [if:powiat](powiat [powiat])[endif:powiat]<br />
        <strong>Kontakt:</strong> [telefon][if:email],
        <strong>email:</strong> <a href="mailto:[email]">[email]</a>[endif:email][if:www],
        <strong>www:</strong> <a href="http://[www]" target="_blank">[www]</a>[endif:www]
    </div>

    <div id="wbp_grid_wyniki"></div>


     
    */
    
    $obj_table=$select['table'];
    include __DIR__.'/system/map.php';
?>



<script>
    window.onload = function () {
        wbp_grid('wbp_grid_form','wbp_grid_template','wbp_grid_wyniki',<?php echo $size?:10?>,'<?php echo $ajax_path?>',<?php echo $cos?'true':'false'?>);
    };
</script>