<?php
/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


// Drop the maps table
function maps_buddybeacon_delete_tables() {
	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mapsbb");

}

$settings_option = 'maps-buddybeacon-settings'; 
$delete_option = 'bbmaps-delete_array';
$version_option = 'mb_plugin_version';
delete_option($settings_option);
delete_option($delete_option);
delete_option($version_option);
 // for site options in Multisite
delete_site_option($settings_option);
delete_site_option($delete_option);
delete_site_option($version_option);

maps_buddybeacon_delete_tables();

