<?php
return; //pudel
global $api_query;

$api_query=ereg_replace("\"","&quot;",stripslashes($api_query));
$szu=$costxt;
if (!strlen($szu)) $szu = "znajdź w serwisie";
if (!strlen($api_query)) $api_query=$szu;


$ret = "<form method=\"post\" action=\"".$next."\" id=\"search\">";
$ret.= "<input type=\"hidden\" name=\"api_post\" value=\"1\">";
$ret.= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"search\"><tr>";
$ret.= "<td><input type=\"text\" class=\"api_search_input\" name=\"api_query\" onClick=\"szukajOnClick(this)\" onBlur=\"szukajOnBlur(this)\" value=\"".$api_query."\"></td>";
$ret.= "<td><input type=\"image\" class=\"api_search_button\" src=\"".$IMAGES."/search.gif\"></td>";
$ret.= "<td><a href=\"".kameleon_href('','',0)."\"><img src=\"".$IMAGES."/home.gif\" alt=\"PTT - strona główna\" border=\"0\" hspace=\"10\"></a></td>";

if ($lang=="i")
	$ret.= "<td><a href=\"".kameleon_href('','','en:0')."\"><img src=\"".$IMAGES."/en.gif\" alt=\"English\" border=\"0\"></a></td>";
elseif ($lang=="en")	
	$ret.= "<td><a href=\"".kameleon_href('','','i:0')."\"><img src=\"".$IMAGES."/pl.gif\" alt=\"Polski\" border=\"0\"></a></td>";

$ret.= "</tr></table>";
$ret.= "</form>";

echo $ret;

?>
<script language="JavaScript" type="text/javascript">
	function szukajOnClick(obj)	{
		if (obj.value=='<?echo $szu?>') obj.value='';
	}

	function szukajOnBlur(obj)	{
		if (obj.value.length == 0) obj.value='<?echo $szu?>';
	}

	function szukajSubmit(obj)	{
		if (obj.szukaj.value == '<?echo $szu?>' || obj.szukaj.value.length == 0)
			return false;
		return true;
	}
</script>
