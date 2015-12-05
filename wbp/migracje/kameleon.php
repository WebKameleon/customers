<?php

require $kameleon_prefix.'/application/classes/Kameleon.php';
require $kameleon_prefix.'/application/classes/Image.php';
require $kameleon_prefix.'/application/classes/Tools.php';


/*
 *
 linki do sklepów:
 
 http://www.folklor.pl/index.php?mode=towary&action=main&menu=4&category=2007&lang=PL
 */





if (!defined('UIMAGES_TOKEN')) define('UIMAGES_TOKEN', 'ea8f11151a1d58cef6210fa5fc20b7db');
if (!defined('UFILES_TOKEN')) define('UFILES_TOKEN', 'x2c23796969a66cb491d74e39ac136ca');
if (!defined('INSIDELINE_TOKEN')) define('INSIDELINE_TOKEN', 'ba05863b65eefe6fa534c354bb49df6d');


function kameleon_page_exists($id)
{
    $sql="SELECT count(*) FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND page_id=$id";

    $e=false;
    $q=$_SERVER['kameleon']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
        $e=$row[0];
    }
    
    return $e;
}

function kameleon_pagekey($id,$pagekey)
{
    $sql="UPDATE webpage SET pagekey='$pagekey' WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND id=$id";

    $_SERVER['kameleon']['dbh']->exec($sql);
    
}

function kameleon_page($id,$title,$prev,$type,$title_short='')
{
        
    $sql="DELETE FROM webcat WHERE tdsid IN (SELECT sid FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND page_id=$id)";
    $_SERVER['kameleon']['dbh']->exec($sql);
    
    
    $sql="DELETE FROM webpage WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND id=$id;
            DELETE FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND page_id=$id;";
            
    $_SERVER['kameleon']['dbh']->exec($sql);
    
    
    $sql="INSERT INTO webpage (server,ver,lang,id,title,prev,type,title_short) VALUES (?,?,?,?,?,?,?,?)";
    $q=$_SERVER['kameleon']['dbh']->prepare($sql);
    
    $v=array($_SERVER['kameleon']['server'],$_SERVER['kameleon']['ver'],$_SERVER['kameleon']['lang'],$id,$title,$prev,$type,$title_short);
    
    if (!$q->execute($v)) print_r($_SERVER['kameleon']['dbh']->errorInfo());

}

function kameleon_get_page($id)	
{
    $sql="SELECT * FROM webpage WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND id=$id";
    $q=$_SERVER['kameleon']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
	return $row;
    }

}


function kameleon_article($page,$title,$plain,$cats,$date,$date2='0000',$pri=1,$cos=null,$costxt=null,$html='',$class='',$trailer='',$att='')
{
    
    $pregex='~^<a[^>]+href="([^"]+)(jpeg|jpg|gif|png)"[^>]*><img[^>]+></a>~i';
    $bgimg='';
    if (preg_match($pregex,$plain,$pm))
    {
	
	if (substr($pm[1],0,7)!='http://' || substr($pm[1],0,24)=='http://www.folklor.pl')
	{
	
	    $plain=preg_replace($pregex,"\\1\\2".UIMAGES_TOKEN,$plain);
	    
	    $p=explode(UIMAGES_TOKEN,$plain);
	    $plain=$p[1];
	    
	    $p[0]=str_replace('http://www.folklor.pl/','',$p[0]);
		
		$p[0]=str_replace('//','/',$p[0]);
	    
	    $bgimg=download($p[0]);
    
	    $icon=$_SERVER['kameleon']['uimages'].'/widgets/article/gfx/icon/'.$bgimg;
	    if (!file_exists($icon))
	    {
			@mkdir(dirname($icon),0775,true);
			$w=$_SERVER['kameleon']['article_bgimg_w'];
			$h=$_SERVER['kameleon']['article_bgimg_h'];
			Tools::check_image(basename($_SERVER['kameleon']['uimages'].'/'.$bgimg), dirname($_SERVER['kameleon']['uimages'].'/'.$bgimg), dirname($icon), $w, $h, 0775, true);
	    }
	}
    
        
    }
    
    
    $plain=process_plain($plain);
    
    $sql="INSERT INTO webtd (server,ver,lang,page_id,title,level,widget,plain,nd_custom_date,nd_custom_date_end,pri,bgimg,cos,costxt,html,class,trailer) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q=$_SERVER['kameleon']['dbh']->prepare($sql);
    
    $d1=strstr($date,'0000')?0:strtotime($date);
    $d2=strstr($date2,'0000')?0:strtotime($date2);
    
    $v=array($_SERVER['kameleon']['server'],$_SERVER['kameleon']['ver'],$_SERVER['kameleon']['lang'],$page,$title,$_SERVER['kameleon']['level'],'article',$plain,$d1,$d2,$pri,$bgimg,$cos,$costxt,$html,$class,$trailer);
    
    //print_r($v);
    
    if (!($q->execute($v))) {
        print_r($_SERVER['kameleon']['dbh']->errorInfo());
        return false;
    }
    
    
    $sql="SELECT max(sid) FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND page_id=$page";
    
    $q=$_SERVER['kameleon']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
        $sid=$row[0];
    }
    
    if ($html)
    {
	$sql="UPDATE webtd SET staticinclude=1,ob=3 WHERE sid=$sid";
	$_SERVER['kameleon']['dbh']->exec($sql);
	
    }
    
    foreach($cats AS $cat)
    {
        $sql="INSERT INTO webcat (server,tdsid,category) VALUES (".$_SERVER['kameleon']['server'].",$sid,'$cat')";
        $_SERVER['kameleon']['dbh']->exec($sql);
    }
    
    
    if ($att)
    {
		$attachment=download('getfile.php?file='.$att);
		$sql="UPDATE webtd SET attachment='$attachment' WHERE sid=$sid";
		$_SERVER['kameleon']['dbh']->exec($sql);
    }
    
}



