<?php

    $webtd=new webtdModel($sid);
    $webtd->staticinclude=1;
    $webtd->save();
    
    $weblink=new weblinkModel();
    
    $tds=$webtd->getAll([$page]);
    
    foreach($tds AS $td)
    {
        if (!$td['menu_id'] || $td['level']!=$webtd->level) continue;
        $webtd2=new webtdModel($td['sid']);
        $webtd2->type=666;
        $webtd2->save();
        
        
        $links=$weblink->getAll($td['menu_id']);
        
        foreach($links AS &$link) {
            $weblink->d_xml($link);
            
            if (isset($link['d_from']) && $link['d_from'])
            {

                $weblink2=new weblinkModel($link['sid']);
                $weblink2->alt=$link['nego']?:date('d.m',strtotime($link['d_from'])) .' - '. date('d.m',strtotime($link['d_to']));
                $weblink2->name=$this->webpage['title'];
                $weblink2->hidden = strtotime($link['d_from'])>time() || (strtotime($link['d_to'])>time() && !$link['confirm']) ? 0 : 1;
                $weblink2->save();
            }
        }
        
    }
    
