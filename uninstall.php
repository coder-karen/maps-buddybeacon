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
function delete_tables() {
	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}maps");

}

$settings_option = 'maps-buddybeacon-settings'; 
$delete_option = 'delete_array';
delete_option($settings_option);
 // for site options in Multisite
delete_site_option($settings_option);
delete_option($delete_option);
 // for site options in Multisite
delete_site_option($delete_option);

delete_tables();

