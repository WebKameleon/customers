<?php
    $js=file_get_contents('line.js');
    
    $line=[];
    preg_match_all('/google.maps.LatLng\(([0-9.]+),\s*([0-9.]+)\)/',$js,$line);
    
    echo "lat,lng\n";
    foreach($line[1] AS $i=>$lat)
        echo "$lat,".$line[2][$i]."\n";