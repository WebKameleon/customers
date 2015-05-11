<?php
global $MsgLang,$msgsearch;
global $IMAGES;

$defaultlang='ms';
$LANGS=array('i','en');

if (!strlen($MsgLang)) $MsgLang=$lang;


echo "<p align=center>";

for ($i=0;$i<count($LANGS);$i++)
{
	if ($i) echo " - ";
	echo "<a href=$next${next_query_char}MsgLang=$LANGS[$i]>";
	if ($MsgLang==$LANGS[$i]) echo "<font color=#FC7116><b>";
	echo sysmsg($LANGS[$i]);
	echo "($LANGS[$i])";
	if ($MsgLang==$LANGS[$i]) echo "</b></font>";	
	echo "</a>";
}
echo "</p>";
if (!isset($MsgLang)) return;

$msgsearch=trim($msgsearch);

?>
<form method=post action="<?php echo $next?>"> 
&nbsp; <input class=k_input name=msgsearch value="<?php echo $msgsearch?>">
<input type=submit class=k_button value="<?php echo sysmsg("submit_search_in_language");?> '<?php echo sysmsg($MsgLang)?>'">
<input type=hidden name=MsgLang value="<?php echo $MsgLang;?>">
</form>

<?php

echo "<table cellspacing=0 cellpadding=5 border=1>\n";

if (strlen($msgsearch)) $AND = "AND msg_msg !~* '$msgsearch'";

$query="SELECT msg_label,msg_id FROM messages defmsg 
	 WHERE msg_lang='$defaultlang' AND msg_label<>''
	 AND msg_label NOT IN (SELECT msg_label FROM messages
				WHERE msg_label=defmsg.msg_label AND msg_lang='$MsgLang' $AND)
	 ORDER BY msg_label
	 ";

$res=db_Exec($db,$query);

for ($i=0;$i<db_NumRows($res);$i++)
{
	parse_str(db_ExplodeName($res,$i));
	$msg_label=stripslashes($msg_label);

	echo " <tr>\n";
	echo "  <td valign=top><b>$msg_label</b><br><br>";
	$msg_delete=sysmsg("delete");
	echo "  <a href=$next${znak}action=UsunMsg&msg_id=$msg_id&MsgLang=$MsgLang><img 
		border=0 src=$IMAGES/ikona-smietnik-b.gif alt='$msg_delete $msg_label'>
		</a> [$msg_id]  </td>\n";

	echo "<td nowrap>";
	echo "<form method=post action=$next>";
	echo "<input type=hidden name=action value=ZapiszMsg>";
	echo "<input type=hidden name=msg_label value=\"$msg_label\">";
	echo "<input type=hidden name=MsgLang value='$MsgLang'>";

	$msg_label=addslashes($msg_label);
	$msg_msg="";

	$query="SELECT msg_msg FROM messages WHERE msg_label='$msg_label' AND msg_lang='$MsgLang'";
	parse_str(sql2url($query));
	echo "<textarea name=msg_msg cols=40 rows=4>$msg_msg</textarea>";


	$msg_save=sysmsg("submit_save_msg");

	echo "<br><input type=submit value='$msg_save' class=button>";
	echo "</form></td>\n";

	echo " </tr>\n";
}
echo "</table>\n";


