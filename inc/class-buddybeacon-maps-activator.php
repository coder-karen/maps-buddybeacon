<?php
/**
 * Fired during plugin activation
 *
 * @since      0.1.0
 *
 * @package    BuddyBeacon_Maps
 * @subpackage Inc
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    BuddyBeacon_Maps
 * @subpackage Inc
 * @author     Karen Attfield <mail@karenattfield.com>
 */

class BuddyBeacon_Maps_Activator {

	/**
	 * Our activate function that sets up a table and all relevant rows in the database.
	 *
	 *
	 * @since    0.1.0
	 */
	public static function activate() {

		if ( ! current_user_can( 'activate_plugins' ) ) {

            return;

        }

    global $wpdb;
    $table_name = $wpdb->prefix . 'maps'; 

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
    );";


    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
		
	}

}
