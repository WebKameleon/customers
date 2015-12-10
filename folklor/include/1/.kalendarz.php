<?php

        if (isset($_POST['ical'][$sid]))
        {
                $webtd=new webtdModel($sid);
                $webtd->costxt=$costxt=$_POST['ical'][$sid];
                $webtd->save();
        }

?>
<form method="post">
<input type="text" style="width:70%" placeholder="link prywatny do kalendarza" name="ical[<?php echo $sid?>]" value="<?php echo $costxt?>" /> 
<input type="submit" value="zapisz" style="width:20%" />
</form>
