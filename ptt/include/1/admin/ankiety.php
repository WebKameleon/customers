<?php
	$p_edit=$_REQUEST['p_edit'];
	$p_del=$_REQUEST['p_del'];
	$p_form=$_REQUEST['p_form'];

	


	if ("$p_edit"!="0") echo "<a href='$self${znak}p_edit=0'>DODAJ PYTANIE</a><br>";
	if ("$p_edit"=="0")
	{
		$query="SELECT max(p_pri) AS pri FROM pytania";
		parse_str(query2url($query));

		$pri++;
		
		$query="INSERT INTO pytania (p_pri) VALUES ($pri); SELECT p_id AS p_edit FROM pytania WHERE p_pri=$pri";
		parse_str(query2url($query));
	}

	if (is_array($p_form))
	{
		
		$set=array();
		foreach($p_form AS $k=>$v) 
		{
			$set[]="$k=".(strlen($v)?"'".addslashes(stripslashes($v))."'":"NULL");
		}
		$query="UPDATE pytania SET ".implode(',',$set)." WHERE p_id=".$p_form[p_id];
		pg_exec($db,$query);
		//echo $query;
	}

	if ($p_del)
	{
		$query="SELECT count(*) AS c FROM odpowiedzi WHERE o_p_id=$p_del";
		parse_str(query2url($query));
		if ($c) echo "<font color=red>W bazie danych są odpowiedzi</font>";
		else
		{
			$query="DELETE FROM pytania WHERE p_id=$p_del";
			pg_exec($db,$query);

		}
	}

	

	if ($p_edit)
	{
		echo "<form method='post' action='$self'><table class=\"table table-responsive table-bordered\">";
		$query="SELECT * FROM pytania WHERE p_id=$p_edit";
		foreach (explode('&',query2url($query)) AS $url)
		{
			$p=explode('=',$url);
			$k=$p[0];
			$v=stripslashes(urldecode($p[1]));

			switch ($k)
			{
				case 'p_pytanie':
					$size=100;
					break;
				case 'p_skala_od':
				case 'p_skala_do':
					$size=1;
					break;

				case 'p_r_od':
				case 'p_r_do':
					$size=5;
					break;

				default:
					$size=20;

			
			}


			if ($k!='p_id')
			{
				echo "<tr>
				<td class='c1'><font color=white><b>".sysmsg($k)."</b></font></td>
				<td class='c2'><input size=$size type=text name='p_form[$k]' value='$v'></td>";
			}
			else
			{
				echo "<input type=hidden name='p_form[$k]' value='$v'>";
			}


		}

		echo "</table>&nbsp;<input type='submit' value='Zapisz'></form>";
	}

	


	$query="SELECT * FROM pytania ORDER BY p_pri";
	$res=pg_Exec($db,$query);

	echo "<table class=\"table table-responsive table-bordered\">";
	echo "<tr>
		<td class='c1'><font color=white><b>".sysmsg("Pytanie")."</b></font></td>
		<td class='c1'><font color=white><b>".sysmsg("Symbol")."</b></font></td>
		<td class='c1'><font color=white><b>".sysmsg("Odp")."</b></font></td>
		<td class='c1'><font color=white><b>".sysmsg("Średnia")."</b></font></td>
		<td class='c1'><font color=white><b>".sysmsg("Akcje")."</b></font></td>
		</tr>";


	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));

		$p_pytanie=stripslashes($p_pytanie);


		$sql="SELECT count(*) AS odp FROM odpowiedzi WHERE o_p_id=$p_id";
		parse_str(query2url($sql));
		$sql="SELECT avg(o_wart) AS srednia FROM odpowiedzi WHERE o_p_id=$p_id AND o_wart>0";
		parse_str(query2url($sql));
		$srednia=round($srednia,2);

		echo "<tr>";

		echo "<td class='c2'>";
		echo "$p_pytanie";
		echo "</td>";

		echo "<td class='c2'>";
		echo "$p_symbol";
		echo "</td>";

		echo "<td class='c2'>";
		echo "$odp";
		echo "</td>";

		echo "<td class='c2'>";
		echo "$srednia";
		echo "</td>";

		echo "<td class='c2'>";
		echo "<a href='$self${znak}p_edit=$p_id'>Zmień</a>";
		echo "<br><a href='$self${znak}p_del=$p_id'>Usuń</a>";
		echo "<br><a href='$self${znak}pytanie=$p_id&action=AnkietaCSV'>Wyniki</a>";
		echo "</td>";
	}


	echo "</table>";


