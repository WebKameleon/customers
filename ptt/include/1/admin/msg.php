<?
global $MsgLang,$msgsearch,$MsgGroup,$MsgSearchLang;

$defaultlang='ms';
$LANGS=array('i','e','r','d','f','t','s');

$next_query_char=strstr($self,"?")?"&":"?";

if (!strlen($MsgLang)) $MsgLang=$lang;


$query="SELECT msg_group, count(*) AS c 
		FROM messages 
		WHERE msg_lang='$defaultlang'
		GROUP BY msg_group";
$res=pg_Exec($db,$query);

echo "<p align=center>";
for ($i=0;$i<pg_NumRows($res);$i++)
{
	parse_str(pg_ExplodeName($res,$i));
	if ($i) echo " - ";

	
	echo "<a href=$next${next_query_char}MsgLang=$MsgLang&MsgGroup=$msg_group>";
	$_msg_group = strlen($msg_group) ? $msg_group :sysmsg("no group","system") ;

	if ($MsgGroup==$msg_group) echo "<font color=#FC7116><b>";
	
	echo $_msg_group;

	if ($MsgGroup==$msg_group) echo "</b></font>";	
	echo "</a>";
}

echo "</p>";


echo "<p align=center>";

for ($i=0;$i<count($LANGS);$i++)
{
	if ($i) echo " - ";
	echo "<a href=$next${next_query_char}MsgLang=$LANGS[$i]&MsgGroup=$MsgGroup>";
	if ($MsgLang==$LANGS[$i]) echo "<font color=#FC7116><b>";
	echo sysmsg($LANGS[$i],"system");
	echo "($LANGS[$i])";
	if ($MsgLang==$LANGS[$i]) echo "</b></font>";	
	echo "</a>";
}

echo "<br><br><a href='$self${next_query_char}action=LangCVS'>EXPORT</a>";

echo "</p>";
if (!isset($MsgLang)) return;

$msgsearch=trim($msgsearch);

?>

<br><br>
<form method=post action="<?echo $next?>"> 
&nbsp; <input class=forminput name=msgsearch value="<?echo $msgsearch?>">
<input type=submit class=formbutton style="width: 300px" value="<?echo sysmsg("submit_search_in_language","system");?> '<?echo sysmsg($MsgLang,"system")?>'" >
<input type=hidden name=MsgLang value="<?echo $MsgLang;?>">
<input type=hidden name=MsgSearchLang value="<?echo $MsgLang;?>">
<input type=hidden name=MsgGroup value="<?echo $MsgGroup;?>">
</form>

<form method=post action="<?echo $next?>"> 
&nbsp; <input class=forminput name=msgsearch value="<?echo $msgsearch?>">
<input type=submit class=formbutton style="width: 300px" value="<?echo sysmsg("submit_search_in_language","system");?> 'xx'" >
<input type=hidden name=MsgLang value="<?echo $MsgLang;?>">
<input type=hidden name=MsgSearchLang value="ms">
<input type=hidden name=MsgGroup value="<?echo $MsgGroup;?>">
</form>



<br><br>
<?

echo "<table cellspacing=0 cellpadding=5 border=1 class=formtable>\n";


if (!strlen($MsgSearchLang)) $MsgSearchLang=$MsgLang;

if (strlen($msgsearch)) $AND = "AND msg_msg ~* '$msgsearch'";
else 
{
	$GROUPAND="AND msg_group='$MsgGroup'";
	$NOT="NOT";
}


$query="SELECT msg_label,msg_id,msg_group FROM messages defmsg 
	 WHERE msg_lang='$defaultlang' AND msg_label<>'' $GROUPAND
	 AND msg_label $NOT IN (SELECT msg_label FROM messages
				WHERE msg_label=defmsg.msg_label AND msg_lang='$MsgSearchLang' $AND)
	 ORDER BY msg_label
	 ";

//echo nl2br($query);
$res=pg_Exec($db,$query);

for ($i=0;$i<pg_NumRows($res);$i++)
{
	parse_str(pg_ExplodeName($res,$i));
	$msg_label=stripslashes($msg_label);

	echo " <tr>\n";
	echo "  <td valign=top ><b>$msg_label</b><br>$msg_group<br><br>";
	$msg_delete=sysmsg("delete","system");
	echo "  <a href=$next${next_query_char}action=UsunMsg&msg_id=$msg_id&MsgLang=$MsgLang&MsgGroup=$MsgGroup><img 
		border=0 src='$UIMAGES/szablon/i_delete_min_a.gif' alt='$msg_delete $msg_label' align='absMiddle'></a> [$msg_id]  </td>\n";

	echo "<td nowrap align=right>";
	echo "<form method=post action=$next>";
	echo "<input type=hidden name=action value=ZapiszMsg>";
	echo "<input type=hidden name=msg_label value=\"$msg_label\">";
	echo "<input type=hidden name=MsgLang value='$MsgLang'>";
	echo "<input type=hidden name=MsgGroup value='$MsgGroup'>";

	$msg_label=addslashes($msg_label);
	$msg_msg="";

	$query="SELECT msg_msg FROM messages WHERE msg_label='$msg_label' AND msg_lang='$MsgLang'";
	parse_str(query2url($query));
	echo "<textarea name=msg_msg cols=40 rows=4 style='width: 290px;' class=formtextarea>$msg_msg</textarea>";


	$msg_save=sysmsg("submit_save_msg","system");

	echo "<br><input type=submit class=formbutton value='$msg_save' class=button>";
	echo "</form></td>\n";

	echo " </tr>\n";
}
echo "</table>\n";


?>