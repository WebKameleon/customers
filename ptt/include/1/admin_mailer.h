<?php
	global $IMAGES,$SCRIPT_NAME;
	global $mailer_id;

	include("$INCLUDE_PATH/zakladkifun2.h");
	$plik_akcji=$SCRIPT_NAME;

	$mailtypes[]=array("plain","zwykły tekst");
	$mailtypes[]=array("html","html");
?>

<form action="<?php echo $SCRIPT_NAME?>" method=POST>
<input type=hidden name=action value=DodajMailer>
<input type=hidden name=page value="<?echo $page?>">

<?php



$query="SELECT * FROM mailer ORDER BY action "; 
$result=pg_Exec($db,$query);

$kattab="";	
for ($i=0;$i<pg_NumRows($result);$i++)
{
	parse_str(pg_ExplodeName($result,$i));


	$kattab[]=array("<b>$action</b>","","$id","$SCRIPT_NAME","mailer_id","page=$page");
}
$kattab[]=array("<b>Nowa akcja:</b> <input size=15 name=nazwa style=font-size:10
	>","",-1,"javascript:nop() ","mailer_id","");


echo zakladki2($kattab,"$IMAGES",4, "", $mailer_id, "RED","#608E60","#B0C2B2");
?>
</form>

<?php
if ($mailer_id>0)
{
	$id=0;
	$query="SELECT * FROM mailer WHERE id=$mailer_id";
	parse_str(query2url($query));

	$msg=stripslashes($msg);
	$subject=stripslashes($subject);
	if (!$id) return;
}
else return;

?>

<form method=post action=<?echo $SCRIPT_NAME?>  enctype='multipart/form-data'>
<input type=hidden name=page value="<?echo $page?>">
<input type=hidden name=action value="ZapiszMailer">
<input type=hidden name=akcja value="<?echo $action?>">
<input type=hidden name=grupa value="<?echo $grupa?>">
<input type=hidden name=mailer_id value="<?echo $mailer_id?>">

<table border=1 cellspacing=0 cellpadding=3 width=100% bordercolor=#000000>
<tr>
	<td colspan=2 bgcolor='#B0C2B2'><font color=#ffffff>
	<b><?echo $action?></b>, 
	<?
	echo "<a href=\"javascript:zmiana($mailer_id,'null','UsunMailer','Jestes pewien, że chcesz usunać $action')\">Usuń akcję</a>";
	?>

	</font></td>
</tr>
<tr>
	<td align=right>Nazwa akcji (radzę nie zmieniać):</td>
	<td>
	<input type=text size=16 name=nazwa value="<?echo $action?>">
	</td>
</tr>
<tr>
	<td align=right>Od kogo:</td>
	<td>
	<input type=text size=50 name=mailfrom value="<?echo $mailfrom?>">
	</td>
</tr>
<tr>
	<td align=right>Temat:</td>
	<td>
	<input type=text size=50 name=subject value="<?echo $subject?>">
	</td>
</tr>
<tr>
	<td align=right>Format:</td>
	<td>
		<?echo CreateFormField(array("",1,"select","type",$type,$mailtypes))?>

	</td>
</tr>


<tr>
	<td align=right>Treść listu:</td>
	<td>
		<textarea name=msg rows=20 cols=80><?echo $msg?></textarea>
	</td>
</tr>
<!--
<tr>
	<td align=right>Grupa (do mailingu):</td>
	<td>
	<input type=text size=2 name=grupa value="<?echo $grupa?>">
	</td>
</tr>
-->


</table>
<br><br>
&nbsp; <input type=submit value=Zapisz class=button>

</form>








<form name=zmiany method=post action=<?echo $SCRIPT_NAME?> >
 <input type=hidden name=action value="">
 <input type=hidden name=mailer_id value="">
 <input type=hidden name=value value="">
 <input type=hidden name=page value="<?echo $page?>">

</form>

	

<script>
function zmiana(id,value,action,query)
{
	if (value=='null') 
	{
		if( !confirm(query)) return;
	}
	else
	{
		value=prompt(query,value);
		if (value==null) return;
	}

	document.zmiany.mailer_id.value=id;	
	document.zmiany.action.value=action;	
	document.zmiany.value.value=value;	
	document.zmiany.submit();
}

function nop()
{

}
</script>
