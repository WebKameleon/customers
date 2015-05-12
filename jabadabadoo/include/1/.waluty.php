<?php
    $waluty=file_exists(__DIR__.'/system/waluty.json')?json_decode(file_get_contents(__DIR__.'/system/waluty.json'),1):[];

    if (isset($_POST['waluty']) && is_array($_POST['waluty']))
    {
        foreach($_POST['waluty'] AS $k=>$v)
        {
            if (strlen($k)==3) $waluty[$k]=str_replace(',','.',$v);
            if ($k=='_' && $v) $waluty[$v]=1;
            if ($v==0) unset($waluty[$k]);
        }
        
        file_put_contents(__DIR__.'/system/waluty.json',json_encode($waluty,JSON_NUMERIC_CHECK));
        register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
        });
        
    }
    
    //mydie($waluty);
?>

<form method="POST">
<?php foreach ($waluty AS $k=>$v):?>
<?php echo $k;?>=<input type="text" value="<?php echo $v;?>" name="waluty[<?php echo $k;?>]"  style="color:black; width:50px"/>
<?php endforeach;?>
&nbsp; | &nbsp;
<input type="text" name="waluty[_]" placeholder="symbol" style="color:black; width:50px"/>
<input type="submit" value="ok" style="color:black; width:50px"/>
</form>