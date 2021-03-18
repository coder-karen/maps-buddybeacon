
/* Creating the initMap function in the global scope */ 
window.initMap = () => {}

/* If the Google Maps API key is invalid */ 
function gm_authFailure() { 

	// Alert potential developer to Invalid API Key
	console.log( "Invalid Google API Key");

	/* Code to hide mapsbb-canvas */ 
	if (document.getElementsByClassName('mapsbb-canvas')[0]) {
		document.getElementsByClassName('mapsbb-canvas')[0].style.width = "0";
		document.getElementsByClassName('mapsbb-canvas')[0].style.height = "0";
		document.getElementsByClassName('mapsbb-canvas')[0].style.display = "none";
		/* Code to hide map-footer */ 
		document.getElementsByClassName('mapsbb-footer')[0].style.width = "0";
		document.getElementsByClassName('mapsbb-footer')[0].style.height = "0";
		document.getElementsByClassName('mapsbb-footer')[0].style.display = "none";
	}
};


(function( $ ) {
	'use strict';

	setTimeout( () => {

		if (initMap.called) {
			return;
		 }
		else {
			initMap();
		}
	}, 3000);


	window.initMap = () => {

		initMap.called = true;

		//Return if the 'mapsbb-canvas' class name doesn't exist or is hidden
		if (!($(".mapsbb-canvas")[0]) && (document.getElementsByClassName('mapsbb-canvas')[0].style.display == "none")) {
			return;
		}

		let map;

		// Parsing our ViewRanger information into JSON

		const $mapcanvas = $('.mapsbb-canvas');

		//For each map on the page:
		for (let j = 0; j < $mapcanvas.length; j++) {  
			const mapdata = JSON.parse(php_vars);
			const arr = mapdata["maparray"].arr;
			const php_vars_id = eval('php_vars' + arr[j]);
			const mapdatasingle = JSON.parse(php_vars_id);
			const arr2 = mapdatasingle["maparray"].arr;
			const maptype = mapdatasingle["maparray"].maptype;
			

			//If the map canvas id matches the id in array
			if ($(".mapsbb-canvas").is('#'+arr[j])) {

				const mapid = arr[j];
		
				const mapdatas = mapdatasingle;

				// Pulling out the location (latitude and longitude) information
				const jslocations = mapdatas["url"].VIEWRANGER.LOCATIONS;

				//Creating an empty array to store the converted data in
				let coordinates = [];

				//Create another empty array to store all beacon coordinates minus deleted beacon coordinates
				let coordinatesdel = []; 

				// Defining a variable for the timezone adjustment
				const timezoneadjust = mapdatas["maparray"].timezone_conversion;


				// Converting the JSON data into a Javascript array
				for (const loc in jslocations) {

					// Defining the longitude and latitude for each beacon
					const longitudes = parseFloat(jslocations[loc].LONGITUDE);
					const latitudes = parseFloat(jslocations[loc].LATITUDE);
					// Defining the correct date and time for each beacon
					const beacondateraw =  moment(mapdatas["url"].VIEWRANGER.LOCATIONS[loc].DATE).add(timezoneadjust, 'hours').format('MMMM Do YYYY HH:mm:ss');
					// Defining the altitude for each beacon
					const altituderaw = mapdatas["url"].VIEWRANGER.LOCATIONS[loc].ALTITUDE;
			
			

					coordinates.push({
						lat: latitudes,
						lng: longitudes,
						date: beacondateraw,
						alt: altituderaw
					});
				}

				coordinatesdel = coordinates;
			
				// Pulling out the array of coordinates that are to be deleted
				const deletecoords = mapdatas["maparray"].deletearray;

				const numberofcoords = coordinates.length;

				// Iterate through each deletecoords array item.
				for (let m = 0; m < deletecoords.length; m++) {

					// Pulling out the map id as stored in the deletedcoords array
					let deletedcoordsid = deletecoords[m][2];

					// If the mapid in the deleted coordinates array isn't correct, continue the iteration
					if (deletedcoordsid != mapid) {
							continue;
					}

					//Iterate through each coordinates array item
					for (let r = 0; r < numberofcoords; r++) {


						// If the id matches
						if ( deletedcoordsid == mapid) {
					
								// If the lat and lng of each individual beacon matches the lat and lng of any of those
								// in the coordinates array
								if (( coordinates[r])) {

									if ((parseFloat(deletecoords[m][0]) == coordinates[r]['lat'] ) && (parseFloat(deletecoords[m][1]) == coordinates[r]['lng'] ) ) {

									// Delete that item in the array of coordinates that holds only undeleted coordinates
									coordinatesdel.splice(r,1);

									}
	
								}
		
						}

					} // end loop iterating through each deletecoords array item

				} // end final for loop


				
				// Defining a map bound
				const bound = new google.maps.LatLngBounds();

				for (let l = 0; l < coordinates.length; l++) {  

				  	bound.extend( new google.maps.LatLng(coordinates[l]['lat'], coordinates[l]['lng']) ); 

				}

				// Defining the map center point based on the bound
				const centerpoint = bound.getCenter();
	

				// Create the map
		 		let map = new google.maps.Map(document.getElementsByClassName('mapsbb-canvas')[j], {

		          	center: centerpoint,  

		  		});

		 		// Set the map type
		   		const themaptype = mapdatas["maparray"].maptype.toLowerCase();
				map.setMapTypeId(themaptype);

				// Fit the map to the created bounds
		  		map.fitBounds(bound);


			    let marker, icon;
			    let beacon_shape = mapdatas["maparray"].beacon_shape;


			    // If the beacon shape is Circle, then create the Circle icon
			  	if (beacon_shape == "Circle") {
			  		
			        icon = {

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

			  		icon = {

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
			    let numberCoords = coordinates.length - 1; 
			    let totaldistance = 0;
			    let distancescombined = 0;
			    let coord = 0;

			    // For each coordinate (including first and last), add marker and infowindow
		     	for( let co = 0; co < coordinates.length; co++ )  { 

			        let positions = new google.maps.LatLng(coordinates[co]); 
	

			        marker = new google.maps.Marker({
			            position: positions,
			            map: map,
			            icon: icon,
		 	            title: coordinates[co][0]  
			        });
			   
			        let latit = "<strong>Latitude:</strong> " + coordinates[co].lat + "&#176; <br/>"; 
			        let longit = "<strong>Longitude:</strong> " + coordinates[co].lng + "&#176; <br/>"; 
					let beacondate = "<strong>Date:</strong> " + coordinates[co].date + " <br/>";
			        let altitude = "<strong>Altitude:</strong> " + coordinates[co].alt + "m <br/>";
			        let message = beacondate + altitude + latit + longit;

			        // Add the infowindow
			        addInfoWindow(marker, message);

			       	// For each coordinate, calculate the distance travelled between each in 
			    	// order to determine total distance travelled
			    	// However, we can't do this for the final coordinate so leave the loop before then.
			    	if (co >= numberCoords ) {
			    		continue;
			    	}

					let coord2 = co+1;
					let startlat = coordinates[coord].lat;  
				    let startlng = coordinates[coord].lng;  
				    let startLatLng = new google.maps.LatLng(startlat, startlng);
				    let endlat = coordinates[coord2].lat; 
				    let endlng = coordinates[coord2].lng;
				    let endLatLng = new google.maps.LatLng(endlat, endlng);
				    distancescombined =  google.maps.geometry.spherical.computeDistanceBetween(startLatLng, endLatLng);
				    totaldistance = totaldistance + distancescombined;
				    coord = coord+1;

			    }


			    // Calculate the distance based on the distance type
			    const distancetype = mapdatas["maparray"].ib_distance;
		    	if (distancetype == 'Miles') {

				    //Convert total distance to miles and send to info box under map
					let distanceinkm = Math.round((totaldistance * 0.00062137) * 100) / 100;
					let elementname = 'mapsbb-footer-distance'+arr[j]+'';
					let outputdistance = document.getElementById(elementname);
			    	outputdistance.innerHTML = 'Distance: ' + distanceinkm + ' miles';

		    	}

		    	else {
			
					//Convert total distance to km and send to info box under map
					let distanceinkm = Math.round((totaldistance/1000) * 100) / 100;
					let elementname = 'mapsbb-footer-distance'+arr[j]+'';
					let outputdistance = document.getElementById(elementname);
			    	outputdistance.innerHTML = 'Distance: ' + distanceinkm + 'km';

	    		}

	    		// Create the flightpath - the polylines between markers
			    let flightPath = new google.maps.Polyline({

			     	path: coordinates,  
			     	geodesic: true,
			     	strokeColor: mapdatas["maparray"].track_colour,  
			     	strokeOpacity: 1.0,
			     	strokeWeight: 2
			    });

		     	flightPath.setMap(map);

		     	// Function to add info windows for each marker

				function addInfoWindow(marker, message) {

		            const infoWindow = new google.maps.InfoWindow({
		                content: message
		            });

		            google.maps.event.addListener(marker, 'click', () => {
		                infoWindow.open(map, marker);
		            });

        		}

	
			} // end map canvas id match

		} // end for each map on the page


	} // end initMap function


})( jQuery );

