
/* Creating the initMap function in the global scope */ 
window.initMap = function() {}

/* If the Google Maps API key is invalid */ 
function gm_authFailure() { 

	// Alert potential developer to Invalid API Key
	console.log( "Invalid Google API Key");

	/* Code to hide map-canvas */ 
	if (document.getElementsByClassName('map-canvas')[0]) {
		document.getElementsByClassName('map-canvas')[0].style.width = "0";
		document.getElementsByClassName('map-canvas')[0].style.height = "0";
		document.getElementsByClassName('map-canvas')[0].style.display = "none";
		/* Code to hide map-footer */ 
		document.getElementsByClassName('map-footer')[0].style.width = "0";
		document.getElementsByClassName('map-footer')[0].style.height = "0";
		document.getElementsByClassName('map-footer')[0].style.display = "none";
	}
};


(function( $ ) {
	'use strict';

	setTimeout(function() {

		if (initMap.called) {
			return;
		 }
		else {
			initMap();
		}
	}, 3000);


	window.initMap = function() {
		initMap.called = true;

		//Return if the 'map-canvas' class name doesn't exist or is hidden
		if (!($(".map-canvas")[0]) && (document.getElementsByClassName('map-canvas')[0].style.display == "none")) {
			return;
		}

		var map;

		// Parsing our ViewRanger information into JSON

		const $mapcanvas = $('.map-canvas');

		//For each map on the page:
		for (let j = 0; j < $mapcanvas.length; j++) {  
			var mapdata = JSON.parse(php_vars);
			var arr = mapdata["maparray"].arr;
			var php_vars_id = eval('php_vars' + arr[j]);
			var mapdatasingle = JSON.parse(php_vars_id);
			var arr2 = mapdatasingle["maparray"].arr;
			var maptype = mapdatasingle["maparray"].maptype;

			//If the map canvas id matches the id in array
			if ($(".map-canvas").is('#'+arr[j])) {
		
				var mapdatas = mapdatasingle;

				// Pulling out the location (latitude and longitude) information
				var jslocations = mapdatas["url"].VIEWRANGER.LOCATIONS;

				//Creating an empty array to store the converted data in
				var coordinates = [];

				// Converting the JSON data into a Javascript array
				for (let i = 0; i < jslocations.length; i++) {

					var longitudes = parseFloat(jslocations[i].LONGITUDE);
					var latitudes = parseFloat(jslocations[i].LATITUDE);
					coordinates.push({
						lat: latitudes,
						lng: longitudes
					});
				}

				// Defining a map bound
				var bound = new google.maps.LatLngBounds();

				for (let l = 0; l < jslocations.length; l++) {  

				  	bound.extend( new google.maps.LatLng(jslocations[l].LATITUDE, jslocations[l].LONGITUDE) ); // <- make sure to edit this

				}

				// Defining the map center point based on the bound
				var centerpoint = bound.getCenter();
	

				// Create the map
		 		var map = new google.maps.Map(document.getElementsByClassName('map-canvas')[j], {

		          	center: centerpoint,  

		  		});

		 		// Set the map type
		   		var themaptype = mapdatas["maparray"].maptype.toLowerCase();
				map.setMapTypeId(themaptype);

				// Fit the map to the created bounds
		  		map.fitBounds(bound);


			    var marker, i;
			    var beacon_shape = mapdatas["maparray"].beacon_shape;


			    // If the beacon shape is Circle, then create the Circle icon
			  	if (beacon_shape == "Circle") {
			  		
			        var icon = {

				        path: google.maps.SymbolPath.CIRCLE, 
				        fillColor: mapdatasingle["maparray"].beacon_colour,  
				        fillOpacity: parseFloat(mapdatas["maparray"].beacon_opacity),  
				        anchor: new google.maps.Point(0,0),
				        strokeWeight: parseInt(mapdatas["maparray"].stroke_weight),
				        strokeColor: mapdatas["maparray"].stroke_colour, 
				        scale: 10
			    	}
			  	}
			  	
			  	// Otherwise create the Square icon
			  	else {

			  		var icon = {

				        path: "M -2,2 -2,-2 2,-2 2,2 z",
				        fillColor: mapdatas["maparray"].beacon_colour,
				        fillOpacity: parseFloat(mapdatas["maparray"].beacon_opacity), 
				        anchor: new google.maps.Point(0,0),
				        strokeWeight: parseInt(mapdatas["maparray"].stroke_weight),
				        strokeColor: mapdatas["maparray"].stroke_colour, 
				        scale: 5
			    	}
			  	}


			    // Variables to aid in determining distance between beacons (in for loop below)
			    var numberCoords = jslocations.length - 1; 
			    var totaldistance = 0;
			    var distancescombined = 0;

			    // For each coordinate, add marker and infowindow, then distance travelled between each in 
			    // order to determine total distance travelled
			    var coord = 0;

		     	for( let k = 0; k < (coordinates.length - 1); k++ )  { 

			        var positions = new google.maps.LatLng(coordinates[k]); 

			        marker = new google.maps.Marker({
			            position: positions,
			            map: map,
			            icon: icon,
		 	            title: coordinates[k][0]  
			        });

			        var latit = "<strong>Latitude:</strong> " + coordinates[k].lat + "&#176; <br/>"; 
			        var longit = "<strong>Longitude:</strong> " + coordinates[k].lng + "&#176; <br/>"; 
			      	var dateraw = moment(mapdatas["url"].VIEWRANGER.LOCATIONS[k].DATE).format('MMMM Do YYYY HH:mm:ss');		
			       	var beacondate = "<strong>Date:</strong> " + dateraw + " <br/>";
			        var altitude = "<strong>Altitude:</strong> " + mapdatas["url"].VIEWRANGER.LOCATIONS[k].ALTITUDE + "m <br/>";
			        var message = beacondate + altitude + latit + longit;

			        // Add the infowindow
			        addInfoWindow(marker, message);
			        

					// Calculating distance travelled between each beacon
					var coord2 = k+1;
					var startlat = coordinates[coord].lat;  
				    var startlng = coordinates[coord].lng;  
				    var startLatLng = new google.maps.LatLng(startlat, startlng);
				    var endlat = coordinates[coord2].lat; 
				    var endlng = coordinates[coord2].lng;
				    var endLatLng = new google.maps.LatLng(endlat, endlng);
				    var distancescombined =  google.maps.geometry.spherical.computeDistanceBetween(startLatLng, endLatLng);
				    var totaldistance = totaldistance + distancescombined;
				    var coord = coord+1;


			    } //end for loop


			    // Calculate the distance based on the distance type
			    var distancetype = mapdatas["maparray"].ib_distance;
		    	if (distancetype == 'Miles') {

				    //Convert total distance to miles and send to info box under map
					var distanceinkm = Math.round((totaldistance * 0.00062137) * 100) / 100;
					var elementname = 'map-footer-distance'+arr[j]+'';
					var outputdistance = document.getElementById(elementname);
			    	outputdistance.innerHTML = 'Distance: ' + distanceinkm + ' miles';

		    	}

		    	else {
			
					//Convert total distance to km and send to info box under map
					var distanceinkm = Math.round((totaldistance/1000) * 100) / 100;
					var elementname = 'map-footer-distance'+arr[j]+'';
					var outputdistance = document.getElementById(elementname);
			    	outputdistance.innerHTML = 'Distance: ' + distanceinkm + 'km';

	    		}
	   	 

	    		// Create the flightpath - the polylines between markers
			    var flightPath = new google.maps.Polyline({

			     	path: coordinates,  
			     	geodesic: true,
			     	strokeColor: mapdatas["maparray"].track_colour,  
			     	strokeOpacity: 1.0,
			     	strokeWeight: 2
			    });

		     	flightPath.setMap(map);

		     	// Function to add info windows for each marker
				function addInfoWindow(marker, message) {

		            var infoWindow = new google.maps.InfoWindow({
		                content: message
		            });

		            google.maps.event.addListener(marker, 'click', function () {
		                infoWindow.open(map, marker);
		            });

        		}

	
			} // end map canvas id match

		} // end for each map on the page


	} // end initMap function


})( jQuery );

