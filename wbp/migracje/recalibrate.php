<?php


    $config=parse_ini_file(__DIR__.'/../template/1/config.ini');
    
    //print_r($config);
    
    
    $sql="SELECT * FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND widget='articlelist'";
    $q=$_SERVER['kameleon']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
	echo '['.$row['page_id'].']'.' '.$row['title']."\n";
        
        $data=unserialize(base64_decode($row['widget_data']));
        $data['thumb_width'] = $config['widgets.articlelist.thumb_width'];
	$data['thumb_height'] = $config['widgets.articlelist.thumb_height'];
        $sql="UPDATE webtd SET widget_data='".base64_encode(serialize($data))."' WHERE sid=".$row['sid'];
        $_SERVER['kameleon']['dbh']->exec($sql);
    }
    
    /*
    $sql="SELECT * FROM webtd WHERE server=".$_SERVER['kameleon']['server']." AND lang='".$_SERVER['kameleon']['lang']."' AND widget='gallery2'";
    $q=$_SERVER['kameleon']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
	echo '['.$row['page_id'].']'.' '.$row['title']."\n";
    
	$data=unserialize(base64_decode($row['widget_data']));
	print_r($data);
	break;
    }
    */