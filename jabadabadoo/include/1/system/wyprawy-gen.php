<?php

    $title_level=4;
    $menu_level=7;

    $webpage=new webpageModel();
    $webtd=new webtdModel($sid);
    $weblink=new weblinkModel();
    
    $db=array();
    $struct=array();
    
    $continents=$webpage->getChildren($this->webtd['next']);
    foreach($continents AS $c)
    {
        $continent=$c['title'];
        $struct[$continent]=array();
        $trips=$webpage->getChildren($c['id']);
        foreach ($trips AS $t)
        {
            $tds=$webtd->getAll([$t['id']]);
        
            $name='';
            $img='';
            $terms=[];
            foreach($tds AS $td)
            {
                if (!$name && $td['level']==$title_level)
                {
                    $name=$td['title'];
                    $country=trim(preg_replace('/<[^>]*>/','',$td['plain']));
                }
                if ($td['level']==$menu_level && $td['type']==666 && $td['menu_id']>0)
                {
                    $terms=$weblink->getAll($td['menu_id'],0);
                }
                
                if (!$img && ($td['widget']=='slideshow' || strstr($td['widget'],'gallery')))
                {
                    $gal=$weblink->getAll($td['menu_id'],0)?:[];
                    if (count($gal) && $gal[0]['img'] ) $img=$gal[0]['img'];
                }
            }
            
            if (!$img) $img='wyprawy/Maroko/maroko-005.jpg';
            
            
            

            
            $img_file=$session['uimages_path'].'/'.$img;
            $w=360;
            $h=168;
            $dstDir=$session['uimages_path'].'/wyprawy-results/'.dirname($img);
            $dst_img=Tools::check_image(basename($img_file),dirname($img_file),$dstDir,$w,$h,0777,true,true);
            $img=substr($dst_img,strlen($session['uimages_path']));
  
            $base=[
                   'page'=>$t['id'],
                   'continent'=>$continent,
                   'country'=>$country,
                   'name'=>$name,
                   'url'=>$t['file_name'],
                   'img'=>$img];
            
            $base['url']=preg_replace('/index.[a-z]+$/','',$base['url']);
            
            foreach($terms AS $term)
            {
                $trm=unserialize(base64_decode($term['d_xml']));
                $trm['_from']=strtotime($trm['d_from']);
                $trm['_to']=strtotime($trm['d_to']);
                
             
                  
                
                if (!$trm['_from'] || !$trm['_to'] || $trm['_to']<time()) continue;
                
                
                $trm['alt']=$term['alt'];
                
                $db[]=array_merge($base,$trm);
                
                if (!isset($struct[$continent][$country])) $struct[$continent][$country]=0;
                $struct[$continent][$country]++;
            }
            
        }
        
    }
    
    foreach($struct AS $k=>$v) if(!count($v)) unset($struct[$k]);
    
    $res=array('struct'=>$struct,'db'=>$db);
    

    
    file_put_contents(__DIR__.'/wyprawy.json',json_encode($res,JSON_NUMERIC_CHECK));
    register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
            $ftp->ftp_start('img','',false);
        });
