//http://jakdojade.pl/pages/api/http_get.html
//http://poznan.jakdojade.pl/?fc=nylat:mylng&td=Nazwa+biblioteki&tc=50.05434:19.93931

var myLat=0;
var myLng=0;

function wbp_pos_found (position) {
    myLat=position.coords.latitude;
    myLng=position.coords.longitude;
}

if (navigator.geolocation) navigator.geolocation.getCurrentPosition(wbp_pos_found);



function wbp_map(lat,lng, title) {

    var wbp_obj_marker;
    var wmp_obj_map;

    $('#wbp-obj-map-canvas').show();

    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
      zoom: 15,
      center: myLatlng
    }
    wmp_obj_map = new google.maps.Map(document.getElementById('wbp-obj-map-canvas'), mapOptions);
    
    wbp_obj_marker = new google.maps.Marker({
        position: myLatlng,
        map: wmp_obj_map,
        title: title
    });
    
    google.maps.event.addListener(wbp_obj_marker, 'click', function() {
        window.location.href = 'https://maps.google.pl/maps?q='+lat+','+lng;
    })
}



function objmap(a,lat,lng) {
    
    var nazwa=$(a).html();
    $("#obj_details").attr('title',nazwa);

    $("#obj_details .adres").html('');
    var adres=$(a).parent().parent().clone();
    adres.find('h4').remove();
    $("#obj_details .adres").append(adres);
    

         
    $("#obj_details").dialog({
      modal: true,
      buttons: {
        Zamknij: function() {
          $( this ).dialog( "close" );
        }
      }
    });
    
    $('#wbp-obj-map-canvas').html('');
    
    if (parseFloat(lat)!=NaN && parseFloat(lng)!=NaN ) {
        wbp_map(parseFloat(lat),parseFloat(lng),nazwa);    
        
        var url='http://poznan.jakdojade.pl/?td='+encodeURIComponent(nazwa)+'&tn='+encodeURIComponent(nazwa)+'&tc='+lat+':'+lng;
        if (myLat>0) url+='&fc='+myLat+':'+myLng;
        $("#obj_details .jakdojade a").prop('href',url);
    }
    
    
    return false;
}

function objmapid(id) {
    var url=$("#obj_details").attr('rel');
    url+=url.indexOf('?')>0?'&':'?';
    url+='id='+id;
    $.get(url,function(data) {
        if (typeof(data.id)!='undefined') {
            var nazwa=data.nazwa;
            
            var adres=$("#obj_details .adres_template").html();
            $("#obj_details .adres").html(smekta(adres,data));
            
            $("#obj_details").dialog({
              modal: true,
              fluid: true,
              width: 'auto',
              height: 'auto',
              maxWidth: 600,
              buttons: {
                Zamknij: function() {
                  $( this ).dialog( "close" );
                }
              }
            }).dialog('option', 'title', nazwa);;
            
            //$('.ui-dialog').css('width','');
            
            var lat=data.lat;
            var lng=data.lng;
            
  
            if (!isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng)) ) {
                wbp_map(parseFloat(lat),parseFloat(lng),nazwa);    
                
                var url='http://poznan.jakdojade.pl/?td='+encodeURIComponent(nazwa)+'&tn='+encodeURIComponent(nazwa)+'&tc='+lat+':'+lng;
                if (myLat>0) url+='&fc='+myLat+':'+myLng;
                $("#obj_details .jakdojade a").show().prop('href',url);
            }
            else
            {
                $('#wbp-obj-map-canvas').html('').hide();
                $("#obj_details .jakdojade a").hide();
                
            }
            
            
                      
            
            
            
            
        }
        
    });
    
    return false;  
}