function showResults(results)
{
    //console.log("Results: " + results);
    var html='';
    for (i=0;i<results.length;i++) {          
        if (results[i].newcity==1) html+='<h4>'+results[i].city+'</h4>';
        html+='<div class="info-container"><div class="info-data"><p><a href="javascript:showMap('+results[i].id+')"><span class="shop-name">'+results[i].name+'</a></span> '
        +'<span class="shop-address">' + results[i].street + ', ' + results[i].zip + ' ' + results[i].city + '</span>';
        if(results[i].tel1)
        {
            html+='<span class="shop-phone">tel: ' + results[i].tel1 +'</span>';
        }
        if(results[i].tel2)
        {
            html+='<span class="shop-phone">tel: ' + results[i].tel2 +'</span>';
        }
        if(results[i].hours_week)
        {
            html+='<span class="shop-hours-title">Godziny otwarcia: </span> <span class="shop-hours">&nbsp; ' + results[i].hours_week +'</span><br>';
        }
        if(results[i].hours_sa)
        {
            html+='<span class="shop-hours-title">Soboty: </span> <span class="shop-hours">&nbsp; ' + results[i].hours_sa +',&nbsp;</span>';
        }
        if(results[i].hours_su)
        {
            html+='<span class="shop-hours-title">Niedziele: </span> <span class="shop-hours">&nbsp; ' + results[i].hours_su +'</span><br>';
        }
        
        html+='</p></div>';
        html+='<div class="img-data"><a href="javascript:showMap('+results[i].id+')"><img src="'+IMAGES+'/on_map.png"></a></div></div>';
    }
    $('#store_results').html(html);        
}      
      
