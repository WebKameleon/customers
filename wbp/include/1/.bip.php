<form method="post">
<?php

    WBP::kameleon_require_static_include($this);
    
    if (isset($_POST['key'][$sid]))
    {
        $webpage=new webpageModel($this->webpage['sid']);
        $webpage->pagekey=$this->webpage['pagekey']=trim($_POST['key'][$sid]);
        $webpage->save();
        

    }
?>
<input name="key[<?php echo $sid;?>]" type="text"
    value="<?php echo $this->webpage['pagekey'];?>" placeholder="osoba, źródło informacji"/>
<input type="submit" value="zapisz"/>
</form>

<?php
    include __DIR__.'/bip.php';