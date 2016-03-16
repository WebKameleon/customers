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
    
    if (count($wlinks)) return;
    
    $plain=$this->webtd['plain'];
    
    $a=[];
    
    preg_match_all('~<tr>[^<]*<td[^>]*>([^<]+)</td>[^<]*<td[^>]*>(.+?)</td>[^<]*</tr>~is',$plain,$a);
 
    foreach ($a[1] AS $i=>$country) {
        $when=str_replace('&nbsp;','',$a[2][$i]);
        $when=explode(',',$when);
        
        $link=$weblink->add_link($this->webtd['menu_id'],'Wyjazdy',$country);
        
        $weblink2=new weblinkModel($link['sid']);
        $weblink2->submenu_id=$submenu=$weblink->get_new_menu_id();
        $weblink2->img='mapa/laleczka.png';
        $weblink2->save();
        
        
        
        foreach($when AS $year) {
            $pg=null;
            if (strstr($year,'<')) {
                $b=[];
                preg_match('~<a href="[^0-9]+([0-9]+)[^"]*"[^>]*>([^<]+)</a>~',$year,$b); 
                $pg=$b[1];
                $year=$b[2];
            }
            $weblink->add_link($submenu,'Wyjazdy - '.$country,$year,$pg);
        
        }
 
    }
    
    
    
    
    