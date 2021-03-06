<?php

    $editmode=$this->mode;
    $templ=Bootstrap::$main->session('template_dir');
    $weblink=new weblinkModel();

    
    
    $links=[];
    if ($this->webtd['menu_id']) {
        $links=$weblink->getAll($this->webtd['menu_id'],0)?:[];
        
        foreach ($links AS &$link) {
            if ($link['submenu_id']) {
                $link['menu']=$weblink->getAll($link['submenu_id'],0);
                
                foreach ($link['menu'] AS &$m) {
                    $m['href']=Bootstrap::$main->kameleon->href(trim($m['href']),$m['variables'],$m['lang_target'].':'.$m['page_target'],$page,$editmode);
                }
            }
            if ($link['img']) $link['img']=Bootstrap::$main->session('uimages').'/'.$link['img'];
            if ($link['imga']) $link['imga']=Bootstrap::$main->session('uimages').'/'.$link['imga'];
        
        
            $link['href']=Bootstrap::$main->kameleon->href(trim($link['href']),$link['variables'],$link['lang_target'].':'.$link['page_target'],$page,$editmode);
        }
       
        
    }
    
    $links_json=json_encode($links);
    $links_json=str_replace("'","\\'",$links_json);
    //mydie($links);
    
?>

<script>
    var folklor_map_opt='<?php echo $costxt;?>';
    var folklor_map_menu=JSON.parse('<?php echo $links_json;?>');
    var map_icons='<?php echo Bootstrap::$main->session('uimages');?>';
    var map_height_to_width_prc = <?php echo $size?:50?>;
    var map_zoom_out_icon='<?php echo $this->webtd['img'];?>';
    var map_zoom_out_threshold='<?php echo $this->webtd['cos']+0;?>';
    var map_lang='<?php echo $this->webtd['lang'];?>';
    
</script>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyA-jaLg2OSo62v-63z7BVL46av20PzQgKo"></script>
<script defer="defer" src="<?php echo $templ;?>/js/smartinfowindow.js"></script>
<script defer="defer" src="<?php echo $templ;?>/js/map.js"></script>

<div id="google-map" style="width: 100%; height: <?php echo $size?:500?>px;"></div>
