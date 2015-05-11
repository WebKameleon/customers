<?php

	if (isset($_POST['ical'][$sid]))
	{
		$webtd=new webtdModel($sid);
		$webtd->costxt=$costxt=$_POST['ical'][$sid];
		$webtd->cos=$cos=$_POST['ical_m'][$sid]+100*($_POST['ical_y'][$sid]-2000);
		$webtd->staticinclude=1;
		$webtd->ob=3;
		$webtd->save();
	}

	$m=$y='';
	if ($cos)
	{
		$m=$cos%100;
		$y=2000+floor($cos/100);
	}

?>
<form method="post">	
<input type="text" style="width:200px" placeholder="link prywatny do kalendarza" name="ical[<?php echo $sid?>]" value="<?php echo $costxt?>" />
<input type="text" style="width:25px" placeholder="mm" name="ical_m[<?php echo $sid?>]" value="<?php echo $m?>" /> /
<input type="text" style="width:50px" placeholder="rrrr" name="ical_y[<?php echo $sid?>]" value="<?php echo $y?>" />
<input type="submit" value="zapisz" />
</form>
