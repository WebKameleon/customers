<?php


	if (isset($this->mode) && $this->mode==2) {
		include __DIR__.'/.waluty.php';
		return;
	}
	
	$include=(isset($KAMELEON_MODE) && $KAMELEON_MODE)?$session['uincludes_ajax']:(isset($session['include_path']) ? $session['include_path'] : $INCLUDE_PATH);
	$ajax_str=$include.'/system/waluty.json';
	
?>

<script>
	var waluty_set;
	var waluty_lock = 0;

	function waluty_get() {
		if (!waluty_lock)
		{
			$.get('<?php echo $ajax_str;?>',waluty_set);
			waluty_lock = 1;
		}
	}
	if (window.addEventListener)
	    window.addEventListener('load', waluty_get, false);
	else if (window.attachEvent)
	    window.attachEvent('onload', waluty_get);
	
</script>
