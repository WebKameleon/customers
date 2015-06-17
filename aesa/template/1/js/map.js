var map;
var map_data;
var map_marker_array=[];

function add_marker(i,kind,icon) {
    var m=new google.maps.Marker({
        position: new google.maps.LatLng(map_data['markers-data'][i]['lat'],map_data['markers-data'][i]['lng']),
        map: map,
        title: map_data['markers-data'][i]['name'],
        animation: google.maps.Animation.DROP,
        icon: icon
    });
    map_marker_array[kind].push(m);
    
    var html='<h3>'+map_data['markers-data'][i]['name']+'</h3>';
    html+='<div>'+map_data['markers-data'][i][map_lang]+'</div>';
    var ul='';

    for (i2=0;i2<map_data['markers'].length;i2++) {
        var kind2=map_data['markers'][i2]['Type'];
        if (typeof(map_data['markers-data'][i][kind2])!='undefined' && map_data['markers-data'][i][kind2]==1) {
            ul+='<li><img src="'+map_icons+'/'+map_data['markers'][i2]['Icon']+'"/></li>';
        }
    }    
    if (ul.length) {
        html+='<ul>'+ul+'</ul>';
    }
    
    var infowindow = new google.maps.InfoWindow({
        content: html
    });
    
    google.maps.event.addListener(m, 'click', function() {
        infowindow.open(map,m);
    });
    
}

function add_markers(kind) {
    
    var icon=null;
    for (i=0;i<map_data['markers'].length;i++) {
        if (map_data['markers'][i]['Type']==kind) {
            icon=map_icons+'/'+map_data['markers'][i]['Icon'];
        }
    }
    
    if (typeof(map_marker_array[kind])=='undefined') {
        map_marker_array[kind]=[];
    }
    var when=1;
    for(var i=0;i<map_data['markers-data'].length;i++) {
        
        if (typeof(map_data['markers-data'][i][kind])!='undefined' && map_data['markers-data'][i][kind]==1) {
            
            setTimeout(add_marker.bind(this, i, kind, icon),when);
            when+=60;

            
        }
    }
}

function initialize() {
    
    var styles = [
    {
        "featureType": "administrative",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#444444"
            }
        ]
    },{
        "featureType": "water",
        "stylers": [
            { "color": "#0e3d8a" }
        ]
    },{
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#f2f2f2"
            }
        ]
    },{
        "featureType": "poi",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },{
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#0e3d8a"
            },
            {
                "visibility": "simplified"
            }
        ]
    },{
        "featureType": "road.highway",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },{
        "featureType": "road.arterial",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },{
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },{
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "color": "#0e3d8a"
            },
            {
                "visibility": "on"
            }
        ]
    }
    ];
    
    
    
    var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});

    
    var aesaLatlng = new google.maps.LatLng(52.1769581,17.1201913);
    var mapOptions = {
        zoom: 8,
        center: aesaLatlng ,
        mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']  
        }
    }
    map = new google.maps.Map(document.getElementById('google-map'), mapOptions);
    
    
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
    
    
    if (typeof(map_json)!='undefined') {
        $.getJSON(map_json,function(data) {
            
            map_data=data;
            
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
            
            
            if (typeof(data['markers'])!='undefined' && typeof(data['markers-data'])!='undefined')
            {
                for (i=0;i<map_data['markers'].length;i++) {
                    if (map_data['markers'][i]['VisibleStart']==1) {
                        add_markers(map_data['markers'][i]['Type']);
                    }
                }                
            }
        });
    }

}

$('ul.map-markers li').click(function() {
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
        
        var kind=$(this).attr('class');
        if (typeof(map_marker_array[kind])!='undefined') {
            
            for (i=0;i<map_marker_array[kind].length;i++) {
                map_marker_array[kind][i].setMap(null);
            }
            map_marker_array[kind]=[];
        }        
    } else {
        
        add_markers($(this).attr('class'));
        $(this).addClass('active');
    }
    
    
});


google.maps.event.addDomListener(window, 'load', function() {
    
    initialize();    
});

