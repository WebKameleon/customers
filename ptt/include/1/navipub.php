<?
$url = str_replace($file_name,"print/$file_name",$REQUEST_URI);
$href_print="popup_url('".$url."',780, 400,'yes')";
?>

<script language="JavaScript1.2" type="text/javascript">
function drukuj()
{
	<?echo $href_print;?>
}
</script>