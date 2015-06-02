function initialize() {
    var aesaLatlng = new google.maps.LatLng(52.1769581,17.1201913);
    var mapOptions = {
        zoom: 8,
        center: aesaLatlng
    }
    var map = new google.maps.Map(document.getElementById('google-map'), mapOptions);

}


google.maps.event.addDomListener(window, 'load', function() {
    initialize();    
});