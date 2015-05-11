<?php
    
    Bootstrap::$main->tokens->set_wbp_js(array('https://maps.googleapis.com/maps/api/js?v=3.exp','maps.js'));
    $include=$KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    $obj_ajax_path=$include.'/ajax/obj.php';
    if (isset($obj_table)) $obj_ajax_path.='?obj='.$obj_table;
?>

<link rel="stylesheet" href="<?php echo $session['template_dir'];?>/css/obj.css" />

<div id="obj_details" style="display: none" title="" rel="<?php echo $obj_ajax_path?>">
    <div id="wbp-obj-map-canvas"></div>
    <div class="jakdojade"><a href="" target="_blank">Jak dojadę?</a></div>
    <div class="adres"></div>
     <div class="adres_template">
        Adres: <strong>[kod] [miasto], [ulica] [if:powiat](powiat [powiat])[endif:powiat]</strong><br /><br />
        Kontakt: <strong>[telefon][if:email]</strong>,<br />
        Email: <a href="mailto:[email]"><strong>[email]</strong></a>[endif:email][if:www],<br />
        <strong>www:</strong> <a href="http://[www]" target="_blank"><strong>[www]</strong></a>[endif:www]        
     </div>
</div>