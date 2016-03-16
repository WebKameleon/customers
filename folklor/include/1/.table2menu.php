<?php

    $weblink=new weblinkModel();
   
    
    if (!$this->webtd['staticinclude'] || !$this->webtd['menu_id']) {
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->staticinclude=1;
        if (!$webtd->menu_id) {
            
            $webtd->menu_id=$this->webtd['menu_id']=$weblink->get_new_menu_id();
        }
        $webtd->save();
    }
    
    $wlinks=$weblink->getAll($this->webtd['menu_id'],0);
    
    
    $plain=$this->webtd['plain'];
    
    $a=[];
    
    preg_match_all('~<tr>[^<]*<td[^>]*>([^<]+)</td>[^<]*<td[^>]*>(.+?)</td>[^<]*</tr>~is',$plain,$a);
 
    foreach ($a[1] AS $i=>$country) {
        $when=str_replace('&nbsp;','',$a[2][$i]);
        $when=explode(',',$when);
        
        $link=null;
        foreach ($wlinks AS $wlink) {
            if ($wlink['alt']==$country) {
                $link=$wlink;
                break;
            }
        }

        if (!$link) $link=$weblink->add_link($this->webtd['menu_id'],'Wyjazdy',$country);
        
        $weblink2=new weblinkModel($link['sid']);
        if (!$weblink2->submenu_id) $weblink2->submenu_id=$submenu=$weblink->get_new_menu_id();
        else $submenu=$weblink2->submenu_id;
        if (!$weblink2->img) $weblink2->img='mapa/laleczka.png';
        $weblink2->save();
        
        $wlinks2=$weblink->getAll($submenu,0);
        
        foreach($when AS $year) {
            $pg=null;
            if (strstr($year,'<')) {
                $b=[];
                preg_match('~<a href="[^0-9]+([0-9]+)[^"]*"[^>]*>([^<]+)</a>~',$year,$b); 
                $pg=$b[1];
                $year=$b[2];
            }
            $link2=null;
            foreach($wlinks2 AS $wlink2) {
                if ($wlink2['alt']==$year) {
                    $link2=$wlink2;
                }
            }
            
            if (!$link2) $weblink->add_link($submenu,'Wyjazdy - '.$country,$year,$pg);
            else {
                $weblink2=new weblinkModel($link2['sid']);
                $weblink2->page_target=$pg;
                $weblink2->save();
            }
        }
 
    }
    
    
    
    
    