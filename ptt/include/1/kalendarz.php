<?php

	require_once(__DIR__.'/system/crypt.php');
	$include=$KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
	$rel=encrypt($costxt);
	if (!$rel) {
		if ($KAMELEON_MODE) echo 'brak ical';
		return;
	}
	

?>

<div class="ptt_kalendarz" rel="<?php echo $include;?>/ajax/kalendarz.php?next=<?php echo urlencode($next_link);?>&id=<?php echo urlencode($rel)?>"></div>

