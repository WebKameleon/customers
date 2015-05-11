<?php
    $webtd=new webtdModel($sid);

    $adresy=unserialize(base64_decode($webtd->web20));
    
    $colors=[
        0 => 'FF0000|FFFFFF',
        1 => '00FF00|FFFFFF',
        2 => 'FFFF00|000000',
        3 => '00FFFF|000000',
        4 => 'FF00FF|000000',
        5 => '0000FF|FFFFFF',
    ];

    $sql="SELECT * FROM obiekty WHERE kod IN (SELECT obiekt FROM kursy WHERE rok=$C_ROK) ORDER BY grupa,id";
    $res=pg_Exec($db,$sql);
    
    $js='';
    $html='';
    $_grupa=0;
    $lp=1;

    for ($i=0;$i<pg_NumRows($res);$i++)
    {
	parse_str(pg_ExplodeName($res,$i));
        
        if (!isset($adresy[md5($adres)]))
        {
            $url='http://maps.google.com/maps/api/geocode/json?address='.urlencode("Poznań, $adres").'&sensor=false&region=PL';
            $data=json_decode(file_get_contents($url),true);
            
            if (isset($data['results'][0]['geometry']['location']))
            {
                $adresy[md5($adres)]=$data['results'][0]['geometry']['location'];

            }
            
        }
        
        if (isset($adresy[md5($adres)]['lat'])) {
            $lat=$adresy[md5($adres)]['lat'];
            $lng=$adresy[md5($adres)]['lng'];
            $title=addslashes($nazwa);
            $color=isset($colors[$grupa])?$colors[$grupa]:$colors[0];
            $url="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=$lp|$color";
            $js.="wkw_gmap_add('kmw_gmap_$sid','',$lat,$lng,0,true,'$title',{url:'$url',w:21,h:34});\n";
            
            
            if ($grupa!=$_grupa)
            {
                $html.="<h4>".sysmsg('Grupa obiektów nr')." $grupa</h4>";
                $_grupa=$grupa;
            }
            $html.="<p><span>$lp</span> $nazwa ($adres)</p>";
            $lp++;
        }
        
    }
    
    $webtd->web20=base64_encode(serialize($adresy));
    $webtd->save();



    echo '<div class="ptt_mapa_obj">'.$html.'</div>';
?>

<script>
    function obiekty_na_google_mapie()
    {
        setTimeout(function() {
            <?php echo $js; ?>
        }, 500);
        
    }
    
    
    (function () {
        if (window.addEventListener) {
            window.addEventListener('load', obiekty_na_google_mapie, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', obiekty_na_google_mapie);
        }
    })();    
    
</script>
    