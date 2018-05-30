<?php

	require_once __DIR__.'/fun.php';
	
	if (isset($_REQUEST['action']) && isset($_REQUEST['method'])) {

	?>

		<form id="frm" method="<?php echo $_REQUEST['method'];?>" action="<?php echo $_REQUEST['action'];?>">
			<?php
				foreach ($_REQUEST AS $k=>$v) {
					if ($k=='action'||$k=='method') continue;
				?>
					<input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>"/>
				<?php	
				}
			?>
		</form>
		<script>
			document.getElementById('frm').submit();
		</script>
	<?php


	}


	if (isset($_GET['proxy'])) {
		$token=md5($_GET['proxy']);
		$contents = WBP::cache($token); 

		Header('Content-type: image');
		if ($contents) {
			die(base64_decode($contents));
		}

		$contents = file_get_contents($_GET['proxy']);
		WBP::cache($token,base64_encode($contents));
		die($contents);
	}
