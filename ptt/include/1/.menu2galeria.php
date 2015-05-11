<?php
    if (!$page) return;

    $webtd=new webtdModel();
    $weblink=new weblinkModel();
    $tds=$webtd->getAll([$page]);
    
    
    
    if (isset($_GET['menu2galeria']))
    {
        $webtd->get($_GET['menu2galeria']);
        $links=$weblink->getAll($webtd->menu_id);
        $images=array();
        
        $uimages_path=Bootstrap::$main->session('uimages_path');
        
        $config=Bootstrap::$main->getConfig();
        
        $height=165;
        $width=825;
        $widget='gallery2';
        


        
        foreach($links AS $link)
        {

            $img=$link['imga']?:$link['img'];
            $sid=$link['sid'];
            
            $weblink->get($sid);
            $weblink->img=$img;
            $weblink->imga=null;
            $weblink->alt=$link['alt_title'];
            $weblink->save();
            
            $images[]=array(
                'sid'=>$sid,
                'url'=>'widgets/'.$widget.'/gfx/normal/'.$img,
                'title'=>$link['alt_title'],
            );
            
            $normal=$uimages_path.'/widgets/'.$widget.'/gfx/normal/'.$img;
            if (!file_exists($normal))
            {
                
                @mkdir(dirname($normal),0775,true);
                copy($uimages_path.'/'.$img,$normal);
            }
    
            $icon=$uimages_path.'/widgets/'.$widget.'/gfx/icon/'.$img;
            if (!file_exists($icon))
            {
                
                @mkdir(dirname($icon),0775,true);
                $w=0;
                Tools::check_image(basename($uimages_path.'/'.$img), dirname($uimages_path.'/'.$img), dirname($icon), $w, $height, 0777, true);
            }        
        
        
        }
        
        $widget_data=array(
            'images'=>json_encode($images),
            'menu_id'=>$webtd->menu_id,
            'effect'=>'fade',
            'width'=>$width,
            'thumb_height'=>$height,
            'slideshow_autostart'=>0,
        );        
        
        //mydie($images);
            
        $webtd->widget=$widget;
        $webtd->widget_data=base64_encode(serialize($widget_data));
        $webtd->save();
        echo '<script>location.href="'.$self.'";</script>';
    }
    
    
    foreach($tds AS $td)
    {
        if ($td['menu_id'] && ($td['widget']=='' || $td['widget']=='menu'))
        {
            $links=$weblink->getAll($td['menu_id']);
            echo '<a onclick="return confirm(\'Operacja nieodwaracalna, na pewno?\')" href="'.$self.'&menu2galeria='.$td['sid'].'">Przekształcenie menu "'.$links[0]['name'].'" &raquo; Galeria</a><br/>';
        }
    }
    
    
    