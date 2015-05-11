<?php



$query="SELECT id,d_wykonania,username,opis FROM undo $add_cond 
	ORDER BY id DESC";
$res=pg_Exec($db,$query);

//echo $query;

echo "<table cellspacing=0 cellpadding=3 border=0 width=100% class=\"table table-responsive table-bordered\">";

for ($i=0;$i<pg_NumRows($res);$i++)
{
	parse_str(pg_ExplodeName($res,$i));

	$href="$next${znak}undo=$id&action=admin/UnDo";

	echo "<tr>";

	echo "<td>";
	echo "$username";
	echo "</td>";
	
	echo "<td nowrap>";
	echo FormatujDate($d_wykonania);
	echo "</td>";

	echo "<td>";
	echo "$opis";
	echo "</td>";


	echo "<td>";
	echo "<a href='$href'>";
	echo "<img src='$IMAGES/ikona-ok-b.gif' border=0 alt='Cofnij operację'";
	echo "</a>";
	echo "</td>";

	echo "</tr>";
}

echo "</table>";