function process_plain($plain)
{
    $plain=preg_replace('~^<br[^>]*>~','',$plain);
    $plain=str_replace('href="http://www.folklor.pl/files','href="'.UFILES_TOKEN,$plain);
    $plain=str_replace('href="files','href="'.UFILES_TOKEN,$plain);
    $plain=str_replace('http://www.folklor.pl/index.php','index.php',$plain);
    $plain=str_replace('href="?mode=','href="index.php?mode=',$plain);
    $plain=str_replace('src="http://www.folklor.pl/','src="/',$plain);
    
    $plain=str_replace('rel="lightbox[images]"','fancybox="1"',$plain);

    $start=INSIDELINE_TOKEN.'begin';
    $end=INSIDELINE_TOKEN.'endotron';
    

    $plain=preg_replace('~'.UFILES_TOKEN.'/([^"]+)"~',UFILES_TOKEN.'/'.$start.'\\1'.$end.'"',$plain);
    while ($pos=strpos($plain,$start)) {
        
        
        $file=substr($plain,$pos+strlen($start));
        $endpos=strpos($file,$end);
        if (!$endpos) break;
        
        $file=substr($file,0,$endpos);
   
        $local=download($file,'files/','ufiles');
		
   
        $plain=str_replace($start.$file.$end,$local,$plain);
    }

    
    $plain=preg_replace('~href="index\.php\?mode=([a-zA-Z_]+)[^"]*id=([0-9]+)[^"]*"~','href="'.$start.'\\1'.INSIDELINE_TOKEN.'\\2'.$end.'"',$plain);
    
    while ($pos=strpos($plain,$start)) {
        
        
        $plain2=substr($plain,$pos+strlen($start));
        $endpos=strpos($plain2,$end);
        if (!$endpos) break;
        
        $plain2=substr($plain2,0,$endpos);
        
        $para=explode(INSIDELINE_TOKEN,$plain2);

		if ($para[0]=='katalogi')
		{
			$kat=katalogi($para[1]);
			
			$dir='/katalogi/'.Kameleon::str_to_url($kat['kategoria']);
			$ufile_dir=$_SERVER['kameleon']['ufiles'].$dir;
			$ext=end(explode('.',$kat['plik']));
			$file=Kameleon::str_to_url($kat['nazwa'],-1).'.'.$ext;
			
			if (!file_exists("$ufile_dir/$file"))
			{
			$tmp=file_get_contents('http://www.folklor.pl/getfile.php?file='.$kat['plik']);
			file_put_contents("$ufile_dir/$file",$tmp);
			}
			@mkdir($ufile_dir,0775,true);
			$plain=str_replace($start.$para[0].INSIDELINE_TOKEN.$para[1].$end,UFILES_TOKEN.$dir.'/'.$file,$plain);
		}
        else {
	    if (!isset($_SERVER['plus'][$para[0]]))
	    {
		die("Unknown mode=".$para[0]."\n");
	    }
	    
	    $plain=str_replace($start.$para[0].INSIDELINE_TOKEN.$para[1].$end,"kameleon:link(".($para[1]+$_SERVER['plus'][$para[0]]).")",$plain);
	    
	    if ($_SERVER['kameleon']['wbp_resursive'])
	    {
		global $argv;
		$cmd="php ".$argv[0]." -m $para[0] -i $para[1]";
		system($cmd);
	    }
	}
       
    }
    
    
    $plain=preg_replace('~(<img [^>]*src=")([^"]+)("[^>]*>)~i','\\1'.$start.'\\2'.$end.'\\3',$plain);
    
    
    while ($pos=strpos($plain,$start)) {
        
        
        $img=substr($plain,$pos+strlen($start));
        $endpos=strpos($img,$end);
        if (!$endpos) break;
    
        $img=substr($img,0,$endpos);
              
        if (substr($img,0,7)!='http://' && substr($img,0,9)!='http%3A//') $dst=UIMAGES_TOKEN.'/'.download($img);
		else $dst=$img;
        
        $plain=str_replace($start.$img.$end,$dst,$plain);
    }
    
    
    
    return $plain;
    
}


