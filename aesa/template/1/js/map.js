function initialize() {
    var aesaLatlng = new google.maps.LatLng(52.1769581,17.1201913);
    var mapOptions = {
        zoom: 8,
        center: aesaLatlng
    }
    var map = new google.maps.Map(document.getElementById('google-map'), mapOptions);
    
    if (typeof(map_json)!='undefined') {
        $.getJSON(map_json,function(data) {
            
            
            if (typeof(data['polyline'])!='undefined' && typeof(data['polyline-data'])!='undefined')
            {
                var path=[];
                for (i=0;i<data['polyline-data'].length;i++) path[path.length]=new google.maps.LatLng(data['polyline-data'][i].lat,data['polyline-data'][i].lng);
                new google.maps.Polyline({
                    strokeColor : data['polyline'][0]['strokeColor'],
                    strokeOpacity : data['polyline'][0]['strokeOpacity'],
                    strokeWeight : data['polyline'][0]['strokeWeight'],
                    path : path}).setMap(map);
            }
        });
    }

}


google.maps.event.addDomListener(window, 'load', function() {
    initialize();    
});