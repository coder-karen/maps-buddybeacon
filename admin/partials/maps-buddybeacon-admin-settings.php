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
	$url = "//www.viewranger.com/developers/register/"; 
	echo sprintf( wp_kses( __( 'Create a <a href="%s" target="_blank">ViewRanger API Key</a>. Under "domain", use the website you intend to add the map to.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url ) );
	?></li>
	<li><?php 
	$url_ios = "//support.viewranger.com/index.php?pg=kb.chapter&id=37"; 
	$url_android = "//support.viewranger.com/index.php?pg=kb.chapter&id=14";
	echo sprintf( wp_kses( __( 'Your BuddyBeacon username and pin must be created in-app.  <a href="%s" target="_blank">You can find information for iOS users here</a>, and for <a href="%s" target="_blank"> android users here</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url_ios ), esc_url($url_android) );
	?></li>
	<li><?php 
	$url = '?page=maps-buddybeacon-settings'; 
	echo sprintf( wp_kses( __( 'Go to <a href="%s">Settings</a> to enter these details.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url ) );
	?></li>
		<li><?php 
	$url = '?page=buddybeacon-add-map'; 
	echo sprintf( wp_kses( __( 'Go to <a href="%s">Add Map</a> to set up your first map', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array()) ) ), esc_url( $url ) );
	?></li>

</ul>
