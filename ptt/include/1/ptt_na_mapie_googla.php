<?php
    $webtd=new webtdModel($sid);

    $adresy=unserialize(base64_decode($webtd->web20))?:[];
    
    $colors=[
        0 => 'FF0000|FFFFFF',
        1 => '00FF00|FFFFFF',
        2 => 'FFFF00|000000',
        3 => '00FFFF|000000',
        4 => 'FF00FF|000000',
        5 => '0000FF|FFFFFF',
    ];

    
    $js='';
    $html='';
    $lp=1;

    
    foreach ($adresy AS $nazwa=>$a)
    {
	if (!isset($a['geo']['results'][0]['geometry']['location']['lat'])) continue;
	if (!isset($a['geo']['results'][0]['geometry']['location']['lng'])) continue;

	$lat=$a['geo']['results'][0]['geometry']['location']['lat'];
	$lng=$a['geo']['results'][0]['geometry']['location']['lng'];

	$html.="<p><span>$lp</span> $nazwa (".$a['address'].")</p>";
	$title=addslashes($nazwa);
	$color=isset($colors[$lp])?$colors[$lp]:$colors[0];
	$url="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=$lp|$color";
	$js.="wkw_gmap_add('kmw_gmap_$sid','',$lat,$lng,0,true,'$title',{url:'$url',w:21,h:34});\n";

	$lp++;	
    }
    
    echo '<div class="ptt_mapa_obj">'.$html.'</div>';
?>

<script>
    function kontakty_na_google_mapie()
    {
        setTimeout(function() {
            <?php echo $js; ?>
        }, 500);
        
    }
    
    
    (function () {
        if (window.addEventListener) {
            window.addEventListener('load', kontakty_na_google_mapie, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', kontakty_na_google_mapie);
        }
    })();    
    
</script>
    
