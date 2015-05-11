
function positionFoundZip(position)
{
    var target = document.getElementById('outer_store_results');
    var spinner = new Spinner().spin(target);        
    $.ajax({ url:ajax_gmap_path+'?latlng='+position.coords.latitude+','+position.coords.longitude+'&sensor=true',
           success: function(data){
                for (i=0; i<data.results[0].address_components.length;i++)
                {
                    if (data.results[0].address_components[i].types[0]=='postal_code') {
                        $('#myzip').html(data.results[0].address_components[i].long_name);
                        break;
                    }
                }
                
                $.ajax({
                    url:ajax_path+'?lat='+data.results[0].geometry.location.lat+'&lng='+data.results[0].geometry.location.lng+'&lang='+lang+'&products='+products,
                    success: function (recipients) {
                            spinner.stop();
                        showResults(recipients.results);
                    }
                });
                
                
                
           }
    });
    
}



function zipSearch()
{
    zipInput=document.getElementById('myzip');
    if (zipInput==null) return;
    
    zip=zipInput.value;
    
    if (zip.length!=6) {
        alert(zipInput.title);
    } else {
            var target = document.getElementById('outer_store_results');
            var spinner = new Spinner().spin(target);
	    $.ajax({ url:ajax_gmap_path+'?address='+zip+','+lang+'&sensor=true',
               success: function(data){
                
                    $.ajax({
                        url:ajax_path+'?lat='+data.results[0].geometry.location.lat+'&lng='+data.results[0].geometry.location.lng+'&lang='+lang+'&products='+products,
                        success: function (recipients) {
                            spinner.stop();
                            var html='<a href="javascript:showAllProvince(true)" title="'+$('#country_path a').attr('title')+'">'+$('#country_path a').attr('title')+'</a> / '+zip;
                            $('#country_path').html(html);
                            showResults(recipients.results);
                        }
                    });
                    
                    
               }
        });        
    }
}


function showAllProvince(forgetall)
{
    if (!forgetall)
    {
	    zipInput=document.getElementById('myzip');
	    if (zipInput!=null && zipInput.value.length>0) return zipSearch();
	    if (last_province!=null) return showProvince(last_province);
    }

    last_province = null;
    
    if (navigator.geolocation) navigator.geolocation.getCurrentPosition(positionFoundZip);
    document.getElementById('localMap').style.display='block';
    document.getElementById('map-canvas').style.display='none';
    
    var html='<a href="javascript:showAllProvince(true)" title="'+$('#country_path a').attr('title')+'">'+$('#country_path a').attr('title')+'</a>';
    $('#country_path').html(html);
    $('.hotspot').find('img').attr('src', $('.hotspot:first').find('img').attr('rel'));
                    $('#outer_store_results').jScrollPane(
                    {
                            autoReinitialise: true,
                    }
            );
}



function initGoogleMap() {
    return;
    var mapOptions = {
        zoom: 8,
        center: new google.maps.LatLng(-34.397, 150.644),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
  
    var map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);
}


function showMap(id)
{
    $.ajax({
        url:ajax_path+'?id='+id+'&lang='+lang+'&products='+products,
        success: function (recipients) {
            document.getElementById('localMap').style.display='none';
            document.getElementById('map-canvas').style.display='block';
            
            var mainLatlng = new google.maps.LatLng(recipients.result.lat,recipients.result.lng);
            
            
            var mapOptions = {
                zoom: 13,
                center: mainLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            
            var marker = new google.maps.Marker({
                position: mainLatlng,
                map: map,
                title: recipients.result.name,
                animation: google.maps.Animation.DROP
            });
            
            
            
            
           for (i=0;i<recipients.results.length;i++) {
        
                if (recipients.results[i].id==id) continue;
                
                new google.maps.Marker({
                    position: new google.maps.LatLng(recipients.results[i].lat,recipients.results[i].lng),
                    map: map,
                    title: recipients.results[i].name,
                    icon: 'http://maps.google.com/mapfiles/marker_purple.png'
                 });
            }
            
            
            console.log(recipients);
        }
    });
    
}

function showProvince(province)
{
    
    zipInput=document.getElementById('myzip');
    if (zipInput!=null) zipInput.value='';
    
    last_province = province;
    
    var target = document.getElementById('outer_store_results');
    var spinner = new Spinner().spin(target);
    $.ajax({
        url:ajax_path+'?province='+province+'&lang='+lang+'&products='+products,
        success: function (recipients) {
            
            var html='<a href="javascript:showAllProvince(true)" title="'+$('#country_path a').attr('title')+'">'+$('#country_path a').attr('title')+'</a> / '+province;
            $('#country_path').html(html);
            showResults(recipients.results);
            spinner.stop();
            $('#outer_store_results').jScrollPane(
                    {
                            autoReinitialise: true,
                    }
            );
        }
    });
}


function loadGoogleMap() {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initGoogleMap';
    document.body.appendChild(script);
}


function setProducts(p,all) {

    if (all==null) all=false;
    if (all) all_products=p;
    
    
    if (all || products.length==0) products=p;
    else {
        var pa=[];
    
        if (products.length>0) pa=products.split(',');
        products='';
        
        
        var found=false;
        for (var i=0;i<pa.length;i++)
        {
            if (pa[i]==p) {
                found=true;
            } else if (pa[i]!=all_products) {
                if (products.length>0) products+=',';
                products+=pa[i];
            }
        }
        
        if (!found) {
            if (products.length>0) products+=',';
            products+=p;
        }	    
        
        
        if (products.length==0) products=default_products;
        
        
    }
    

    
    if($('#box-special-container a[data='+p+']').hasClass('active'))
    {
            $('#box-special-container a[data='+p+']').removeClass('active');
    }
    else
    {
            $('#box-special-container a[data='+p+']').addClass('active');
    }
    
    if(all == true)
    {

    	$("#box-special-container li a").removeClass('active');
    	$('#box-special-container ul > li:first-child a').addClass('active');
    }
    else
    {
    	$('#box-special-container ul > li:first-child a').removeClass('active');
    }
    
    
    showAllProvince(false);
    return false;
}


jQuery(function(){
		  
		  //set active class on clicked image
		  
		  $('.hotspot').click(function(){
			  $('.hotspot').find('img').attr('src', $(this).find('img').attr('rel'));
			  $(this).find('img').attr('src', $(this).find('img').attr('data'));
		  });
		  
		  //active areas
		  $('#m_mapka > area').click(function(){

			  //take each .hotspot and clear images src to initial not active
			  $('.hotspot').each(function(){
			  	$(this).find('img').attr('src', $(this).find('img').attr('rel'));
			  });
			  
			  h = $(this).attr('data');
			  $(h).find('img').attr('src', $(h).find('img').attr('data'));
		  });
		  
		  
		  function resetClick()
		  {
			  $('.hotspot').find('img').attr('src', $('.hotspot:first').find('img').attr('rel'));
		  }
});




window.onload = loadGoogleMap;    
setProducts(default_products,false);