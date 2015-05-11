<?
$ret = "<div class=\"navi\">
		<a href=\"javascript:history.back();\"><img src=\"".$IMAGES."/navi/".$lang."/back.gif\" border=\"0\" alt=\"\"></a>
		<a href=\"#top\"><img src=\"".$IMAGES."/navi/".$lang."/top.gif\" border=\"0\" hspace=\"10\" alt=\"\"></a>
		<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/navi/".$lang."/print.gif\" border=\"0\" alt=\"\"></a></div>";
echo $ret;

global $C_URL, $REQUEST_URI,$NAVI,$HTTP_HOST, $WEBPAGE;
if (!$KAMELEON_MODE) 
{
	$file_name = $WEBPAGE->file_name;
	echo "<? \$file_name='$file_name'; include(\"\$MAIN_INCLUDE_PATH/navipub.php\");?>";
}
?>