function kameleon_galery($menu,$page_id,$title='')
{
    if (!count($menu)) return;
    
    
    $plus=$_SERVER['kameleon']['article_galery_plus'];
    $widget=$_SERVER['kameleon']['article_galery'];
    $width=$_SERVER['kameleon']['article_galery_w'];
    $height=$_SERVER['kameleon']['article_galery_h'];
    
    
    $menu_id=$menu[0]['menu_id']+$plus;
    
    $sql="DELETE FROM weblink WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND menu_id=".$menu_id;
    $_SERVER['kameleon']['dbh']->exec($sql);    
    
    $images=array();
    
    foreach($menu AS $m)
    {
        
        $img=download('files/galeria/big/'.$m['GaleriaGrafika']);
        
        $sql="INSERT INTO weblink (server,ver,lang,menu_id,name,alt,img) VALUES (?,?,?,?,?,?,?)";
        $q=$_SERVER['kameleon']['dbh']->prepare($sql);

        $v=array($_SERVER['kameleon']['server'],$_SERVER['kameleon']['ver'],$_SERVER['kameleon']['lang'],$menu_id,mb_substr('Gal.'.$m['name'],0,32,'utf8'),$m['GaleriaTytul'],$img);
    
        //print_r($v);
    
        if (!($q->execute($v))) {
            print_r($_SERVER['kameleon']['dbh']->errorInfo());
            return false;
        }
        
        $sql="SELECT max(sid) FROM weblink WHERE server=".$_SERVER['kameleon']['server']." AND menu_id=$menu_id";
        
        $q=$_SERVER['kameleon']['dbh']->query($sql);
        if ($q) foreach ($q AS $row ){
            $sid=$row[0];
        }
        
        
        $images[]=array(
            'sid'=>$sid,
            'url'=>'widgets/'.$widget.'/gfx/normal/'.$img,
            'title'=>$m['GaleriaTytul'],
        );
        
        $normal=$_SERVER['kameleon']['uimages'].'/widgets/'.$widget.'/gfx/normal/'.$img;
        if (!file_exists($normal))
        {
            
            @mkdir(dirname($normal),0775,true);
            copy($_SERVER['kameleon']['uimages'].'/'.$img,$normal);
        }

        $icon=$_SERVER['kameleon']['uimages'].'/widgets/'.$widget.'/gfx/icon/'.$img;
        if (!file_exists($icon))
        {
            
            @mkdir(dirname($icon),0775,true);
            $w=0;
            Tools::check_image(basename($_SERVER['kameleon']['uimages'].'/'.$img), dirname($_SERVER['kameleon']['uimages'].'/'.$img), dirname($icon), $w, $height, 0777, true);
        }
        
    }
    
    $widget_data=array(
        'images'=>json_encode($images),
        'menu_id'=>$menu_id,
        'effect'=>'fade',
        'width'=>$width,
        'thumb_height'=>$height,
        'slideshow_autostart'=>0,
    );
    
    
    $sql="INSERT INTO webtd (server,ver,lang,page_id,title,level,widget,menu_id,pri,widget_data) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $q=$_SERVER['kameleon']['dbh']->prepare($sql);
    
    $v=array($_SERVER['kameleon']['server'],$_SERVER['kameleon']['ver'],$_SERVER['kameleon']['lang'],$page_id,$title,$_SERVER['kameleon']['level'],$widget,$menu_id,2,base64_encode(serialize($widget_data)));
    
    //print_r($v);
    
    if (!($q->execute($v))) {
        print_r($_SERVER['kameleon']['dbh']->errorInfo());
        return false;
    }
    
}




