<?php
    

    
    if (isset($_POST['gen'][$sid]))
    {
        $webpage =new webpageModel();
        $sids=$webpage->sidsForFtp('',true,$session['server']['id'],$session['lang'],$session['ver']);
        $f=fopen(__DIR__.'/redirect.php','w');
        fwrite($f,'<?php'."\n\$redirect=array(");        
        foreach($sids AS $sid)
        {
            $wp=$webpage->get($sid);
            
            $file_name=preg_replace('/index\.(html|php)$/','',$wp['file_name']);
            
            if (trim($file_name)) fwrite($f,"\n\t".$wp['id']."=>'$file_name',");
            
        }
        fwrite($f,"\n);");
        fclose($f);
        
        register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
        });       
        
    }
    

?>

<form method="POST">
    
    
    <p><input type="submit" value="Wygeneruj przekierowania" name="gen[<?php echo $sid?>]"/></p>
</form>