<?php
	$lwidth="60%";	
	global $szukaj,$only_dancers,$only_newsletter;
?>

<form action="<?echo $SCRIPT_NAME?>" method=post>
<input name=szukaj value="<?echo $szukaj?>"> 
(tylko zapisani na tańce<input type=checkbox <?if ($only_dancers) echo "checked"?> value=1 name=only_dancers>)
(tylko odbiorcy newslettera <input type=checkbox <?if ($only_newsletter) echo "checked"?> value=1 name=only_newsletter>)

<br>
<input type=submit value="<?echo sysmsg("admin_mailing_search")?>" class=button>

<?php
	
	$ile=0;
	if (strlen($szukaj) || $only_dancers || $only_newsletter)
	{
		$query="SELECT DISTINCT imie,nazwisko,miasto,email FROM klienci
			WHERE email ~'@'
			";
		$szukaj=trim($szukaj);
		if (strlen($szukaj))
		{
			$query.=" AND (nazwisko ~* '$szukaj' OR email ~* '$szukaj' OR miasto ~* '$szukaj')";
		}
		if ($only_dancers)
		{
			$query.=" AND id IN (SELECT klient_id FROM zapisy WHERE ilosc>0 AND klient_id=klienci.id)";
		}

		if ($only_newsletter)
		{
			$query.=" AND email_zgoda='1'";
		}


		$query.="\nORDER BY nazwisko,miasto";

		//echo $query;

		$res=db_Exec($db,$query);
		$ile=db_NumRows($res);
	}

	$msg_send=sysmsg("admin_mailing_send");
	$msg_select_msg=sysmsg("admin_mailing_select_msg");

	echo "<br><br>";

	echo "<select name=sendmail_action><option value=''>$msg_select_msg</option>\n";
	$query="SELECT action FROM mailer ORDER BY action";
	$res1=db_Exec($db,$query);
	for ($i=0;$i<db_numRows($res1);$i++)
	{
		parse_str(db_ExplodeName($res1,$i));
		$selected=($sendmail_action==$action)?"selected":"";
		echo "<option $selected value='$action'>$action</option>";
	}
	echo "</select>\n";
			
			

	echo "<input type=button class=button value='$msg_send'
		onClick=\"action.value='RozeslijListy'; submit(); \">\n";

	$msg_ile=sysmsg("admin_mailing_addr_count");
	echo "<br><br><br><b>$msg_ile: $ile</b><br><br>";
	echo "<textarea rows=15 cols=90 name=mailing_bcc>";

	for ($i=0;$i<$ile;$i++)
	{
		parse_str(db_ExplodeName($res,$i));
		if ($maile[$email]) continue;
		$maile[$email]=1;
		if ($i) echo "\n";
		echo "$imie $nazwisko - $miasto <$email>";
	}
	echo "</textarea>";



?>
<input type=hidden name=action value="">
</form>
