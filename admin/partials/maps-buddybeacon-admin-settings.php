<?php
/**
 * Creates the 'how to use' main settings page
 *
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Admin/Partials
 */


// Check that the user is allowed to update options
if (!current_user_can('manage_options')) {

    wp_die('You do not have sufficient permissions to access this page.');

}

?>

<h1><?php _e('How to use Maps for BuddyBeacon', 'maps-buddybeacon') ?></h1>
<hr>

<h3><?php _e('Creating your first map', 'maps-buddybeacon') ?></h3>
<p><?php _e('Take the following steps:', 'maps-buddybeacon') ?></p>
<ul class="bb-list">
	<li><?php 
	$url = "//developers.google.com/maps/documentation/javascript/"; 
	echo sprintf( wp_kses( __( 'Create a <a href="%s" target="_blank">Google Maps API Key</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url ) );
	?></li>
	<li><?php 
	$url_vr = "//www.viewranger.com/developers/register/"; 
	echo sprintf( wp_kses( __( 'Create a <a href="%s" target="_blank">ViewRanger API Key</a>. Under "domain", use the website you intend to add the map to.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url_vr ) );
	?></li>
	<li><?php 
	$url_app = "//support.viewranger.com/index.php?pg=kb.page&id=232";
	echo sprintf( wp_kses( __( 'Your BuddyBeacon username and pin must be created in-app.  <a href="%s" target="_blank">You can find more information here</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url_app ) );
	?></li>
	<li><?php 
	$url_settings = '?page=maps-buddybeacon-settings'; 
	echo sprintf( wp_kses( __( 'Go to <a href="%s">Settings</a> to enter these details.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url_settings ) );
	?></li>
		<li><?php 
	$url_add = '?page=buddybeacon-add-map'; 
	echo sprintf( wp_kses( __( 'Go to <a href="%s">Add Map</a> to set up your first map', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url_add ) );
	?></li>
	<li><?php 
	_e('To display a map on a page or post just add the shortcode: [bb_maps id="{id}"] where {id} is the id of the map you want to display, for example [bb_maps id="3"]. Enter it via a shortcode block or using the Classic Editor directly.', 'maps-buddybeacon');
	?>

</ul>
<hr>
<h3><?php _e('Troubleshooting', 'maps-buddybeacon') ?></h3>
	<p><?php
	echo sprintf( wp_kses( __( 'Map not displaying? Make sure you have entered all fields in <a href="%s">Settings</a> correctly.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url_settings ) );
	?>
 	</p>
 	<p>
	<?php 
	_e('Unable to change map width or height? Depending on your theme you may need to experiment with % or px. To adjust the % you may need to change the map alignment to "center".', 'maps-buddybeacon');
	?>
	</p>
	 <p>
	<?php 
	_e('New beacons not displaying on the map? Make sure to refresh the page as that will trigger the check to the ViewRanger API to see if any new beacons have been sent.', 'maps-buddybeacon');
	?>
	</p>
 	<?php
 	$url_wporg = '//wordpress.org/plugins/maps-buddybeacon/#faq-header';
	echo sprintf( wp_kses( __( 'For other questions, please visit the plugin FAQ section <a href="%s">here</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url_wporg ) );
	?>
	</p>
 	
