<?php

/**
 * Fired during plugin deactivation
 *
 * @since      0.1.0
 *
 * @package    BuddyBeacon_Maps
 * @subpackage Inc
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    BuddyBeacon_Maps
 * @subpackage Inc
 * @author  Karen Attfield <mail@karenattfield.com>
 */
class BuddyBeacon_Maps_Deactivator {

	/**
	 * Our deactivation function that cleans up anything temporary.
	 *
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {

		if ( ! current_user_can( 'activate_plugins' ) ) {

            return;

        }

	}

}
