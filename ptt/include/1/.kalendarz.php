<?php

        if (isset($_POST['ical'][$sid]))
        {
                $webtd=new webtdModel($sid);
                $webtd->costxt=$costxt=$_POST['ical'][$sid];
                $webtd->save();
        }

?>
<form method="post">
<input type="text" width="20" placeholder="link prywatny do kalendarza" name="ical[<?php echo $sid?>]" value="<?php echo $costxt?>" /> <inpu
t type="submit" value="zapisz" />
</form>
