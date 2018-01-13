<?php
/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @since      0.1.0
 *
 * @package    BuddyBeacon_Maps
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

$option_name = 'buddybeacon-maps-settings'; 
delete_option($option_name);
 // for site options in Multisite
delete_site_option($option_name);

delete_tables();

