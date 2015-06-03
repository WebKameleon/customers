var main_map, dir, directionsService, directionsDisplay, distance, duration, points = 0, activInfos = [], ppoGlobal = [], ouaGlobal = [], spoGlobal = [], nodesTable = [];
var App = (function($) {
	var App = {};
	//App.BASEURL = "http://aesa.e-surf.pl/templates/beez_20";
	App.BASEURL = "http://www.aesa.pl/templates/beez_20";
	//App.BASEURL = "http://aesa.localhost/templates/beez_20";
	App.LANG = "pl";
	return App
}(jQuery));



var ibc = {
	markerPrototype : function(map, coords, image, _title) {
		if (image == undefined) {
			var image = App.BASEURL + "/images/autostrada/map/point.png"
		}
		return new google.maps.Marker( {
			position : coords,
			map : map,
			icon : image,
			title : _title
		})
	},
	servicesActive : function(lang, dataObj) {
		var content = new String();
		$
				.each(
						pointTypes,
						function(i) {
							//var tmp_in = '<span class="ico ' + pointTypes[i] + '"></span>'; 
							content += ibc.isServiceSelected(dataObj,
									pointTypes[i]) ? '<span class="ico ' + pointTypes[i] + '"></span>'
									: "";
						})
		return content
	},
	isServiceSelected : function(pointPattern, service) {
		return PATTERN[service] == pointPattern[service]
	},
	createInfoWindows : function(lang) {
		activInfos = [];
		for ( var i = 0; i < infoBoxesData.length; i++) {
			var me = infoBoxesData[i];
			activInfos[i] = ibc.getSmoke(300, me.pointerPosition, 5,
					(me.pointerPosition == "top" ? 0 : 15), 5);
			activInfos[i].open(null, ibc.markerPrototype(null, me.cordinates));
		}
		ibc.hideInfoWindows()
	},
	hideInfoWindows : function(items) {
		$.each(activInfos, function(i) {
			activInfos[i].close();
		})
	},
	showInfoWindows : function() {
		$.each(activInfos, function() {
			activInfos[i].open(main_map)
		})
	},
	showByPatern : function() {
		$.each(activInfos, function(i) {
			var me = activInfos[i];
			if (ibc.comparePaterns(infoBoxesData[i])) {
				me.setContent(ibc.prepareInfo(infoBoxesData[i]));
				me.open(main_map)
			} else {
				me.close()
			}
		})
	},
	prepareInfo : function(data) {
		return '<div  class="info-bouble"><h3>' + data.namePl + "</h3>"
				+ ((App.LANG == "pl") ? data.contentPl : data.contentEn)
				+ "<br>"
				+ '<div class="in_bubble">' + ibc.servicesActive("pl", data.pattern, PATTERN) + '</div>'
				+ "</div>"
	},
	updatePattern : function(obj) {
		PATTERN[$.grep(obj.attr('class').split(" "), function(e, i) {
			return e != "selected"
		}).pop()] = obj.hasClass("selected") ? true : "no-activ";
	},
	comparePaterns : function(obj) {
		var pat = obj.pattern;
		return ((PATTERN.wc == pat.wc) || (PATTERN.parking == pat.parking)
				|| (PATTERN.cafe == pat.cafe)
				|| (PATTERN.gas == pat.gas)
				|| (PATTERN.restaurant == pat.restaurant) || (PATTERN.hotel == pat.hotel))
	},
	
	controlMarkers : function (points, visibility, globalArray, imageUrl){
		var image = imageUrl;
		var lang=(App.LANG == 'pl')?0:1;
		if (visibility) {
			for (var i = 0; i< points.length; i++) {
				globalArray[i]=  ibc.markerPrototype(main_map,points[i].coords, image);
				globalArray[i].setMap(main_map);
                                
                                      var smoke = new InfoBubble({
                                        maxWidth: 300,
                                        arrowDirection: 'bottom',
                                        borderRadius: 5,
                                        arrowSize: 15, 
                                        padding: 3,
                                        disableAutoPan: true   
                                });
                    
                    
                       globalArray[i].preview = '<div class="placesBouble"><h3>'+points[i].name+'</h3>'+points[i].description[lang]+'<div>';
                     
			
                         google.maps.event.addListener(globalArray[i], "mouseover", function(){
                           smoke.setContent(this.preview);
                            smoke.open(main_map, this);
                        });
                        google.maps.event.addListener(globalArray[i], "mouseout", function(){
                             smoke.close();
                        });
                                
                                
				globalArray[i].setMap(main_map);
			
			}
		} else {
			for (var i = 0; i< points.length; i++) {
				globalArray[i].setMap(null);
			}
		}
		
	},
	/**************************************************
	controlMarkers : function(points, visibility, globalArray, imageUrl) {
		var lang = (App.LANG == "pl") ? 0 : 1;
		$.each(points, function(i) {
			if (typeof globalArray[i] == 'undefined') {
				globalArray[i] = ibc.createMarker(this.name,
						this.description[lang], this.coords, imageUrl, "", ibc
								.getSmoke(), "placesBouble", null);
			}
			globalArray[i].setMap(visibility ? main_map : null);
		})
	}, 
	*********************************************/
	createMarker : function(name, desc, coords, _title, smoke, _class, _image) {
		var ret = ibc.markerPrototype(main_map, coords, _image, _title);
		ret.preview = '<div class="' + _class + '"><h3>' + name + "</h3>"
				+ desc + "<div>";
		google.maps.event.addListener(ret, "mouseover", function() {
			smoke.setContent(this.preview);
			smoke.open(main_map, this)
		});
		google.maps.event.addListener(ret, "mouseout", function() {
			smoke.close()
		});
		return ret
	},
	getSmoke : function(_maxWidth, _arrowDirection, _borderRadius, _arrowSize,
			_padding, _disableAutoPan) {
		return new InfoBubble( {
			maxWidth : _maxWidth != null ? _maxWidth : 300,
			arrowDirection : _arrowDirection != null ? _arrowDirection
					: "bottom",
			borderRadius : _borderRadius != null ? _borderRadius : 5,
			arrowSize : _arrowSize != null ? _arrowSize : 15,
			padding : _padding != null ? _padding : 3,
			disableAutoPan : _disableAutoPan != null ? _disableAutoPan : true
		})

	},
	getMapImage : function(f) {
		return App.BASEURL + "/images/autostrada/map/" + f
	},
	
	nodeMarkers : function (points, visibility, image){//konstrukoor w?z??w
		var rozmiar = new google.maps.Size(15, 16),
			startPoint = new google.maps.Point(0, 0),
			hangPoint = new google.maps.Point(7,7),
			nodeImage = new google.maps.MarkerImage(App.BASEURL+'/images/autostrada/map/node.png', rozmiar, startPoint, hangPoint),
			halfNodeImage = new google.maps.MarkerImage(App.BASEURL+'/images/autostrada/map/half-node.png', rozmiar, startPoint, hangPoint),
			warningNodeImage = new google.maps.MarkerImage(App.BASEURL+'/images/autostrada/map/warning-node.png', rozmiar, startPoint, hangPoint);
		for(var i=0 ; i< roadNodes.length; i++){
			if(roadNodes[i].type == 'HalfNode') {
				var ico = halfNodeImage;
			} else if(roadNodes[i].type == 'WarningNode') {	
				var ico = warningNodeImage;
			} else {
				var ico = nodeImage;
			}	
			var titleRoadNode = roadNodes[i].name;
			if (App.LANG == 'en'){
				if (roadNodes[i].name_en)
					titleRoadNode = roadNodes[i].name_en;
			}
			var marker = new google.maps.Marker({
				title: titleRoadNode,
				icon: ico,
				position: roadNodes[i].coords,
				map: main_map
			});
                              
                        var smoke = new InfoBubble({
                                maxWidth: 480,
                                arrowDirection: 'bottom',
                                borderRadius: 5,
                                arrowSize: 15, 
                                padding: 3,
                                disableAutoPan: true   
                        });
                    var h3='', dscr='';
                    if(App.LANG == 'en'){
                       h3= marker.title+'&nbsp;Interchange';
                       dscr= roadNodes[i].description[1];
                    } else {
                       h3= 'Węzeł&nbsp;drogowy&nbsp;'+marker.title;
                        dscr= roadNodes[i].description[0];
                    }
                       marker .preview = '<div class="placesBouble"><h3>'+h3+'</h3>'+dscr+'<div>';
                     
			
                         google.maps.event.addListener(marker , "mouseover", function(){
                           smoke.setContent(this.preview);
                            smoke.open(main_map, this);
                        });
                        google.maps.event.addListener(marker , "mouseout", function(){
                             smoke.close();
                        });
                                
		}
	},
	/*************************************
	nodeMarkers : function(points, visibility, image) {
		var rozmiar = new google.maps.Size(15, 16);
		var startPoint = new google.maps.Point(0, 0);
		var hangPoint = new google.maps.Point(7, 7);
		var nodeName = "", dscr = "";
		for ( var i = 0; i < roadNodes.length; i++) {
			var me = roadNodes[i];
			var ico = new google.maps.MarkerImage(ibc
					.getMapImage((me.type != "HalfNode" ? "node.png"
							: "half-node.png")), rozmiar, startPoint, hangPoint);

			if (App.LANG == "en") {
				nodeName = me.name + "&nbsp;Interchange";
				dscr = me.description[1]
			} else {
				nodeName = "Węzeł&nbsp;drogowy&nbsp;" + me.name;
				dscr = me.description[0]
			}

			var marker = ibc.createMarker(nodeName, dscr, me.coords, me.name,
					ibc.getSmoke(480), "placesBouble", ico);

		}
	},
	************************************/
	bindingActions : function() {
		$(".info-conroler-icons a").click(
				function(e) {
					var me = $(this), btnClass = me.attr("class").replace(
							"selected", "");
					all = $("a.all");
					if (all.hasClass("selected")) {
						all.click()
					}
					e.preventDefault();
					$(".info-conroler-icons a." + btnClass).toggleClass(
							"selected");
					ibc.updatePattern(me);
					ibc.showByPatern()
				});
		$(".controls a.ppo").click(
				function(e) {
					var me = $(this);
					e.preventDefault();
					$(".markers-controls a.ppo").toggleClass("selected");
					var imageUrl = App.BASEURL
							+ "/images/autostrada/map/marker_ppo.png";
					ibc.controlMarkers(ppos1, me.hasClass("selected"),
							ppoGlobal, imageUrl)
				});
		$(".controls a.oua").click(
				function(e) {
					var me = $(this);
					e.preventDefault();
					$(".markers-controls a.oua").toggleClass("selected");
					var imageUrl = App.BASEURL
							+ "/images/autostrada/map/marker_setting.png";
					ibc.controlMarkers(ousas, me.hasClass("selected"),
							ouaGlobal, imageUrl)
				});
		$(".controls a.spo").click(
				function(e) {
					var me = $(this);
					e.preventDefault();
					$(".markers-controls a.spo").toggleClass("selected");
					var imageUrl = App.BASEURL
							+ "/images/autostrada/map/marker_spo.png";
					ibc.controlMarkers(spos1, me.hasClass("selected"),
							spoGlobal, imageUrl)
				});
		$(".markers-controls a.mop").click(
				function(e) {
					var me = $(this);
					e.preventDefault();
					$(".markers-controls a.mop").toggleClass("selected");
					var imageUrl = App.BASEURL
							+ "/images/autostrada/map/marker_mop.png";
					ibc.controlMarkers(spos1, me.hasClass("selected"),
							spoGlobal, imageUrl)
				});

	}
};

