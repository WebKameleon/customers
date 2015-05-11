<?php
    //$ip=$_SERVER['REMOTE_ADDR']=='127.0.0.1'?'46.238.93.58':$_SERVER['REMOTE_ADDR'];
    
    //mydie(geoip_record_by_name($ip));
        

    include(__DIR__.'/product_choose.php');
    
    if (isset($KAMELEON_MODE) && $KAMELEON_MODE )
    {
        $incpath=$session['uincludes_ajax'];
    }
    else
    {
        $incpath = isset($session['include_path']) ? $session['include_path'] : $INCLUDE_PATH;
    }
    
    $ajax_gmap_path=$incpath.'/google_maps.php';

?>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=true"></script>

<script type="text/javascript">
    
    function positionFound(position)
    {
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        
        geocoder.geocode({
            'latLng': latlng }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                
                for (i=0; i<results[0].address_components.length;i++)
                {
                    if (results[0].address_components[i].types[0]=='locality') {
                        $('#whereami').html(results[0].address_components[i].long_name);
                        break;
                    }
                }                
            }
        });
    
    }
    
    $(function() {
        if (navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition(positionFound);
        }
    }); 
</script>
