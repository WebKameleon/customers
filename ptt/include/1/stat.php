
<?
	/*
	w const.h szablonu należy jeszcze włšczyć zmiennš:
	$C_SHOW_PAGE_KEY=1;
	*/
	if ($KAMELEON_MODE) return;
	global $WEBPAGE;
	$key=$WEBPAGE->pagekey;
	if (!strlen($key)) return;
?>
<!-- (C) 2004 stat.pl - ver 1.0 / Strona xxx -->
<SCRIPT type="text/javascript">
<!--
document.writeln('<'+'scr'+'ipt type="text/javascript" src="http://s2.hit.stat.pl/_'+(new Date()).getTime()+'/script.js?<?echo $key?>"></'+'scr'+'ipt>');
//-->
</SCRIPT>
