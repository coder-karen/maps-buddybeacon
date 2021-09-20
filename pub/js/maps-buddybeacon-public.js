/* Creating the initMap function in the global scope */ 
function initMap() {}

/* If the Google Maps API key is invalid */ 
let gm_authFailure = () => { 

	// Alert potential developer to Invalid API Key
	console.log( "Invalid Google API Key");

	const mapCanvasClass = document.getElementsByClassName('mapsbb-canvas')[0];
	const mapFooterClass = document.getElementsByClassName('mapsbb-footer')[0];
	/* Hide the map canvas and footer */ 
	if (mapCanvasClass) {
		mapCanvasClass.style.width = "0";
		mapCanvasClass.style.height = "0";
		mapCanvasClass.style.display = "none";
		mapFooterClass.style.width = "0";
		mapFooterClass.style.height = "0";
		mapFooterClass.style.display = "none";
	}
};

{

	let mapRenderer = () => {
		'use strict';

		setTimeout( () => {

			if (initMap.called) {
				return;
			 }
			else {
				initMap();
			}
		}, 3000);

		initMap = () => {

			initMap.called = true;

			//Return if the 'mapsbb-canvas' class name doesn't exist or is hidden
			if (!(document.getElementsByClassName('mapsbb-canvas')[0]) && ("none" === document.getElementsByClassName('mapsbb-canvas')[0].style.display)) {
				return;
			}

			let map;

			const mapcanvasarray = document.getElementsByClassName('mapsbb-canvas');

			// For each mapcanvasarray, create a new map.
			for (let mapNumber = 1; mapNumber <= mapcanvasarray.length; mapNumber++) {

			let mapdata = JSON.parse(php_vars);
			let arr = mapdata["maparray"].arr;
			let mapDivID = mapcanvasarray[mapNumber-1].id;

				// creates a new EachMap object
				var thisMap = new EachMap( map, mapDivID, mapNumber );
				// renders the new EachMap object (creates the map)
				thisMap.createMap( mapDivID );
				
			} // end for loop over each map canvas in the array

		} // end initMap function

	}

	mapRenderer();

	/* Main creation function for each map. */
	function EachMap ( map, mapDivID, mapNumber ) {
		this.mapDivID = mapDivID;

		this.createMap = function() {
			//For each map on the page:
			const phpVarsID		= Function('"use strict";return php_vars' + mapDivID)();
			const mapDivData	= JSON.parse(phpVarsID);
			const mapType 		= mapDivData["maparray"].maptype;
			let coordinates 	= [];


			createBeaconCoordinateData(coordinates, mapDivData);

			// Pulling out the array of coordinates that are to be deleted
			const coordsToDelete = mapDivData["maparray"].deletearray;

			// Defining a map bound
			let bound = new google.maps.LatLngBounds();



			// Iterate through each coordsToDelete array item.
			let currentCoordsToDelete = 0;
			let numberCoordsToDelete = coordsToDelete.length;
			while (currentCoordsToDelete < numberCoordsToDelete) {
				currentCoordsToDelete++;
				// Pulling out the map id as stored in the deletedcoords array
				let deletedcoordsid = coordsToDelete[currentCoordsToDelete-1][2];
				// If the mapDivID in the deleted coordinates array isn't correct, continue the iteration
				if (deletedcoordsid != mapDivID) {
						continue;
				}
				//Iterate through each coordinates array item
				loopThroughCoordsForMap(deletedcoordsid, mapDivID, coordinates, coordsToDelete, currentCoordsToDelete);

			}

			// Defines the map's bounds
			boundExtend(bound, coordinates); 

			// Builds the map	
			buildMap(bound, mapType, mapDivData, coordinates, mapDivID, mapNumber);



		} // end this.createMap function

	}  // end EachMap function


	/* Converting the JSON location data into a Javascript array with that data */
	let createBeaconCoordinateData = (coordinates, mapDivData) => {

		const timezoneAdjust = mapDivData["maparray"].timezone_conversion;
		const locationData   = mapDivData["url"].VIEWRANGER.LOCATIONS;

		for (const beacon in locationData) {
			const longitude  = parseFloat(locationData[beacon].LONGITUDE);
			const latitude   = parseFloat(locationData[beacon].LATITUDE);
			const beaconDate =  moment(mapDivData["url"].VIEWRANGER.LOCATIONS[beacon].DATE).add(timezoneAdjust, 'hours').format('MMMM Do YYYY HH:mm:ss');
			// Defining the altitude for each beacon
			const altitude   = mapDivData["url"].VIEWRANGER.LOCATIONS[beacon].ALTITUDE;
			coordinates.push({
				lat: latitude,
				lng: longitude,
				date: beaconDate,
				alt: altitude
			});
		}
		return coordinates;
	}


	/* Main looping function - looping through coords to remove those deleted */
	let loopThroughCoordsForMap = (deletedcoordsid, mapDivID, coordinates, coordsToDelete, currentCoordsToDelete) => {

		let coordIndex = 0;
		coordinates.forEach(coordinate => {
			removeDeletedCoords(coordinate, deletedcoordsid, mapDivID, coordinates, coordsToDelete, currentCoordsToDelete, coordIndex );
			coordIndex++;
		});

	}


	/* Defines the map bounds, based on the coordinates showing on the map */
	let boundExtend = (bound, coordinates) => {
			coordinates.forEach(coordinate => {
			bound.extend( new google.maps.LatLng(coordinate['lat'], coordinate['lng']) );
		});
	}


	/* Removes the deleted coordinates from the array of coordinates */
	let removeDeletedCoords = (coordinate, deletedcoordsid, mapDivID, coordinates, coordsToDelete, currentCoordsToDelete, coordIndex ) => {
	// If the id matches
		if ( deletedcoordsid == mapDivID) {
			// If the lat and lng of each individual beacon matches the lat and lng of any of those
			// in the coordinates array
			if (coordinate) {
				if ((parseFloat(coordsToDelete[currentCoordsToDelete-1][0]) == coordinate['lat'] ) && (parseFloat(coordsToDelete[currentCoordsToDelete-1][1]) == coordinate['lng'] ) ) {
				// Delete that item in the array of coordinates that holds only undeleted coordinates
					coordinates.splice(coordIndex,1);
				}
			}
		}
	}


	/* Builds up the map - creates the map bounds, creates the icons, info window and flightpath */
	let buildMap = (bound, mapType, mapDivData, coordinates, mapDivID, mapNumber) => {
		const centerpoint = bound.getCenter();

		// Create the map
		let map = new google.maps.Map(document.getElementsByClassName('mapsbb-canvas')[mapNumber-1], {
			center: centerpoint,  
		});


		// Set the map type
		const theMapType = mapType.toLowerCase();
		map.setMapTypeId(theMapType);

		// Fit the map to the created bounds
		map.fitBounds(bound);

		let marker, icon;
		let beacon_shape = mapDivData["maparray"].beacon_shape;

		// If the beacon shape is Circle, then create the Circle icon
		if (beacon_shape == "Circle") {
			icon = createBeaconShape(mapDivData, { path: google.maps.SymbolPath.CIRCLE, scale: 10 });
		}

		// Otherwise create the Square icon
		else {
			icon = createBeaconShape(mapDivData, { path: "M -2,2 -2,-2 2,-2 2,2 z", scale: 5 });
		}

		// Variables to aid in determining distance between beacons
		var totalDistance = 0;
		var distancesCombined = 0;



		loopThroughCoordsForInfoWindow(coordinates, map, icon, totalDistance, mapDivData, mapDivID);
		createFlightPath(coordinates, mapDivData, map);

	}


	/* Beacon shape creation factory function */
	const createBeaconShape = (mapDivData, { path, scale }) => ({
		 path,
		 fillColor: mapDivData["maparray"].beacon_colour,  
		 fillOpacity: parseFloat(mapDivData["maparray"].beacon_opacity),  
		 anchor: new google.maps.Point(0,0),
		 strokeWeight: parseInt(mapDivData["maparray"].stroke_weight),
		 strokeColor: mapDivData["maparray"].stroke_colour, 
		 scale
	});


	/* Main looping function that loops through each of the coordinates for the Info Window */
	let  loopThroughCoordsForInfoWindow = (coordinates, map, icon, totalDistance, mapDivData, mapDivID) => {
		let nextCoordinateIndex = 1;
		let currentCoord = 0;
		let numberCoords = coordinates.length;
		while (currentCoord < numberCoords) {
			buildInfoWindow(coordinates, currentCoord, map, icon);
			currentCoord++;
			if (currentCoord >= numberCoords) {
				continue;
			}
			// Calculate total distance travelled
			let startlat = coordinates[currentCoord-1].lat;  
			let startlng = coordinates[currentCoord-1].lng;  
			let startLatLng = new google.maps.LatLng(startlat, startlng);
			let endlat = coordinates[currentCoord].lat; 
			let endlng = coordinates[currentCoord].lng;
			let endLatLng = new google.maps.LatLng(endlat, endlng);
			distancesCombined =  google.maps.geometry.spherical.computeDistanceBetween(startLatLng, endLatLng);
			totalDistance = totalDistance + distancesCombined;
		}
		convertDistanceMeasurement(mapDivData, totalDistance, mapDivID);
	}


	/* Gathers the info window information for each individual coordinate */
	let buildInfoWindow = (coordinates, currentCoord, map, icon) => {
		let positions = new google.maps.LatLng(coordinates[currentCoord]);
		marker = new google.maps.Marker({
		position: positions,
		map: map,
		icon: icon,
		    title: coordinates[currentCoord][0]  
		});
		let latitudeString = "<strong>Latitude:</strong> " + coordinates[currentCoord].lat + "&#176; <br/>"; 
		let longitudeString = "<strong>Longitude:</strong> " + coordinates[currentCoord].lng + "&#176; <br/>"; 
		let beaconDateString = "<strong>Date:</strong> " + coordinates[currentCoord].date + " <br/>";
		let altitudeString = "<strong>Altitude:</strong> " + coordinates[currentCoord].alt + "m <br/>";
		let message = beaconDateString + altitudeString + latitudeString + longitudeString;

		// Add the infowindow
		addInfoWindow(marker, message, map);
	}


	/* Adds the info window for each individual coordinate */
	let addInfoWindow = (marker, message, map) => {

		const infoWindow = new google.maps.InfoWindow({
			content: message
	    });

		google.maps.event.addListener(marker, 'click', () => {
			infoWindow.open(map, marker);
	    });

	}


	/* Converts distance to either miles or km based on what is chosen in the UI */
	let convertDistanceMeasurement = (mapDivData, totalDistance, mapDivID) => {
		// Calculate the distance based on the distance type
		const distanceType = mapDivData["maparray"].ib_distance;
		if (distanceType == 'Miles') {
			//Convert total distance to miles and send to info box under map
			let distanceinkm = Math.round((totalDistance * 0.00062137) * 100) / 100;
			let elementname = 'mapsbb-footer-distance'+mapDivID+'';
			let outputdistance = document.getElementById(elementname);
			outputdistance.innerHTML = 'Distance: ' + distanceinkm + ' miles';
		}
		else {
			//Convert total distance to km and send to info box under map
			let distanceinkm = Math.round((totalDistance/1000) * 100) / 100;
			let elementname = 'mapsbb-footer-distance'+mapDivID +'';
			let outputdistance = document.getElementById(elementname);
			outputdistance.innerHTML = 'Distance: ' + distanceinkm + 'km';
		}
	}


	/* Creates the Google map flight path with the coordinates */
	let createFlightPath = (coordinates, mapDivData, map) => {
		// Create the flightpath - the polylines between markers
		let flightPath = new google.maps.Polyline({
			path: coordinates,  
			geodesic: true,
			strokeColor: mapDivData["maparray"].track_colour,  
			strokeOpacity: 1.0,
			strokeWeight: 2
		});

		flightPath.setMap(map);
	}

}