<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Public
 * @author     Karen Attfield <mail@karenattfield.com>
 */
	

class Maps_BuddyBeacon_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Function to create the map showing the buddybeacons over the specified time, after calling the shortcode
	 *
	 * @since  0.1.0
	 */
	public function maps_buddybeacon_shortcode($atts = [], $content = null, $tag = '') {

		    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

		    ob_start();

			 if (isset ($atts)) {
			 
	                $atts = shortcode_atts(
	                    array(
	                        'id' => '',
	                    ), $atts, $tag
	                );

	            
	            }

	            $mapid = $atts['id'];
	
	          
				$googleapi = isset(get_option('maps-buddybeacon-settings')['maps-buddybeacon_googleapi']) ? get_option('maps-buddybeacon-settings')['maps-buddybeacon_googleapi'] : '';
	            //Map variables
	            $item = $this->get_map_variables($mapid);
	            if( is_array($item) ) {

		            $id = $item['id'];
					$maptitle = $item['maptitle'];
					$mapwidth = $item['mapwidth'];
					$mapwidth_type = $item['mapwidth_type'];
					$mapheight = $item['mapheight'];
					$mapheight_type = $item['mapheight_type'];
					$mapalignment = $item['alignment'];
					$info_box_display = $item['info_box_display'];
					$ib_background = $item['ib_background'];
					$ib_text = $item['ib_text'];
					$datefrom = $item['daterange_from'];
					$dateto = $item['daterange_to'];

				


					//Check if mapwidth is set and if not default to 100.
					if ($mapwidth == 0) {
						$mapwidth = 100;
						$mapwidth_type = '%';
					}

					//Check if mapheight is set and if not default to auto.
					if ($mapheight == 0) {
						$mapheight = 'auto';
					}

					$footeralignmentcode = '';


					//Check map alignment and create CSS accordingly
					if ($mapalignment == 'Left') {
						$alignmentcode = $footeralignmentcode = 'clear:both;';
					}
					if ($mapalignment == 'Right') {
						$alignmentcode = $footeralignmentcode = 'clear:both;';
					}
					if ($mapalignment == 'Center') {
						$alignmentcode = 'margin: 10px auto 0;';
						$footeralignmentcode = 'margin: 0 auto 10px !important;';
					}
					if ($mapalignment == 'None') {
						$alignmentcode = $footeralignmentcode = '';
					}


					//Convert date from and current date to usable strings
					$fromtime = strtotime($datefrom);
					$datefromstring = date("D, d M Y", $fromtime);
					$dateend_choice = $item['dateend_choice'];

		  			if ($dateend_choice == 'currentdate') {

		  				$dateendstring = "current";

					}
		  			else {

		  				//The date that the mapped route ended
			  			$totime = strtotime($dateto);
						$dateendstring = date("D, d M Y", $totime);

				  	}

				  	//If 'Hide info box' was checked
				  	if ($info_box_display == '1') {

				  		$mapfooter = "style='display:none;'";
				  		$alignmentcode = $alignmentcode . ' margin-bottom:10px;';

				  	} 

				  	else {

				  	//Style for map footer
				  	$mapfooter = "style='color: ".$ib_text."; background-color: ".$ib_background."; width: " . $mapwidth.$mapwidth_type . "; " . $footeralignmentcode . "'";

				  	}

					 

				  	// If the database map id matches the shortcode id and the googleapi field is not empty
		            if (($mapid == $id) && ($googleapi != '') ) {

		            	// Finding and decoding the JSON from the ViewRanger api url
		            	$params = $this->viewranger_get_profile($mapid);
		               	$jsonparams = json_decode($params);

		               	// If the ViewRanger api url produces valid JSON
		               	if (!isset($jsonparams->url->VIEWRANGER->ERROR)) {

			            	?>
							<!-- This script addition ensures that initMap is defined as a function in time -->
							<script type="text/javascript">
								window.initMap = function() {}
				
							</script>
							<?php
		              		// Enqueue our buddybeacon-js script and google maps
		              		wp_enqueue_script('buddybeacon-js');
		              		wp_enqueue_script( 'google-maps');

		              		// Send all the JSON map and ViewRanger variables to Javascript
		              		wp_localize_script( 'buddybeacon-js', 'php_vars', $params);
		              		wp_localize_script( 'buddybeacon-js', 'php_vars'.$mapid , $params);
			              		    	
		              		//Write out the content of the distanceinkm variable to the page
		              		$distanceinkm = 'echo <script>document.writeln(distanceinkm);</script>';

		              		$theid = $atts['id'];
			         
			            	$content = "<div class='mapsbb-canvas' id='".$atts['id'] ."' style='width: " . $mapwidth.$mapwidth_type . "; height: " .$mapheight.$mapheight_type . "; " . $alignmentcode . "'></div><div class='mapsbb-footer' " . $mapfooter . "><div class='mapsbb-summary' ><p class='mapsbb-footer-title'>" . $maptitle .  "</p><p class='mapsbb-footer-text'>" . $datefromstring . " - " . $dateendstring . "</p><p class='bb-map-footer-distance' id='mapsbb-footer-distance".$theid."' ></p></div></div>";					

			            	$content .= ob_get_contents();
			            	ob_end_clean();
			            	return $content;

						}	

	            	}
	            

	            	else {

						return;

					}
				} // end if $item is array

			}



	/**
	 * Function to access the database and retrieve map variables
	 *
	 * @since  0.1.0
	 */
	function get_map_variables($mapid) {

		global $wpdb;

		$table_name = $wpdb->prefix.'mapsbb';
	
  		$item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $mapid), ARRAY_A);

		return $item;

	}


	/**
	 * Function to access the database and retrieve map variables
	 *
	 * @since  0.1.0
	 */
	function viewranger_get_profile($mapid) { 


		//Variables from the map settings page needed to make a call to the ViewRanger API.
		$vrkey = get_option('maps-buddybeacon-settings')['maps-buddybeacon_viewrangerapi'];
		$username = get_option('maps-buddybeacon-settings')['maps-buddybeacon_bbuser'];
		$pin = get_option('maps-buddybeacon-settings')['maps-buddybeacon_bbpin'];
		$service = 'getBBPositions';

		//Accessing the database to pull the row that matches the id in the shortcode
		$item = $this->get_map_variables($mapid);

  		//Here we are creating variables for all the relevant map input fields needed to create a call the ViewRanger Api (number of beacons, date from and date until)
  		$limit = $item['number_beacons'];

  		//The date that the map should begin
  		$date_from_full = str_replace(" ", "%20" ,$item['daterange_from']);
  		$date_from = strstr($date_from_full, '.', true); // As of PHP 5.3.0

  		//Check to see if 'current date' has been selected as an end date. If so, set to current date/time.
  		$dateend_choice = $item['dateend_choice'];
  		if ($dateend_choice == 'currentdate') {

  			$date_until = date('Y-m-d%20H:i:s');

  		}

  		else {

	  		//The date that the map should end
	  		$date_until_full = str_replace(" ", "%20" ,$item['daterange_to']);
	  		$date_until = strstr($date_until_full, '.', true);
 
  		}

		$format = 'json';
		$base_url = 'http://api.viewranger.com/public/v1/';

		// Creating the complete url
  		if (($item['number_beacons']) == 0 ) {

  			$json_feed_url = ''.$base_url.'?key='.$vrkey.'&service='.$service.'&username='.$username.'&pin='.$pin.'&date_from='.$date_from.'&date_until='.$date_until.'&format='.$format.'';
  			
  		}

  		else {

  			$json_feed_url = ''.$base_url.'?key='.$vrkey.'&service='.$service.'&username='.$username.'&pin='.$pin.'&date_from='.$date_from.'&date_until='.$date_until.'&limit='.$limit.'&format='.$format.'';

  		}

		//Here we are gathering the remaining map/beacon information to be used in order to style the map output
		$maptype = $item['type'];
		$ib_distance = $item['ib_distance'];
		$timezone_conversion = $item['timezone_conversion']; 
		
		$track_colour = $item['track_colour'];
		$beacon_shape = $item['beacon_shape'];
		$beacon_colour = $item['beacon_colour'];
		$beacon_opacity = $item['beacon_opacity'];
		$stroke_weight = $item['stroke_weight'];
		$stroke_colour = $item['stroke_colour'];
		$beacon_delete_lon = $item['beacon_delete_lon'];
		$beacon_delete_lat = $item['beacon_delete_lat'];

		$deletearray = get_option("bbmaps-delete_array");

		// Here we check if our array of deleted coordinates is empty. If so we add the option.
		if (empty($deletearray)) {
			
			add_option("bbmaps-delete_array", array(1 => array($beacon_delete_lat, $beacon_delete_lon, $mapid)));

		}

		// If not empty, we add to the array, but only if beacon coordinates aren't already inputted
		else {

			$break = false;
			foreach ($deletearray as $value => $key ) {
				
				if ((($deletearray[$value][0]) == $beacon_delete_lat) && (($deletearray[$value][1]) == $beacon_delete_lon) && (($deletearray != null) && (isset($deletearray[$value][2])))) {
					
					if ($deletearray[$value][2] == $mapid) {
						$break = true;
					}

				}

			} // end foreach

			if ($break == false) {

				$int = sizeof($deletearray) + 1;
				$deletearray = array_merge($deletearray, array($int => array($beacon_delete_lat, $beacon_delete_lon, $mapid)));
				
				update_option("bbmaps-delete_array", $deletearray);
			
			}
			
			
			// Then we want to clear the beacon_delete_lon and beacon_delete_lat options from the DB.
			global $wpdb;
			$table_name = $wpdb->prefix.'mapsbb';
			$wpdb->update( $table_name, array( 'beacon_delete_lon' => "", 'beacon_delete_lat' => ""), array( "ID" => $item['id'], "ID" => $item['id']));
   
		}

		$deletearray = get_option("bbmaps-delete_array");


		// Create an array of all shortcode ids (if more than one on a page) to add to map data array
    	global $arr;  
	    if (isset ($arr)) {
	    	$arr[] = $mapid;

	    }
	    else {
	    	$arr[] = $mapid;
	    }
	 

		//We then put these variables into an array
		$maparray = compact("maptype", "ib_distance", "timezone_conversion", "track_colour", "deletearray", "beacon_shape", "beacon_colour", "beacon_opacity", "stroke_weight", "stroke_colour", "arr");

		$args = array( 'timeout' => 120 );

		$json_feed = wp_remote_get( $json_feed_url, $args );

		if (!(is_wp_error($json_feed))) {

			$viewranger_profile = json_decode( $json_feed['body']);
		  
			$complete_profile = array("url" => $viewranger_profile, "maparray" => $maparray);

			return json_encode($complete_profile);

		}
		 
		else {

			_e('The ViewRanger API is currently unavailable so no map can be shown. Possible reasons include no / poor internet connection, or the ViewRanger API itself is down.');

		}

	}



	/**
	 * Runs the BuddyBeacon_Shortcode function
	 *
	 * @since  0.1.0
	 */
    public function register_shortcodes() {

		add_shortcode( 'bb_maps', array( $this, 'maps_buddybeacon_shortcode') );

	}

 

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/maps-buddybeacon-public.css', array(), $this->version, 'all' );

	}


	/**
	 * Add async and defer attributes to Google Maps script 
	 *
	 * @since    0.1.0
	 */
	public function google_maps_script_attributes( $tag, $handle) {

    	if ( 'google-maps' !== $handle ) {

        	return $tag;

  		}
   	 
   	 	return str_replace( ' src', ' async="async"  defer  src', $tag );

	}



	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		$googleapi = isset(get_option('maps-buddybeacon-settings')['maps-buddybeacon_googleapi']) ? get_option('maps-buddybeacon-settings')['maps-buddybeacon_googleapi'] : '';

		if (($googleapi)  != '') {

			if( ! wp_script_is( 'jquery', 'enqueued' ) ) {
    			wp_enqueue_script('jquery');
			}

			wp_enqueue_script('jquery-script-time-moment', plugin_dir_url( __FILE__ ) .'../admin/js/mbb-moment.min.js');


			wp_register_script( 'buddybeacon-js', plugin_dir_url( __FILE__ ) . 'js/maps-buddybeacon-public.js', array(), time(), true );
						
			$url = 'https://maps.googleapis.com/maps/api/js?key='.$googleapi.'&libraries=geometry&callback=initMap&format=json';
		
			wp_register_script( 'google-maps', $url, array('buddybeacon-js'), time(), true );

		}


	}


} // end Class