function download($src_file,$src_prefix='',$dst_dir='uimages')
{
    $base64prefix='data:image/';

	$src_file=str_replace('//','/',$src_file);
	if ($src_file[0]=='/') $src_file=substr($src_file,1);
	
    
    if (strstr($src_file,'getfile.php?'))
    {
		$dst_dir='ufiles';
	
		$src='http://www.folklor.pl/'.$src_file;
		$dst='x-archiwum/'.end(explode('file=',$src_file));
		
		$src=str_replace('//','/',$src);
		$dst=str_replace('//','/',$dst);
		
		
		if (!file_exists($_SERVER['kameleon'][$dst_dir].'/'.$dst))
		{
			echo "Downloading $src -> ";
			$f=file_get_contents($src);
			$dir=dirname($_SERVER['kameleon'][$dst_dir].'/'.$dst);
			@mkdir($dir,0775,true);
			file_put_contents($_SERVER['kameleon'][$dst_dir].'/'.$dst,$f);
			echo $_SERVER['kameleon'][$dst_dir].'/'.$dst."\n";
		}
	
    }
    elseif (substr($src_file,0,strlen($base64prefix))==$base64prefix)
    {
		$src_file=substr($src_file,strlen($base64prefix));
		$pos=strpos($src_file,';');
		
		$ext=substr($src_file,0,$pos);
		$src_file=substr($src_file,$pos);
		$pos=strpos($src_file,',');
		$src_file=substr($src_file,$pos+1);
	
		
		$img=base64_decode($src_file);
		
		$file_name=md5($img).'.'.$ext;
		$dir=$_SERVER['kameleon'][$dst_dir].'/x-archiwum/base64';
		@mkdir($dir,0775,true);
		
		$dst='x-archiwum/base64/'.$file_name;
		
		file_put_contents($_SERVER['kameleon'][$dst_dir].'/'.$dst,$img);
    }
    else
    {
    
		
		$src = $src_prefix.$src_file;
		
		$src=str_replace('/',UIMAGES_TOKEN,$src);
		$src=str_replace(' ',INSIDELINE_TOKEN,$src);
		$src=urlencode($src);
		$src=str_replace(UIMAGES_TOKEN,'/',$src);
		$src=str_replace(INSIDELINE_TOKEN,'%20',$src);
		
		$src='http://www.folklor.pl/'.$src;
			
		$dst=$src_file;
		
		$dst=preg_replace('~^files/~','',$dst);
		$dst=preg_replace('~^uimages/[12]/~','archiwum/',$dst);
		
		
		$dst='x-archiwum/'.$dst;
		
		for ($i=0;$i<4;$i++)
		{
			$dst=str_replace(' /','/',$dst);
			$dst=str_replace('/ ','/',$dst);
		}
		
		
		$dst=preg_replace('/\.+/','.',$dst);
		
		$dst=str_replace('/',UIMAGES_TOKEN,$dst);
		$dst=Kameleon::str_to_url($dst);
		$dst=str_replace(UIMAGES_TOKEN,'/',$dst);
		
		$dst=str_replace('.jpg.jpg','.jpg',$dst);
		$dst=str_replace('.png.png','.png',$dst);
		
		
		
		
		if (!file_exists($_SERVER['kameleon'][$dst_dir].'/'.$dst))
		{
			echo "Downloading $src -> ";
			$f=file_get_contents($src);
			$dir=dirname($_SERVER['kameleon'][$dst_dir].'/'.$dst);
			@mkdir($dir,0775,true);
			file_put_contents($_SERVER['kameleon'][$dst_dir].'/'.$dst,$f);
			echo $_SERVER['kameleon'][$dst_dir].'/'.$dst."\n";
		}
    
    }
    
    return $dst;
        
}