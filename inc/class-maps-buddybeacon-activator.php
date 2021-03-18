<?php
/**
 * Fired during plugin activation
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Inc
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Maps_BuddyBeacon
 * @subpackage Inc
 * @author     Karen Attfield <mail@karenattfield.com>
 */

class Maps_BuddyBeacon_Activator {

	/**
	 * Our activate function that sets up a table and all relevant rows in the database.
	 *
	 *
	 * @since    0.1.0
	 */
	public static function activate() {

    if(!function_exists('wp_get_current_user')) {
      
      include(ABSPATH . 'wp-includes/pluggable.php');
    
    }

		if ( ! current_user_can( 'activate_plugins' ) ) {

            return;

        }

    global $wpdb;


    update_option('mb_plugin_version', MAPS_BUDDYBEACON_VERSION);


    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'mapsbb'; 

    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      maptitle tinytext NOT NULL,
      mapwidth int(11) NULL,
      mapwidth_type varchar(100) NOT NULL,
      mapheight int(11) NULL,
      mapheight_type varchar(100) NOT NULL,
      alignment varchar(10) NOT NULL,
      info_box_display int(11) NOT NULL,
      ib_background varchar(10) NOT NULL,
      ib_text varchar(10) NOT NULL,
      ib_distance varchar(100) NOT NULL,
      type varchar(100) NOT NULL,
      daterange_from datetime(6) NULL,
      dateend_choice varchar(20) NOT NULL,
      daterange_to datetime(6) NOT NULL,
      timezone_conversion int(11) NULL,
      number_beacons int(11) NULL,
      track_colour varchar(10) NOT NULL,
      beacon_delete_lon varchar(20) NULL,
      beacon_delete_lat varchar(20) NULL,
      beacon_shape varchar(10) NOT NULL,
      beacon_colour varchar(10) NOT NULL,
      beacon_opacity varchar(10) NOT NULL,
      stroke_weight int(11) NULL,
      stroke_colour varchar(10) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";


    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);

		
	}

}
