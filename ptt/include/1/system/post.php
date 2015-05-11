<?php
	@pg_Close($db);

	if (isset($sysinfo) && strlen($sysinfo)) {
		echo "<script>alert('$sysinfo');</script>";
		$sysinfo='';
	}