function showMap() {
	//var centerLatLong = new google.maps.LatLng(52.306576, 17.566821); 
	var centerLatLong = new google.maps.LatLng(52.346576, 16.500000);
	var mapOptions = {
		scrollwheel : false,
		streetViewControl : false,
		zoom : 8,
		zoomControlOptions : {
			style: google.maps.ZoomControlStyle.SMALL
			/* style : google.maps.ZoomControlStyle.LARGE,
			position : google.maps.ControlPosition.RIGHT_CENTER */
		},
		center : centerLatLong,
		mapTypeControl : true,
		mapTypeControlOptions : {
			style : google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
			position : google.maps.ControlPosition.TOP_LEFT
		},
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	main_map = new google.maps.Map(document.getElementById("main_map"),
			mapOptions);
	new google.maps.Polyline( {
		strokeColor : "#7fbb0e",
		strokeOpacity : 1,
		strokeWeight : 3,
		path : [ new google.maps.LatLng(52.315523, 14.577875),
				new google.maps.LatLng(52.316441, 14.588131),
				new google.maps.LatLng(52.316677, 14.590234),
				new google.maps.LatLng(52.317359, 14.594097),
				new google.maps.LatLng(52.31791, 14.595985),
				new google.maps.LatLng(52.320638, 14.605984),
				new google.maps.LatLng(52.323419, 14.61637),
				new google.maps.LatLng(52.325176, 14.623022),
				new google.maps.LatLng(52.325806, 14.626412),
				new google.maps.LatLng(52.32612, 14.629158),
				new google.maps.LatLng(52.326278, 14.63315),
				new google.maps.LatLng(52.32612, 14.636669),
				new google.maps.LatLng(52.325425, 14.643878),
				new google.maps.LatLng(52.324612, 14.652419),
				new google.maps.LatLng(52.323996, 14.660551),
				new google.maps.LatLng(52.324193, 14.666709),
				new google.maps.LatLng(52.324691, 14.671967),
				new google.maps.LatLng(52.325543, 14.678211),
				new google.maps.LatLng(52.326488, 14.685721),
				new google.maps.LatLng(52.327865, 14.696364),
				new google.maps.LatLng(52.328796, 14.703509),
				new google.maps.LatLng(52.329687, 14.710311),
				new google.maps.LatLng(52.330553, 14.717221),
				new google.maps.LatLng(52.331392, 14.72383),
				new google.maps.LatLng(52.332349, 14.731233),
				new google.maps.LatLng(52.333083, 14.737091),
				new google.maps.LatLng(52.333896, 14.743399),
				new google.maps.LatLng(52.334578, 14.748806),
				new google.maps.LatLng(52.334945, 14.75239),
				new google.maps.LatLng(52.335181, 14.757454),
				new google.maps.LatLng(52.335221, 14.764106),
				new google.maps.LatLng(52.335404, 14.775521),
				new google.maps.LatLng(52.335417, 14.781615),
				new google.maps.LatLng(52.33547, 14.790627),
				new google.maps.LatLng(52.33547, 14.797537),
				new google.maps.LatLng(52.335417, 14.801142),
				new google.maps.LatLng(52.334985, 14.804983),
				new google.maps.LatLng(52.333975, 14.81039),
				new google.maps.LatLng(52.332651, 14.817514),
				new google.maps.LatLng(52.331431, 14.824316),
				new google.maps.LatLng(52.33012, 14.830861),
				new google.maps.LatLng(52.328809, 14.838542),
				new google.maps.LatLng(52.328573, 14.842255),
				new google.maps.LatLng(52.328914, 14.847898),
				new google.maps.LatLng(52.329451, 14.851481),
				new google.maps.LatLng(52.330723, 14.859163),
				new google.maps.LatLng(52.331772, 14.865),
				new google.maps.LatLng(52.332887, 14.871973),
				new google.maps.LatLng(52.333582, 14.879033),
				new google.maps.LatLng(52.334382, 14.882831),
				new google.maps.LatLng(52.335116, 14.888839),
				new google.maps.LatLng(52.335142, 14.893453),
				new google.maps.LatLng(52.334985, 14.901671),
				new google.maps.LatLng(52.334657, 14.907529),
				new google.maps.LatLng(52.33425, 14.911992),
				new google.maps.LatLng(52.334132, 14.916284),
				new google.maps.LatLng(52.334329, 14.921433),
				new google.maps.LatLng(52.335208, 14.927914),
				new google.maps.LatLng(52.336322, 14.933814),
				new google.maps.LatLng(52.337266, 14.939115),
				new google.maps.LatLng(52.338459, 14.946045),
				new google.maps.LatLng(52.338826, 14.950573),
				new google.maps.LatLng(52.338682, 14.955766),
				new google.maps.LatLng(52.337948, 14.960658),
				new google.maps.LatLng(52.336899, 14.965293),
				new google.maps.LatLng(52.335431, 14.970958),
				new google.maps.LatLng(52.334382, 14.976193),
				new google.maps.LatLng(52.333149, 14.983017),
				new google.maps.LatLng(52.332703, 14.99027),
				new google.maps.LatLng(52.332625, 14.998252),
				new google.maps.LatLng(52.332625, 15.003316),
				new google.maps.LatLng(52.332834, 15.013487),
				new google.maps.LatLng(52.332834, 15.017821),
				new google.maps.LatLng(52.332074, 15.0231),
				new google.maps.LatLng(52.331051, 15.029451),
				new google.maps.LatLng(52.329661, 15.035502),
				new google.maps.LatLng(52.326671, 15.049922),
				new google.maps.LatLng(52.325124, 15.059406),
				new google.maps.LatLng(52.324783, 15.065543),
				new google.maps.LatLng(52.325124, 15.079233),
				new google.maps.LatLng(52.325334, 15.095927),
				new google.maps.LatLng(52.326173, 15.116097),
				new google.maps.LatLng(52.326619, 15.131676),
				new google.maps.LatLng(52.32696, 15.14455),
				new google.maps.LatLng(52.326383, 15.153391),
				new google.maps.LatLng(52.32536, 15.16236),
				new google.maps.LatLng(52.324416, 15.174891),
				new google.maps.LatLng(52.322737, 15.187766),
				new google.maps.LatLng(52.319956, 15.19283),
				new google.maps.LatLng(52.316494, 15.199525),
				new google.maps.LatLng(52.313162, 15.208709),
				new google.maps.LatLng(52.31185, 15.215747),
				new google.maps.LatLng(52.311456, 15.219352),
				new google.maps.LatLng(52.310905, 15.226089),
				new google.maps.LatLng(52.310276, 15.237419),
				new google.maps.LatLng(52.308964, 15.244371),
				new google.maps.LatLng(52.306209, 15.251452),
				new google.maps.LatLng(52.302115, 15.25686),
				new google.maps.LatLng(52.299281, 15.260636),
				new google.maps.LatLng(52.297916, 15.262954),
				new google.maps.LatLng(52.296394, 15.26746),
				new google.maps.LatLng(52.294871, 15.275657),
				new google.maps.LatLng(52.294609, 15.283038),
				new google.maps.LatLng(52.294976, 15.291492),
				new google.maps.LatLng(52.303453, 15.319559),
				new google.maps.LatLng(52.306392, 15.337755),
				new google.maps.LatLng(52.31164, 15.358011),
				new google.maps.LatLng(52.315575, 15.375735),
				new google.maps.LatLng(52.317779, 15.386207),
				new google.maps.LatLng(52.319038, 15.394446),
				new google.maps.LatLng(52.319038, 15.404403),
				new google.maps.LatLng(52.318723, 15.416076),
				new google.maps.LatLng(52.318304, 15.426547),
				new google.maps.LatLng(52.317884, 15.435988),
				new google.maps.LatLng(52.317464, 15.441825),
				new google.maps.LatLng(52.316835, 15.451953),
				new google.maps.LatLng(52.316835, 15.461566),
				new google.maps.LatLng(52.317359, 15.473754),
				new google.maps.LatLng(52.318828, 15.4868),
				new google.maps.LatLng(52.319773, 15.4971),
				new google.maps.LatLng(52.321032, 15.507056),
				new google.maps.LatLng(52.322815, 15.519587),
				new google.maps.LatLng(52.323497, 15.527055),
				new google.maps.LatLng(52.324022, 15.533063),
				new google.maps.LatLng(52.324363, 15.538084),
				new google.maps.LatLng(52.324258, 15.54332),
				new google.maps.LatLng(52.32376, 15.551087),
				new google.maps.LatLng(52.322212, 15.560657),
				new google.maps.LatLng(52.319694, 15.57306),
				new google.maps.LatLng(52.318776, 15.578639),
				new google.maps.LatLng(52.316887, 15.58881),
				new google.maps.LatLng(52.314893, 15.59868),
				new google.maps.LatLng(52.312007, 15.610525),
				new google.maps.LatLng(52.308832, 15.621769),
				new google.maps.LatLng(52.305159, 15.634086),
				new google.maps.LatLng(52.300619, 15.649921),
				new google.maps.LatLng(52.299412, 15.656316),
				new google.maps.LatLng(52.298651, 15.663397),
				new google.maps.LatLng(52.297837, 15.677773),
				new google.maps.LatLng(52.297286, 15.689704),
				new google.maps.LatLng(52.296892, 15.703008),
				new google.maps.LatLng(52.29684, 15.714337),
				new google.maps.LatLng(52.297443, 15.721676),
				new google.maps.LatLng(52.298205, 15.727598),
				new google.maps.LatLng(52.299543, 15.734765),
				new google.maps.LatLng(52.301695, 15.743906),
				new google.maps.LatLng(52.304136, 15.752317),
				new google.maps.LatLng(52.308465, 15.76678),
				new google.maps.LatLng(52.31164, 15.77708),
				new google.maps.LatLng(52.314919, 15.786435),
				new google.maps.LatLng(52.316756, 15.793645),
				new google.maps.LatLng(52.321661, 15.810039),
				new google.maps.LatLng(52.325229, 15.822355),
				new google.maps.LatLng(52.326933, 15.830767),
				new google.maps.LatLng(52.328428, 15.842182),
				new google.maps.LatLng(52.328979, 15.85467),
				new google.maps.LatLng(52.328927, 15.866773),
				new google.maps.LatLng(52.329084, 15.880849),
				new google.maps.LatLng(52.329425, 15.895483),
				new google.maps.LatLng(52.32953, 15.908615),
				new google.maps.LatLng(52.329792, 15.919344),
				new google.maps.LatLng(52.330159, 15.929386),
				new google.maps.LatLng(52.331156, 15.941445),
				new google.maps.LatLng(52.33252, 15.951745),
				new google.maps.LatLng(52.333542, 15.957496),
				new google.maps.LatLng(52.33821, 15.977666),
				new google.maps.LatLng(52.341383, 15.990669),
				new google.maps.LatLng(52.343506, 16.000883),
				new google.maps.LatLng(52.345709, 16.014316),
				new google.maps.LatLng(52.347229, 16.024916),
				new google.maps.LatLng(52.349064, 16.037061),
				new google.maps.LatLng(52.350742, 16.049892),
				new google.maps.LatLng(52.352341, 16.061522),
				new google.maps.LatLng(52.355277, 16.081264),
				new google.maps.LatLng(52.358029, 16.095039),
				new google.maps.LatLng(52.359460000000006, 16.10404),

				new google.maps.LatLng(52.359460000000006, 16.10404),
				new google.maps.LatLng(52.362350000000006, 16.11938),
				new google.maps.LatLng(52.363020000000006, 16.12358),
				new google.maps.LatLng(52.363670000000006, 16.12865),
				new google.maps.LatLng(52.3669, 16.16109),
				new google.maps.LatLng(52.36713, 16.163870000000003),
				new google.maps.LatLng(52.36732000000001, 16.167440000000003),
				new google.maps.LatLng(52.36741000000001, 16.17059),
				new google.maps.LatLng(52.367630000000005, 16.19),
				new google.maps.LatLng(52.368, 16.19623),
				new google.maps.LatLng(52.368500000000004, 16.20091),
				new google.maps.LatLng(52.36887, 16.2036),
				new google.maps.LatLng(52.3695, 16.207340000000002),
				new google.maps.LatLng(52.374810000000004, 16.23431),
				new google.maps.LatLng(52.375600000000006, 16.239530000000002),
				new google.maps.LatLng(52.376310000000004, 16.24615),
				new google.maps.LatLng(52.37666, 16.25147),
				new google.maps.LatLng(52.377370000000006, 16.27154),
				new google.maps.LatLng(52.37802000000001, 16.285880000000002),
				new google.maps.LatLng(52.382850000000005, 16.37357),
				new google.maps.LatLng(52.3834, 16.38024),
				new google.maps.LatLng(52.384130000000006, 16.38682),
				new google.maps.LatLng(52.38778000000001, 16.41232),
				new google.maps.LatLng(52.38870000000001, 16.42039),
				new google.maps.LatLng(52.389050000000005, 16.424400000000002),
				new google.maps.LatLng(52.389450000000004, 16.430580000000003),
				new google.maps.LatLng(52.38993000000001, 16.443250000000003),
				new google.maps.LatLng(52.389990000000004, 16.44807),
				new google.maps.LatLng(52.38991000000001, 16.451620000000002),
				new google.maps.LatLng(52.38978, 16.45427),
				new google.maps.LatLng(52.389320000000005, 16.45981),
				new google.maps.LatLng(52.388850000000005, 16.46358),
				new google.maps.LatLng(52.38725, 16.47428),
				new google.maps.LatLng(52.3868, 16.4783),
				new google.maps.LatLng(52.38658, 16.48141),
				new google.maps.LatLng(52.386410000000005, 16.486220000000003),
				new google.maps.LatLng(52.38645, 16.48959),
				new google.maps.LatLng(52.38741, 16.50766),
				new google.maps.LatLng(52.3875, 16.511760000000002),
				new google.maps.LatLng(52.387480000000004, 16.51424),
				new google.maps.LatLng(52.38731000000001, 16.51883),
				new google.maps.LatLng(52.38700000000001, 16.52298),
				new google.maps.LatLng(52.38646000000001, 16.52783),
				new google.maps.LatLng(52.38564, 16.53303),
				new google.maps.LatLng(52.384620000000005, 16.538),
				new google.maps.LatLng(52.383390000000006, 16.54279),
				new google.maps.LatLng(52.377230000000004, 16.56389),
				new google.maps.LatLng(52.37568, 16.56897),
				new google.maps.LatLng(52.374430000000004, 16.5728),
				new google.maps.LatLng(52.37268, 16.577910000000003),
				new google.maps.LatLng(52.370740000000005, 16.58323),
				new google.maps.LatLng(52.358560000000004, 16.6151),
				new google.maps.LatLng(52.35746, 16.618270000000003),
				new google.maps.LatLng(52.356370000000005, 16.621950000000002),
				new google.maps.LatLng(52.35542, 16.62583),
				new google.maps.LatLng(52.354560000000006, 16.630280000000003),
				new google.maps.LatLng(52.34928000000001, 16.664150000000003),
				new google.maps.LatLng(52.34893, 16.66685),
				new google.maps.LatLng(52.348620000000004, 16.67002),
				new google.maps.LatLng(52.34835, 16.6749),
				new google.maps.LatLng(52.34834000000001, 16.680200000000003),
				new google.maps.LatLng(52.34995000000001, 16.71883),
				new google.maps.LatLng(52.35025, 16.727890000000002),
				new google.maps.LatLng(52.350280000000005, 16.732120000000002),
				new google.maps.LatLng(52.350410000000004, 16.73627),
				new google.maps.LatLng(52.35022000000001, 16.834200000000003),
				new google.maps.LatLng(52.35027, 16.839080000000003),
				new google.maps.LatLng(52.35045, 16.84371),
				new google.maps.LatLng(52.350660000000005, 16.847240000000003),
				new google.maps.LatLng(52.351020000000005, 16.85173),
				new google.maps.LatLng(52.35148, 16.85595),
				new google.maps.LatLng(52.35374, 16.87293),
				new google.maps.LatLng(52.354130000000005, 16.876330000000003),
				new google.maps.LatLng(52.354580000000006, 16.883850000000002),
				new google.maps.LatLng(52.354580000000006, 16.888840000000002),
				new google.maps.LatLng(52.354440000000004, 16.89376),
				new google.maps.LatLng(52.35417, 16.89748),
				new google.maps.LatLng(52.346090000000004, 16.997390000000003),
				new google.maps.LatLng(52.34508, 17.00748),
				new google.maps.LatLng(52.34460000000001, 17.01143),
				new google.maps.LatLng(52.34339000000001, 17.02016),
				new google.maps.LatLng(52.341930000000005, 17.029040000000002),
				new google.maps.LatLng(52.339690000000004, 17.040580000000002),
				new google.maps.LatLng(52.33223, 17.076610000000002),
				new google.maps.LatLng(52.33088000000001, 17.08277),
				new google.maps.LatLng(52.32875000000001, 17.09156),
				new google.maps.LatLng(52.327650000000006, 17.09562),
				new google.maps.LatLng(52.325680000000006, 17.102430000000002),
				new google.maps.LatLng(52.31172, 17.14729),
				new google.maps.LatLng(52.31036, 17.15143),
				new google.maps.LatLng(52.309900000000006, 17.15407),
				new google.maps.LatLng(52.30892000000001, 17.158890000000003),
				new google.maps.LatLng(52.308350000000004, 17.16234),
				new google.maps.LatLng(52.30780000000001, 17.166520000000002),
				new google.maps.LatLng(52.30733000000001, 17.17169),
				new google.maps.LatLng(52.307140000000004, 17.17539),
				new google.maps.LatLng(52.30706000000001, 17.178710000000002),
				new google.maps.LatLng(52.307120000000005, 17.18366),
				new google.maps.LatLng(52.307300000000005, 17.18736),
				new google.maps.LatLng(52.30776, 17.19274),
				new google.maps.LatLng(52.308150000000005, 17.1959),
				new google.maps.LatLng(52.30926, 17.20363),
				new google.maps.LatLng(52.30966, 17.20726),
				new google.maps.LatLng(52.31004, 17.212600000000002),
				new google.maps.LatLng(52.31017000000001, 17.217000000000002),
				new google.maps.LatLng(52.30979000000001, 17.348860000000002),
				new google.maps.LatLng(52.30982, 17.354740000000003),
				new google.maps.LatLng(52.31006000000001, 17.36193),
				new google.maps.LatLng(52.31031, 17.366300000000003),
				new google.maps.LatLng(52.31074, 17.371830000000003),
				new google.maps.LatLng(52.31127000000001, 17.37706),
				new google.maps.LatLng(52.31192000000001, 17.382270000000002),
				new google.maps.LatLng(52.312990000000006, 17.38934),
				new google.maps.LatLng(52.316750000000006, 17.41064),
				new google.maps.LatLng(52.317310000000006, 17.41429),
				new google.maps.LatLng(52.317780000000006, 17.417930000000002),
				new google.maps.LatLng(52.31828, 17.42284),
				new google.maps.LatLng(52.318580000000004, 17.42651),
				new google.maps.LatLng(52.318940000000005, 17.43412),
				new google.maps.LatLng(52.31899000000001, 17.440900000000003),
				new google.maps.LatLng(52.31803000000001, 17.50446),
				new google.maps.LatLng(52.31783000000001, 17.51086),
				new google.maps.LatLng(52.317420000000006, 17.517110000000002),
				new google.maps.LatLng(52.31685, 17.522730000000003),
				new google.maps.LatLng(52.316, 17.52898),
				new google.maps.LatLng(52.314980000000006, 17.534830000000003),
				new google.maps.LatLng(52.311730000000004, 17.551910000000003),
				new google.maps.LatLng(52.31116, 17.55524),
				new google.maps.LatLng(52.310810000000004, 17.55787),
				new google.maps.LatLng(52.31053000000001, 17.56015),
				new google.maps.LatLng(52.31018, 17.564510000000002),
				new google.maps.LatLng(52.309110000000004, 17.58201),
				new google.maps.LatLng(52.30883000000001, 17.58554),
				new google.maps.LatLng(52.308170000000004, 17.590780000000002),
				new google.maps.LatLng(52.307640000000006, 17.594070000000002),
				new google.maps.LatLng(52.307100000000005, 17.5969),
				new google.maps.LatLng(52.29412000000001, 17.65362),
				new google.maps.LatLng(52.292660000000005, 17.65943),
				new google.maps.LatLng(52.29144, 17.663790000000002),
				new google.maps.LatLng(52.289680000000004, 17.669410000000003),
				new google.maps.LatLng(52.28853, 17.67276),
				new google.maps.LatLng(52.28708, 17.67669),
				new google.maps.LatLng(52.27722000000001, 17.70118),
				new google.maps.LatLng(52.27423, 17.70899),
				new google.maps.LatLng(52.27228, 17.714660000000002),
				new google.maps.LatLng(52.270970000000005, 17.718950000000003),
				new google.maps.LatLng(52.26917, 17.72551),
				new google.maps.LatLng(52.26834, 17.72884),
				new google.maps.LatLng(52.267320000000005, 17.73339),
				new google.maps.LatLng(52.26644, 17.737820000000003),
				new google.maps.LatLng(52.26549000000001, 17.74331),
				new google.maps.LatLng(52.26484000000001, 17.74774),
				new google.maps.LatLng(52.264010000000006, 17.75447),
				new google.maps.LatLng(52.26203, 17.77493),
				new google.maps.LatLng(52.261500000000005, 17.778740000000003),
				new google.maps.LatLng(52.260810000000006, 17.78283),
				new google.maps.LatLng(52.2573, 17.80261),
				new google.maps.LatLng(52.255610000000004, 17.81163),
				new google.maps.LatLng(52.253640000000004, 17.82167),
				new google.maps.LatLng(52.25216, 17.82891),
				new google.maps.LatLng(52.249930000000006, 17.83933),
				new google.maps.LatLng(52.248380000000004, 17.846320000000002),
				new google.maps.LatLng(52.24593, 17.856900000000003),
				new google.maps.LatLng(52.24268000000001, 17.870230000000003),
				new google.maps.LatLng(52.24063, 17.87798),
				new google.maps.LatLng(52.23921000000001, 17.882460000000002),
				new google.maps.LatLng(52.236250000000005, 17.890710000000002),
				new google.maps.LatLng(52.234570000000005, 17.8956),
				new google.maps.LatLng(52.233430000000006, 17.899250000000002),
				new google.maps.LatLng(52.23223, 17.903550000000003),
				new google.maps.LatLng(52.23064, 17.910230000000002),
				new google.maps.LatLng(52.22688, 17.929640000000003),
				new google.maps.LatLng(52.224740000000004, 17.93946),
				new google.maps.LatLng(52.22316000000001, 17.94591),
				new google.maps.LatLng(52.22164, 17.95081),
				new google.maps.LatLng(52.22124, 17.95232),
				new google.maps.LatLng(52.22059, 17.955250000000003),
				new google.maps.LatLng(52.216950000000004, 17.968140000000002),
				new google.maps.LatLng(52.21399, 17.979740000000003),
				new google.maps.LatLng(52.21284000000001, 17.984620000000003),
				new google.maps.LatLng(52.21128, 17.991670000000003),
				new google.maps.LatLng(52.206880000000005, 18.01328),
				new google.maps.LatLng(52.20562, 18.01884),
				new google.maps.LatLng(52.20423, 18.024130000000003),
				new google.maps.LatLng(52.203100000000006, 18.027890000000003),
				new google.maps.LatLng(52.20168, 18.03218),
				new google.maps.LatLng(52.19809000000001, 18.042370000000002),
				new google.maps.LatLng(52.196450000000006, 18.04768),
				new google.maps.LatLng(52.19543, 18.05167),
				new google.maps.LatLng(52.194860000000006, 18.05423),
				new google.maps.LatLng(52.192690000000006, 18.06578),
				new google.maps.LatLng(52.1916, 18.07065),
				new google.maps.LatLng(52.19012000000001, 18.075940000000003),
				new google.maps.LatLng(52.18807, 18.08218),
				new google.maps.LatLng(52.18717, 18.0852),
				new google.maps.LatLng(52.18524000000001, 18.09243),
				new google.maps.LatLng(52.18394000000001, 18.097630000000002),
				new google.maps.LatLng(52.18289000000001, 18.10235),
				new google.maps.LatLng(52.182170000000006, 18.106090000000002),
				new google.maps.LatLng(52.181360000000005, 18.110870000000002),
				new google.maps.LatLng(52.18009000000001, 18.119510000000002),
				new google.maps.LatLng(52.179030000000004, 18.1259),
				new google.maps.LatLng(52.178430000000006, 18.128970000000002),
				new google.maps.LatLng(52.17714, 18.13464),
				new google.maps.LatLng(52.173500000000004, 18.149510000000003),
				new google.maps.LatLng(52.17219000000001, 18.15512),
				new google.maps.LatLng(52.17058, 18.16241),
				new google.maps.LatLng(52.1687, 18.17161),
				new google.maps.LatLng(52.166630000000005, 18.18278),
				new google.maps.LatLng(52.16353, 18.201810000000002),
				new google.maps.LatLng(52.163160000000005, 18.203380000000003),
				new google.maps.LatLng(52.16304, 18.20416),
				new google.maps.LatLng(52.162939, 18.206812),
				new google.maps.LatLng(52.162939, 18.206812),
				new google.maps.LatLng(52.162310000000005, 18.21027),
				new google.maps.LatLng(52.16208, 18.21283),
				new google.maps.LatLng(52.16181, 18.21791),
				new google.maps.LatLng(52.16019000000001, 18.24114),
				new google.maps.LatLng(52.159710000000004, 18.24659),
				new google.maps.LatLng(52.159380000000006, 18.24942),
				new google.maps.LatLng(52.15876, 18.25365),
				new google.maps.LatLng(52.15802000000001, 18.25767),
				new google.maps.LatLng(52.15713, 18.261740000000003),
				new google.maps.LatLng(52.1559, 18.266360000000002),
				new google.maps.LatLng(52.15395, 18.272930000000002),
				new google.maps.LatLng(52.15173000000001, 18.28009),
				new google.maps.LatLng(52.15048, 18.28517),
				new google.maps.LatLng(52.14958000000001, 18.289530000000003),
				new google.maps.LatLng(52.149150000000006, 18.292070000000002),
				new google.maps.LatLng(52.148610000000005, 18.296120000000002),
				new google.maps.LatLng(52.14734000000001, 18.307280000000002),
				new google.maps.LatLng(52.146950000000004, 18.310080000000003),
				new google.maps.LatLng(52.146440000000005, 18.31313),
				new google.maps.LatLng(52.14537000000001, 18.31846),
				new google.maps.LatLng(52.14269, 18.32977),
				new google.maps.LatLng(52.14182, 18.33405),
				new google.maps.LatLng(52.141090000000005, 18.33865),
				new google.maps.LatLng(52.14076000000001, 18.34136),
				new google.maps.LatLng(52.14032, 18.346480000000003),
				new google.maps.LatLng(52.140100000000004, 18.35095),
				new google.maps.LatLng(52.140080000000005, 18.352870000000003),
				new google.maps.LatLng(52.14011000000001, 18.357650000000003),
				new google.maps.LatLng(52.140280000000004, 18.36145),
				new google.maps.LatLng(52.140820000000005, 18.36841),
				new google.maps.LatLng(52.14193, 18.380010000000002),
				new google.maps.LatLng(52.14226000000001, 18.38624),
				new google.maps.LatLng(52.14267, 18.398690000000002),
				new google.maps.LatLng(52.14302000000001, 18.404130000000002),
				new google.maps.LatLng(52.14321, 18.40614),
				new google.maps.LatLng(52.14392, 18.41141),
				new google.maps.LatLng(52.144760000000005, 18.416240000000002),
				new google.maps.LatLng(52.14987000000001, 18.441010000000002),
				new google.maps.LatLng(52.15075, 18.445600000000002),
				new google.maps.LatLng(52.15128000000001, 18.448990000000002),
				new google.maps.LatLng(52.15156, 18.450870000000002),
				new google.maps.LatLng(52.151880000000006, 18.4537),
				new google.maps.LatLng(52.152300000000004, 18.458060000000003),
				new google.maps.LatLng(52.15254, 18.462670000000003),
				new google.maps.LatLng(52.15260000000001, 18.466820000000002),
				new google.maps.LatLng(52.15256, 18.46956),
				new google.maps.LatLng(52.152420000000006, 18.47267),
				new google.maps.LatLng(52.15207, 18.47762),
				new google.maps.LatLng(52.15167, 18.481430000000003),
				new google.maps.LatLng(52.15124, 18.48441),
				new google.maps.LatLng(52.15059, 18.48835),
				new google.maps.LatLng(52.14988, 18.49181),
				new google.maps.LatLng(52.145790000000005, 18.50908),
				new google.maps.LatLng(52.14444, 18.51537),
				new google.maps.LatLng(52.14347000000001, 18.52063),
				new google.maps.LatLng(52.14309, 18.52316),
				new google.maps.LatLng(52.14244000000001, 18.52822),
				new google.maps.LatLng(52.141960000000005, 18.53312),
				new google.maps.LatLng(52.141510000000004, 18.54017),
				new google.maps.LatLng(52.14139, 18.548370000000002),
				new google.maps.LatLng(52.141450000000006, 18.55309),
				new google.maps.LatLng(52.142100000000006, 18.5805),
				new google.maps.LatLng(52.14209, 18.584770000000002),
				new google.maps.LatLng(52.141920000000006, 18.59131),
				new google.maps.LatLng(52.14143000000001, 18.599140000000002),
				new google.maps.LatLng(52.14083, 18.605030000000003),
				new google.maps.LatLng(52.14032, 18.60919),
				new google.maps.LatLng(52.139810000000004, 18.612640000000003),
				new google.maps.LatLng(52.138960000000004, 18.617530000000002),
				new google.maps.LatLng(52.13799, 18.622320000000002),
				new google.maps.LatLng(52.136720000000004, 18.62769),
				new google.maps.LatLng(52.133660000000006, 18.63906),
				new google.maps.LatLng(52.132760000000005, 18.64264),
				new google.maps.LatLng(52.13149000000001, 18.64852),
				new google.maps.LatLng(52.129560000000005, 18.659170000000003),
				new google.maps.LatLng(52.128370000000004, 18.664800000000003),
				new google.maps.LatLng(52.127, 18.669980000000002),
				new google.maps.LatLng(52.12429, 18.67895),
				new google.maps.LatLng(52.123270000000005, 18.682740000000003),
				new google.maps.LatLng(52.12259, 18.685740000000003),
				new google.maps.LatLng(52.12176, 18.69001),
				new google.maps.LatLng(52.12122, 18.693340000000003),
				new google.maps.LatLng(52.120630000000006, 18.69782),
				new google.maps.LatLng(52.118790000000004, 18.71355),
				new google.maps.LatLng(52.11795000000001, 18.71882),
				new google.maps.LatLng(52.11733, 18.721970000000002),
				new google.maps.LatLng(52.116240000000005, 18.72669),
				new google.maps.LatLng(52.115520000000004, 18.72934),
				new google.maps.LatLng(52.11408, 18.73386),
				new google.maps.LatLng(52.112370000000006, 18.738470000000003),
				new google.maps.LatLng(52.11102, 18.74163),
				new google.maps.LatLng(52.10943, 18.744880000000002),
				new google.maps.LatLng(52.104710000000004, 18.753960000000003),
				new google.maps.LatLng(52.102700000000006, 18.75822),
				new google.maps.LatLng(52.10043, 18.7637),
				new google.maps.LatLng(52.09723, 18.772080000000003),
				new google.maps.LatLng(52.096090000000004, 18.77486),
				new google.maps.LatLng(52.094350000000006, 18.778640000000003),
				new google.maps.LatLng(52.092470000000006, 18.782210000000003),
				new google.maps.LatLng(52.09120000000001, 18.7844),
				new google.maps.LatLng(52.0889, 18.78789),
				new google.maps.LatLng(52.08715, 18.790280000000003),
				new google.maps.LatLng(52.0816, 18.797030000000003),
				new google.maps.LatLng(52.07938000000001, 18.79989),
				new google.maps.LatLng(52.07733, 18.80284),
				new google.maps.LatLng(52.07538, 18.80592),
				new google.maps.LatLng(52.073750000000004, 18.8088),
				new google.maps.LatLng(52.07206000000001, 18.81199),
				new google.maps.LatLng(52.068360000000006, 18.81982),
				new google.maps.LatLng(52.06680000000001, 18.82291),
				new google.maps.LatLng(52.06575, 18.824900000000003),
				new google.maps.LatLng(52.06392, 18.82806),
				new google.maps.LatLng(52.062000000000005, 18.83104),
				new google.maps.LatLng(52.05913, 18.8353),
				new google.maps.LatLng(52.051010000000005, 18.847150000000003),
				new google.maps.LatLng(52.04643, 18.853990000000003),
				new google.maps.LatLng(52.043980000000005, 18.857850000000003),
				new google.maps.LatLng(52.04262000000001, 18.86015),
				new google.maps.LatLng(52.04046, 18.86417),
				new google.maps.LatLng(52.03909, 18.86702),
				new google.maps.LatLng(52.037850000000006, 18.86982),
				new google.maps.LatLng(52.036170000000006, 18.874010000000002),
				new google.maps.LatLng(52.03488, 18.877670000000002),
				new google.maps.LatLng(52.03372, 18.881300000000003),
				new google.maps.LatLng(52.03237000000001, 18.88615),
				new google.maps.LatLng(52.03154000000001, 18.88945),
				new google.maps.LatLng(52.0275, 18.909090000000003),
				new google.maps.LatLng(52.026390000000006, 18.91395),
				new google.maps.LatLng(52.02561000000001, 18.91692),
				new google.maps.LatLng(52.02478000000001, 18.91972),
				new google.maps.LatLng(52.017540000000004, 18.94133),
				new google.maps.LatLng(52.016160000000006, 18.945780000000003),
				new google.maps.LatLng(52.01549000000001, 18.948140000000002),
				new google.maps.LatLng(52.014920000000004, 18.95052),
				new google.maps.LatLng(52.012260000000005, 18.96292),
				new google.maps.LatLng(52.011030000000005, 18.967840000000002),
				new google.maps.LatLng(52.009600000000006, 18.972550000000002),
				new google.maps.LatLng(52.00600000000001, 18.98278),
				new google.maps.LatLng(52.004220000000004, 18.98808),
				new google.maps.LatLng(52.002860000000005, 18.992610000000003),
				new google.maps.LatLng(52.00155, 18.99762),
				new google.maps.LatLng(52.000310000000006, 19.003110000000003),
				new google.maps.LatLng(51.99929, 19.008830000000003),
				new google.maps.LatLng(51.998650000000005, 19.013060000000003),
				new google.maps.LatLng(51.996210000000005, 19.03096),
				new google.maps.LatLng(51.9958, 19.03337),
				new google.maps.LatLng(51.994640000000004, 19.0392),
				new google.maps.LatLng(51.993320000000004, 19.04446),
				new google.maps.LatLng(51.99242, 19.04736),
				new google.maps.LatLng(51.99125, 19.050790000000003),
				new google.maps.LatLng(51.989990000000006, 19.05403),
				new google.maps.LatLng(51.988530000000004, 19.057440000000003),
				new google.maps.LatLng(51.9868, 19.061),
				new google.maps.LatLng(51.98453000000001, 19.06513),
				new google.maps.LatLng(51.979780000000005, 19.0732),
				new google.maps.LatLng(51.976470000000006, 19.079220000000003),
				new google.maps.LatLng(51.97379, 19.084880000000002),
				new google.maps.LatLng(51.971650000000004, 19.09017),
				new google.maps.LatLng(51.96952, 19.09653),
				new google.maps.LatLng(51.96645, 19.10726),
				new google.maps.LatLng(51.96452000000001, 19.113380000000003),
				new google.maps.LatLng(51.96303, 19.11731),
				new google.maps.LatLng(51.959790000000005, 19.12516),
				new google.maps.LatLng(51.95843000000001, 19.128610000000002),
				new google.maps.LatLng(51.956880000000005, 19.133080000000003),
				new google.maps.LatLng(51.956, 19.136080000000003),
				new google.maps.LatLng(51.954840000000004, 19.14057),
				new google.maps.LatLng(51.95239, 19.152410000000003),
				new google.maps.LatLng(51.95107, 19.15794),
				new google.maps.LatLng(51.94953, 19.16319),
				new google.maps.LatLng(51.944880000000005, 19.17685),
				new google.maps.LatLng(51.94371, 19.180850000000003),
				new google.maps.LatLng(51.942660000000004, 19.184880000000003),
				new google.maps.LatLng(51.94154, 19.190260000000002),
				new google.maps.LatLng(51.94075, 19.19506),
				new google.maps.LatLng(51.94037, 19.198230000000002),
				new google.maps.LatLng(51.939960000000006, 19.20315),
				new google.maps.LatLng(51.939690000000006, 19.21029),
				new google.maps.LatLng(51.93972, 19.22663),
				new google.maps.LatLng(51.939460000000004, 19.23338),
				new google.maps.LatLng(51.938900000000004, 19.239330000000002),
				new google.maps.LatLng(51.937760000000004, 19.247860000000003),
				new google.maps.LatLng(51.93565, 19.26266),
				new google.maps.LatLng(51.934850000000004, 19.267660000000003),
				new google.maps.LatLng(51.93392000000001, 19.2724),
				new google.maps.LatLng(51.93074000000001, 19.28674),
				new google.maps.LatLng(51.930150000000005, 19.28969),
				new google.maps.LatLng(51.92943, 19.294220000000003),
				new google.maps.LatLng(51.92904000000001, 19.29737),
				new google.maps.LatLng(51.92871, 19.30075),
				new google.maps.LatLng(51.9279, 19.31325),
				new google.maps.LatLng(51.92737, 19.31847),
				new google.maps.LatLng(51.926970000000004, 19.32147),
				new google.maps.LatLng(51.92631, 19.325370000000003),
				new google.maps.LatLng(51.924870000000006, 19.33254),
				new google.maps.LatLng(51.92378, 19.338600000000003),
				new google.maps.LatLng(51.92306000000001, 19.343410000000002),
				new google.maps.LatLng(51.92203000000001, 19.35162),
				new google.maps.LatLng(51.92146, 19.35903),
				new google.maps.LatLng(51.921150000000004, 19.366010000000003),
				new google.maps.LatLng(51.92107000000001, 19.37111),
				new google.maps.LatLng(51.92114, 19.381040000000002),
				new google.maps.LatLng(51.9211, 19.38567),
				new google.maps.LatLng(51.92092, 19.39112),
				new google.maps.LatLng(51.92071000000001, 19.394620000000003),
				new google.maps.LatLng(51.92027, 19.39949),
				new google.maps.LatLng(51.9196, 19.404690000000002),
				new google.maps.LatLng(51.91864, 19.41037),
				new google.maps.LatLng(51.91762000000001, 19.41535),
				new google.maps.LatLng(51.916290000000004, 19.420740000000002),
				new google.maps.LatLng(51.914910000000006, 19.42549),
				new google.maps.LatLng(51.91274000000001, 19.43177),
				new google.maps.LatLng(51.904830000000004, 19.451430000000002),
				new google.maps.LatLng(51.903620000000004, 19.45466),
				new google.maps.LatLng(51.90267000000001, 19.45749),
				new google.maps.LatLng(51.90126000000001, 19.462310000000002),
				new google.maps.LatLng(51.90014000000001, 19.46702),
				new google.maps.LatLng(51.899170000000005, 19.47247),
				new google.maps.LatLng(51.898700000000005, 19.47559),
				new google.maps.LatLng(51.8982, 19.480300000000003),
				new google.maps.LatLng(51.89786, 19.48543),
				new google.maps.LatLng(51.897780000000004, 19.49096),
				new google.maps.LatLng(51.898230000000005, 19.50843),
				new google.maps.LatLng(51.89818, 19.51659),
				new google.maps.LatLng(51.897870000000005, 19.52307),
				new google.maps.LatLng(51.89748, 19.527910000000002),
				new google.maps.LatLng(51.89712, 19.53152),
				new google.maps.LatLng(51.896280000000004, 19.537850000000002),
				new google.maps.LatLng(51.89479000000001, 19.546680000000002),
				new google.maps.LatLng(51.89014, 19.57158),
				new google.maps.LatLng(51.88947, 19.57589),
				new google.maps.LatLng(51.889120000000005, 19.578670000000002),
				new google.maps.LatLng(51.88866, 19.583640000000003) ]
	}).setMap(main_map);

	ibc.nodeMarkers();
	// directionsService = new google.maps.DirectionsService();
	// directionsDisplay = new google.maps.DirectionsRenderer( {
	// draggable : true
	// });
	// directionsDisplay.setMap(main_map);
}
$(document).ready(function() {
	showMap();
	ibc.bindingActions();
	$(".controls").show();
	ibc.createInfoWindows(App.LANG)
});
var PATTERN = {
	parking : "n/a",
	hotel : "n/a",
	wc : "n/a",
	restaurant : "n/a",
	gas : "n/a",
	cafe : "n/a"
};
var pointTypes = [ "parking", "hotel", "wc", "restaurant", "gas", "cafe" ];
var ppos1 = [
		{
			name : "PPO&nbsp;Tarnawa",
			coords : new google.maps.LatLng(52.33518173393839,
					14.901022911071777),
			description : [
					"23&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"23&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "PPO&nbsp;Gołuski",
			coords : new google.maps.LatLng(52.350480230160294,
					16.731812953948975),
			description : [
					"152&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"152&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "PPO&nbsp;Nagradowice",
			coords : new google.maps.LatLng(52.31076134635855,
					17.15140700340271),
			description : [
					"181&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"181&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "PPO&nbsp;Lądek",
			coords : new google.maps.LatLng(52.22185166339437,
					17.950791120529175),
			description : [
					"238&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"238&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		} ];
var spos1 = [
		{
			name : "SPO&nbsp;Buk",
			coords : new google.maps.LatLng(52.376388889, 16.566388889),
			description : [
					"140&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"140&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},     
		{
			name : "SPO&nbsp;Nowy Tomyśl",
			coords : new google.maps.LatLng(52.35859270832136,
					16.09823226928711),
			description : [
					"108&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"108&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "SPO&nbsp;Trzciel",
			coords : new google.maps.LatLng(52.32894501868009,
					15.871896743774414),
			description : [
					"91&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"91&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "SPO&nbsp;Jordanowo",
			coords : new google.maps.LatLng(52.32400930869265,
					15.535354614257812),
			description : [
					"69&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"69&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "SPO&nbsp;Torzym",
			coords : new google.maps.LatLng(52.325242085647034,
					15.085923671722412),
			description : [
					"26&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"26&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "SPO&nbsp;Słupca",
			coords : new google.maps.LatLng(52.253139, 17.824673),
			description : [
					"229&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"229&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		} ];
var ousas = [
		{
			name : "OUA&nbsp;Kotowo",
			coords : new google.maps.LatLng(52.351388889, 16.843888889),
			description : [
					"160&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"160&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "OUA&nbsp;Sługocin ",
			coords : new google.maps.LatLng(52.206666667, 18.019444444),
			description : [
					"243&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"243&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "OUA&nbsp;Bolewice",
			coords : new google.maps.LatLng(52.360555556, 16.098055556),
			description : [
					"108&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"108&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "OUA&nbsp;Biały Mur",
			coords : new google.maps.LatLng(52.298777031, 15.652181206),
			description : [
					"76&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"76&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "OUA&nbsp;Ilanka",
			coords : new google.maps.LatLng(52.323438711, 15.084758664),
			description : [
					"35&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka",
					"35&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		} ];
var roadNodes = [
		{
			name : "Słupca",
			coords : new google.maps.LatLng(52.253139, 17.824673),
			type : "fullNode",
			description : [
					'229&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;466',
					'229&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko' ]
		},
		{
			name : "Poznań Wschód",
			name_en : "Poznan East",
			coords : new google.maps.LatLng(52.320533741936586,
					17.11948871612549),
			type : "fullNode",
			description : [
					"180&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie z drogą ekspresową nr S-5<br>oddanie do użytku - 06.2012",
					"180&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Poznań Zachód",
			name_en : "Poznan West",
			coords : new google.maps.LatLng(52.35050644411929,
					16.76786184310913),
			type : "fullNode",
			description : [
					"154&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie z drogą ekspresową nr S-11<br>oddanie do użytku - 05.2012",
					"154&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Modła",
			coords : new google.maps.LatLng(52.162939, 18.206812),
			type : "fullNode",
			description : [
					"257&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;krajową&nbsp;nr&nbsp;25",
					"257&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Nowy Tomyśl",
			coords : new google.maps.LatLng(52.35859270832136,
					16.09823226928711),
			type : "fullNode",
			description : [
					"108&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;305",
					"108&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Buk",
			coords : new google.maps.LatLng(52.376388889, 16.566388889),
			type : "fullNode",
			description : [
					"140&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;307",
					"140&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Komorniki",
			coords : new google.maps.LatLng(52.350277778, 16.836944444),
			type : "fullNode",
			description : [
					"159&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;krajową&nbsp;nr&nbsp;5",
					"159&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Dębina",
			coords : new google.maps.LatLng(52.353888889, 16.901944444),
			type : "fullNode",
			description : [
					"164&nbsp;km&nbspna&nbspwschód&nbspod&nbspŚwiecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;430",
					"164&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Krzesiny",
			coords : new google.maps.LatLng(52.345833333, 16.999166667),
			type : "fullNode",
			description : [
					"170&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;krajową&nbsp;nr&nbsp;11",
					"170&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Września",
			coords : new google.maps.LatLng(52.315277778, 17.533333333),
			type : "fullNode",
			description : [
					"208&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;krajową&nbsp;nr&nbsp;92",
					"208&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Sługocin",
			coords : new google.maps.LatLng(52.205, 18.021388889),
			type : "fullNode",
			description : [
					"243&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;467",
					"243&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Trzciel",
			coords : new google.maps.LatLng(52.32894501868009,
					15.871896743774414),
			type : "fullNode",
			description : [
					"91&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;powiatową&nbsp;F1339",
					"91&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Jordanowo",
			coords : new google.maps.LatLng(52.32400930869265,
					15.535354614257812),
			type : "fullNode",
			description : [
					'69&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;ekspresową&nbsp;S3',
					'69&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko' ]
		},
		{
			name : "Torzym",
			coords : new google.maps.LatLng(52.325242085647034,
					15.085923671722412),
			type : "fullNode",
			description : [
					"35&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;wojewódzką&nbsp;nr&nbsp;138",
					"35&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Rzepin",
			coords : new google.maps.LatLng(52.333398531374435,
					14.88072395324707),
			type : "fullNode",
			description : [
					"21&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>skrzyżowanie&nbsp;z&nbsp;DK&nbsp;Nr&nbsp;2<br>oraz&nbsp;drogą&nbsp;powiatową&nbsp;F1254",
					"21&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		},
		{
			name : "Świecko",
			coords : new google.maps.LatLng(52.323484712336324,
					14.616494178771973),
			type : "fullNode",
			description : [
					"3&nbsp;km&nbsp;na&nbsp;wschód&nbsp;od&nbsp;Świecka<br>Skrzyżowanie&nbsp;z&nbsp;drogą&nbsp;krajową&nbsp;nr&nbsp;29",
					"3&nbsp;km&nbsp;east&nbsp;of&nbsp;Świecko" ]
		} ];
var infoBoxesData = [
		{
			namePl : "Gnilec",
			cordinates : new google.maps.LatLng(52.325301, 14.651295),
			contentEn : "5&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "5&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Sosna",
			cordinates : new google.maps.LatLng(52.323948, 14.653914),
			contentEn : "5&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "5&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Chociszewo",
			cordinates : new google.maps.LatLng(52.313384, 15.781606),
			contentEn : "85&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "85&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Rogoziniec",
			cordinates : new google.maps.LatLng(52.312423, 15.782388),
			contentEn : "85&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "85&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Koryta",
			cordinates : new google.maps.LatLng(52.32532, 15.161596),
			contentEn : "41&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "41&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Walewice",
			cordinates : new google.maps.LatLng(52.326432, 15.161907),
			contentEn : "41&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "41&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Kozielaski",
			cordinates : new google.maps.LatLng(52.3672222222222,
					16.1894444444444),
			contentEn : "114&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "114&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Wytomyśl",
			cordinates : new google.maps.LatLng(52.3680555555556, 16.19),
			contentEn : "114&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "114&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Zalesie",
			cordinates : new google.maps.LatLng(52.3863888888889,
					16.4761111111111),
			contentEn : "134&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "134&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Sędzinko",
			cordinates : new google.maps.LatLng(52.3877777777778, 16.475),
			contentEn : "134&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "134&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Konarzewo",
			cordinates : new google.maps.LatLng(52.3494444444444,
					16.7141666666667),
			contentEn : "151&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "151&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Krzyżowniki",
			cordinates : new google.maps.LatLng(52.3275, 17.0927777777778),
			contentEn : "177&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "177&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Tulce",
			cordinates : new google.maps.LatLng(52.32945162887316,
					17.092537879943848),
			contentEn : "177&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "177&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Targowa&nbsp;Górka",
			cordinates : new google.maps.LatLng(52.3175, 17.4208333333333),
			contentEn : "200&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "200&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Chwałszyce",
			cordinates : new google.maps.LatLng(52.318592551880876,
					17.41830825805664),
			contentEn : "200&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "200&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Gozdowo",
			cordinates : new google.maps.LatLng(52.2936111111111, 17.6525),
			contentEn : "216&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "216&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Sołeczno",
			cordinates : new google.maps.LatLng(52.2947222222222,
					17.6530555555556),
			contentEn : "216&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "216&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Lądek",
			cordinates : new google.maps.LatLng(52.22634048519531,
					17.931082248687744),
			contentEn : "237&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "237&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Skarboszewo",
			cordinates : new google.maps.LatLng(52.2611111111111,
					17.7883333333333),
			contentEn : "226&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "226&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		},
		{
			namePl : "Osiecza&nbsp;II",
			cordinates : new google.maps.LatLng(52.18, 18.1244444444444),
			contentEn : "251&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "251&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Osiecza&nbsp;III",
			cordinates : new google.maps.LatLng(52.1788888888889,
					18.1216666666667),
			contentEn : "251&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "251&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "top",
			pattern : {
				parking : true,
				hotel : true,
				wc : true,
				restaurant : true,
				gas : true,
				cafe : true
			}
		},
		{
			namePl : "Dopiewiec",
			cordinates : new google.maps.LatLng(52.3502777777778,
					16.7141666666667),
			contentEn : "151&nbsp;km&nbsp;east<br>of&nbsp;Świecko",
			contentPl : "151&nbsp;km&nbsp<br>od&nbsp;Świecka",
			pointerPosition : "bottom",
			pattern : {
				parking : true,
				hotel : false,
				wc : true,
				restaurant : false,
				gas : false,
				cafe : false
			}
		} ];
