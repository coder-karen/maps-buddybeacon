<?php
/**
 *
 * @since             0.1.0
 * @package           BuddyBeacon_Maps
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyBeacon Maps
 * Plugin URI:        https://karenattfield.com/buddybeacon-maps/
 * Description:       Serving map tracks in real time via ViewRanger BuddyBeacon 
 * Version:           1.0.0
 * Author:            Karen Attfield
 * Author URI:        https://karenattfield.com
 * License: 		  GNU General Public License v2 or later
 * License URI: 	  https://www.gnu.org/licenses/gpl-2.0.html 
 * Text Domain:       buddybeacon-maps
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BUDDYBEACON_MAPS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in inc/class-buddybeacon-maps-activator.php
 */
function activate_buddybeacon_maps() {

	require_once plugin_dir_path( __FILE__ ) . 'inc/class-buddybeacon-maps-activator.php';
	BuddyBeacon_Maps_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in inc/class-buddybeacon-maps-deactivator.php
 */
function deactivate_buddybeacon_maps() {

	require_once plugin_dir_path( __FILE__ ) . 'inc/class-buddybeacon-maps-deactivator.php';
	BuddyBeacon_Maps_Deactivator::deactivate();

}

register_activation_hook( __FILE__, 'activate_buddybeacon_maps' );
register_deactivation_hook( __FILE__, 'deactivate_buddybeacon_maps' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-buddybeacon-maps.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_buddybeacon_maps() {

	$plugin = new BuddyBeacon_Maps();
	$plugin->run();

}

run_buddybeacon_maps();
