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
	$sitemap=$c['nositemap'];
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
            if (!$img) {
                echo '<a href="'.Bootstrap::$main->tokens->page_href($t['id']).'">Na stronie '.$t['id'].' brakuje obrazka</a><br/>';
            } else {
                $img_file=$session['uimages_path'].'/'.$img;
                if (!file_exists($img_file)) {
                    echo '<a href="'.Bootstrap::$main->tokens->page_href($t['id']).'">Na stronie '.$t['id'].' brakuje obrazka</a><br/>';
                    $img='';
                } else {
                    $w=360;
                    $h=168;
                    $dstDir=$session['uimages_path'].'/wyprawy-results/'.dirname($img);
                    $dst_img=Tools::check_image(basename($img_file),dirname($img_file),$dstDir,$w,$h,0777,true,true);
                    $img=substr($dst_img,strlen($session['uimages_path']));
                }
            }
        //oferta wystepuje w wiecej niz jednym kraju
        $countries = explode(",", $country);
	//zapamietaj orginal
	$countryorg = $country; 
        foreach($countries AS $country)
        {
          //echo $coun;
	    $country = trim($country);
    	    $base=[
                   'page'=>$t['id'],
                   'continent'=>$continent,
		   'sitemap'=>$sitemap,
                   'country'=>$country,
		   'countryorg'=>$countryorg,
                   'name'=>$name,
                   'url'=>$t['file_name'],
                   'img'=>$img];
            
            $base['url']=preg_replace('/index.[a-z]+$/','',$base['url']);
            
            foreach($terms AS $term)
            {
                $trm=unserialize(base64_decode($term['d_xml']));
                $trm['_from']=strtotime($trm['d_from']);
                $trm['_to']=strtotime($trm['d_to']);
                
            	//wymagana data od 
                if (!$trm['_from']) continue;
		//jesli brak daty do to daj date od - wyprawa 1 dzien
		if (!$trm['d_to']) 
			$trm['_to'] = $trm['_from'];
		//else 
		//	if ($trm['_to']<time()) continue;
                
                
                $trm['alt']=$term['alt'];
                
                $db[]=array_merge($base,$trm);
                //jesli strona nie widoczna w mapie to ja pomin, aby nie pojawila sie w select kontynentu w wyszukiwarce
		if ($sitemap != 1) {
       		        if (!isset($struct[$continent][$country])) $struct[$continent][$country]=0;
                	$struct[$continent][$country]++;
		}
            }
        } //countries
            
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
