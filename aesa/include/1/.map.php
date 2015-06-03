<?php

    if (isset($_REQUEST['map_doc_id'][$sid]))
    {
        
        $user=Bootstrap::$main->session('user');
        $tokens=json_decode($user['access_token'],true);
        $scopes=array('spreadsheets');
    
        foreach ($scopes AS $scope)
            if (!isset($tokens[$scope]) || !$tokens[$scope])
                Bootstrap::$main->redirect('scopes/'.$scope.'?setreferpage='.$page);
        
        
        $webtd=new webtdModel($sid);
        $costxt=$webtd->costxt=$_REQUEST['map_doc_id'][$sid];
        $webtd->save();
        
        $wrksh=Spreadsheet::listWorksheets($costxt);
        $data=[];
        foreach ($wrksh AS $id=>$w) $data[$w['title']]=Spreadsheet::getWorksheet($costxt,$id,'',true);
        
        @mkdir(__DIR__.'/map',0755);
        file_put_contents(__DIR__.'/map/'.$costxt.'.json',json_encode($data,JSON_NUMERIC_CHECK));
        register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
        });
    }
?>
<form class="google-map-form">
    <input type="text" placeholder="google spreadsheet id" name="map_doc_id[<?php echo $sid;?>]" value="<?php echo $costxt;?>"/>
    <input type="submit" value="import" class="button"/>
</form>