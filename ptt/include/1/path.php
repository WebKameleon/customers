<?
global $WEBPAGE;
$path_beginword=".:";
$path_separator=" : ";
parse_str($costxt);

$webpage_tree=explode(":",$WEBPAGE->tree);
$webpage_tree[]=$WEBPAGE->id;

for ($i_tree=0;$i_tree<(count($webpage_tree)) && is_array($webpage_tree);$i_tree++)
{
	if (!strlen($webpage_tree[$i_tree])) continue;

	$parent_page=$webpage_tree[$i_tree];
	unset($PARENT_WEBPAGE);
	$PARENT_WEBPAGE=kameleon_page($parent_page);
	if ($PARENT_WEBPAGE[0]->nositemap) continue;
	
	if (strlen($PARENT_WEBPAGE[0]->title_short))
		$title=$PARENT_WEBPAGE[0]->title_short;
	else
		$title=$PARENT_WEBPAGE[0]->title;
		
	if ($PARENT_WEBPAGE[0]->hidden!=1)
		$title="<a href=\"".kameleon_href("","",$parent_page)."\">".$title."</a>";
		
	$title.=$path_separator;
	$path.=$title;
} 

$path = substr($path,0,(strlen($path)-strlen($path_separator)));

echo "<div id=\"path\">".$path_beginword.$path."</div>";

?>