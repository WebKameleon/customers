var map;
var map_marker_array=[];

function add_marker(i,lat,lng) {
 
       
    if (lat==null) lat=0;
    if (folklor_map_menu[i].titlea!=null ) {
        lat=parseFloat(folklor_map_menu[i].titlea);
    }
    
    if (lng==null) lng=0;
    if (folklor_map_menu[i].titleb!=null ) {
        lng=parseFloat(folklor_map_menu[i].titleb);
    }
    
    if (lat==0 || lng==0)  return;
    
    console.log(folklor_map_menu);
    
    var m=new google.maps.Marker({
        position: new google.maps.LatLng(lat,lng),
        map: map,
        title: folklor_map_menu[i]['alt'],
        animation: google.maps.Animation.DROP,
        icon: folklor_map_menu[i]['img'],
        draggable: typeof(markerDragFun)=='function'
    });
    map_marker_array.push(m);
    
    
    if (typeof(markerDragFun)=='function') {
        google.maps.event.addListener(m, 'dragend',function(event) {
            markerDragFun(event.latLng.lat(),event.latLng.lng(),folklor_map_menu[i]['sid']);
        });
    }
    
    
    
    var html='<h3>'+folklor_map_menu[i]['alt']+'</h3>';
    if (folklor_map_menu[i]['imga']!=null && folklor_map_menu[i]['imga']!='') {
        html+='<img src="'+folklor_map_menu[i]['imga']+'"/>';
    }
    if (folklor_map_menu[i]['description']!=null && folklor_map_menu[i]['description']!='') {
        html+='<div>'+folklor_map_menu[i]['description']+'</div>';
    }
    

    var ul='';

    if (typeof(folklor_map_menu[i]['menu'])!='undefined') {
        for (var j=0; j<folklor_map_menu[i]['menu'].length; j++) {
            ul+='<li>';
            if (folklor_map_menu[i]['menu'][j].href!=null) ul+='<a href="'+folklor_map_menu[i]['menu'][j].href+'">';
            ul+=folklor_map_menu[i]['menu'][j].alt;
            if (folklor_map_menu[i]['menu'][j].href!=null) ul+='</a>';
            ul+='</li>';
        }
    }
     
    if (ul.length) {
        html+='<ul>'+ul+'</ul>';
    }
    
    if (folklor_map_menu[i]['href']!=null && folklor_map_menu[i]['href']!='') {
        html='<a href="'+folklor_map_menu[i]['href']+'">'+html+'</a>';
        
    }

    var infowindow = new google.maps.InfoWindow({
        content: html
    });
    
    google.maps.event.addListener(m, 'click', function() {
        infowindow.open(map,m);
        //var infobox = new SmartInfoWindow({position: m.getPosition(), map: map, content: html});
    });

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
            { "color": "#009ee2" }
        ]
    },{
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#a5c725"
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
                "color": "#009ee2"
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
                "color": "#009ee2"
            },
            {
                "visibility": "on"
            }
        ]
    }
    ];
    
    
    
    var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});

    var opt=folklor_map_opt.split(',');
    if (opt.length<3) opt=[52.1769581,18.9201913,6];
	
    
    var folklorLatlng = new google.maps.LatLng(opt[0],opt[1]);
    var mapOptions = {
        zoom: parseInt(opt[2]),
        center: folklorLatlng ,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']  
        }
    }
    map = new google.maps.Map(document.getElementById('google-map'), mapOptions);
    
    
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
    
    
    for (var i=0;i<folklor_map_menu.length;i++) {
        add_marker(i);
    }

    
    if (typeof(mapIdle)=='function') {
        google.maps.event.addListener(map, 'idle', function(ev){
            var center=map.getCenter();
            mapIdle(center.lat(),center.lng(),map.getZoom());
        });
    }

}


google.maps.event.addDomListener(window, 'load', function() {
    initialize();    
});

