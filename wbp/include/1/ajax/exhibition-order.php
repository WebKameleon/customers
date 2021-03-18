<?php
    ini_set('display_errors',true);
    header('Content-type: application/json; charset=utf8');
    
    require_once __DIR__.'/../kameleon/Google.php';
    include_once __DIR__.'/../system/fun.php';

    $data=$_REQUEST;
    
    $teraz=date('Y-m-d,H:i');
    $now=time();

    if (!isset($data['sid']) || !$data['sid'])
    {
        die (json_encode(array('error'=>'Błąd')));
    }    
    
    $sid=$data['sid'];
    unset($data['sid']);
    
    $td_data=WBP::get_data($sid);

    if (!isset($data['institution']) || !$data['institution'])
    {
        die (json_encode(array('error'=>'Nie podano nazwy instytucji','obj'=>'institution')));
    }

    if (!isset($data['address']) || !$data['address'])
    {
        die (json_encode(array('error'=>'Nie podano numeru adresu','obj'=>'address')));
    }    
    
    if (!isset($data['phone']) || !$data['phone'])
    {
        die (json_encode(array('error'=>'Nie podano numeru telefonu','obj'=>'phone')));
    }

    if (!isset($data['email']) || !$data['email'])
    {
        die (json_encode(array('error'=>'Nie podano adresu e-mail','obj'=>'email')));
    }
    
    if (!isset($data['exhibition']) || !$data['exhibition'])
    {
        die (json_encode(array('error'=>'Nie podano wystawy','obj'=>'exhibition')));
    }

    if (!isset($data['since']) || !$data['since'])
    {
        die (json_encode(array('error'=>'Nie podano sugerowanego terminu wystawy','obj'=>'since')));
    }

    if (!isset($data['till']) || !$data['till'])
    {
        die (json_encode(array('error'=>'Nie podano sugerowanego terminu wystawy','obj'=>'till')));
    }

    $since=strtotime($data['since']);
    
    if ($since<$now)
    {
        die (json_encode(array('error'=>'Sugerowany początek wypożyczenia musi być w przyszłości','obj'=>'since')));
    }

    $till=strtotime($data['till']);
    if ($since>=$till)
    {
        die (json_encode(array('error'=>'Zakończenie wypożyczenia musi następować po rozpoczęciu','obj'=>'till')));
    }
    
    
    if (!isset($data['orderName']) || !$data['orderName'])
    {
        die (json_encode(array('error'=>'Nie podano imienia zamawiającego','obj'=>'orderName')));
    }
    if (!isset($data['orderSurname']) || !$data['orderSurname'])
    {
        die (json_encode(array('error'=>'Nie podano nazwiska zamawiającego','obj'=>'orderSurname')));
    }      
    
    $wystawy=WBP::get_data('wystawy');
    $title='';
    
    foreach($wystawy AS $w)
    {
        if ($w['id']!=$data['exhibition']) continue;
    
        $title=$data['title']=$w['title'];
        
        if ( $since<=$w['from'] && $till>=$w['from']  ||  $since<=$w['to'] && $since>=$w['from'] )
        {
            die (json_encode(array('error'=>'W podanym terminie wystawa jest już zarezerwowana','obj'=>'since')));
        }
    }
    
    
    @session_start();
           
   
    if (!isset($_SESSION['drive_access_token'])) 
	$_SESSION['drive_access_token']=$td_data['tokens']['drive'];
    $token=Google::setToken($_SESSION['drive_access_token']);    
    foreach($token AS $k=>$v) $td_data['tokens']['drive']->$k=$v;
    $_SESSION['drive_access_token']=$td_data['tokens']['drive'];
    
    
    session_write_close();
    
    // Drive part
    Google::setToken($_SESSION['drive_access_token']);
    $file=Google::getFile($td_data['drive']['id']);
    
    foreach($file['parents'] AS $parent)
    {
        if (!$parent['isRoot'])
        {
            $parent_id=$parent['id'];
            break;
        }
    }
    $parent_is_root=$parent['isRoot'];
    
    
    
    $export_link=$file['exportLinks']['text/html'];
    
    $html=Google::req($export_link);
    
    $data['now']=date('d-m-Y');
    
    foreach ($data AS $k=>$v) $html=str_replace("[$k]",$v,$html);
    
    $fname=$title.' - '.$data['institution'].' '.date('d.m.Y',$since).'-'.date('d.m.Y',$till);
    
    $file=Google::uploadFile($fname,'text/html',$html,$parent_id,true);
    
    
    if (isset($td_data['drive']['email']) && $td_data['drive']['email'])
    {
        WBP::mail($data['email'],$td_data['drive']['email'],$td_data['title'],$file['alternateLink']);
    }
    
    $resp=array('result'=>1,'error'=>null);
    
    
    die(json_encode($resp));
