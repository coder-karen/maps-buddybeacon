
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

				var mapid = arr[j];
		
				var mapdatas = mapdatasingle;

				// Pulling out the location (latitude and longitude) information
				var jslocations = mapdatas["url"].VIEWRANGER.LOCATIONS;

				//Creating an empty array to store the converted data in
				var coordinates = [];

				//Create another empty array to store all beacon coordinates minus deleted beacon coordinates
				var coordinatesdel = []; 



				// Converting the JSON data into a Javascript array
				for (let i = 0; i < jslocations.length; i++) {

					var longitudes = parseFloat(jslocations[i].LONGITUDE);
					var latitudes = parseFloat(jslocations[i].LATITUDE);
					var beacondateraw =  moment(mapdatas["url"].VIEWRANGER.LOCATIONS[i].DATE).format('MMMM Do YYYY HH:mm:ss');
					var altituderaw = mapdatas["url"].VIEWRANGER.LOCATIONS[i].ALTITUDE;
			
			

					coordinates.push({
						lat: latitudes,
						lng: longitudes,
						date: beacondateraw,
						alt: altituderaw
					});
				}

				coordinatesdel = coordinates;
			
				// Pulling out the array of coordinates that are to be deleted
				var deletecoords = mapdatas["maparray"].deletearray;


				// Make sure we iterate through the deletedcoords array as many times as there are coordinates
				// to make sure we don't miss duplicates
				for (let r = 0; r < coordinates.length; r++) {

					// Iterate through each deletecoords array item.
					for(let m = 0; m < deletecoords.length; m++) {

						// Pulling out the map id as stored in the deletedcoords array
						var deletedcoordsid = deletecoords[m][2];

						// If the id matches
						if ( deletedcoordsid == mapid) {

							// Iterate through each original coordinates item
							for (let n = 0; n < coordinates.length; n++) {

								// If the lat and lng of each individual beacon matches the lat and lng of any of those
								// in the coordinates array
								if ((parseFloat(deletecoords[m][0]) == coordinates[n]['lat'] ) && (parseFloat(deletecoords[m][1]) == coordinates[n]['lng'] ) ) {

									// Delete that item in the array of coordinates that holds only undeleted coordinates
									coordinatesdel.splice(n,1);
	
								}
		
							}

						}

					} // end loop iterating through each deletecoords array item

				} // end final for loop

				
				// Defining a map bound
				var bound = new google.maps.LatLngBounds();

				for (let l = 0; l < coordinates.length; l++) {  

				  	bound.extend( new google.maps.LatLng(coordinates[l]['lat'], coordinates[l]['lng']) ); // <- make sure to edit this

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
			    var numberCoords = coordinates.length - 1; 
			    var totaldistance = 0;
			    var distancescombined = 0;
			    var coord = 0;

			    // For each coordinate (including first and last), add marker and infowindow
		     	for( let k = 0; k < (coordinates.length); k++ )  { 

			        var positions = new google.maps.LatLng(coordinates[k]); 
	

			        marker = new google.maps.Marker({
			            position: positions,
			            map: map,
			            icon: icon,
		 	            title: coordinates[k][0]  
			        });
			   
			        var latit = "<strong>Latitude:</strong> " + coordinates[k].lat + "&#176; <br/>"; 
			        var longit = "<strong>Longitude:</strong> " + coordinates[k].lng + "&#176; <br/>"; 
					var beacondate = "<strong>Date:</strong> " + coordinates[k].date + " <br/>";
			        var altitude = "<strong>Altitude:</strong> " + coordinates[k].alt + "m <br/>";
			        var message = beacondate + altitude + latit + longit;

			        // Add the infowindow
			        addInfoWindow(marker, message);

			    }

			    // For each coordinate, calculate the distance travelled between each in 
			    // order to determine total distance travelled
			    for( let q = 0; q < (coordinates.length - 1); q++ )  { 

					// Calculating distance travelled between each beacon
					var coord2 = q+1;
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

