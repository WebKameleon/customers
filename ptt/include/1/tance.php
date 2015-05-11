<?php
	$prow=isset($_REQUEST['prow'])?$_REQUEST['prow']:'';

	$where="";
	if (strlen($prow)) 
	{
		$where="AND prowadzacy='$prow'";
		//if ($lang!='pl') $prow=unpolish($prow);
		echo "<p><font size=2><b>".sysmsg("Prowadzący").": $prow</b></font></p>";
		$prow="";
	}
	else
	if (strlen($costxt))
	{
		$where="AND cykl='$costxt'";
	}


	$query="SELECT DISTINCT taniec FROM kursy WHERE rok=$C_ROK $where ORDER BY taniec";
	$res=pg_Exec($db,$query);

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$taniec=stripslashes($taniec);
		$t=urlencode($taniec);
		$href="$next${znak}taniec=$t";

		echo "<a href='$href'>";
		echo "<img border=0 src='$IMAGES/arr3_r.gif' align=absMiddle vspace=3 hspace=5>";
		echo " ".sysmsg($taniec);
		echo "</a>";
		echo "<br>";
	}



