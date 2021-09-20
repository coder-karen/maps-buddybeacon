<?php
/**
 *
 * @since             0.1.0
 * @package           Maps_BuddyBeacon
 *
 * @wordpress-plugin
 * Plugin Name:       Maps for BuddyBeacon
 * Plugin URI:        https://karenattfield.com/maps-buddybeacon/
 * Description:       Serving map tracks in real time via ViewRanger BuddyBeacon 
 * Version:           1.1.2
 * Author:            Karen Attfield
 * Author URI:        https://karenattfield.com
 * License: 		  GNU General Public License v2 or later
 * License URI: 	  https://www.gnu.org/licenses/gpl-2.0.html 
 * Text Domain:       maps-buddybeacon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MAPS_BUDDYBEACON_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in inc/class-maps-buddybeacon-activator.php
 */
function activate_maps_buddybeacon() {

	require_once plugin_dir_path( __FILE__ ) . 'inc/class-maps-buddybeacon-activator.php';
	Maps_BuddyBeacon_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in inc/class-maps-buddybeacon-deactivator.php
 */
function deactivate_maps_buddybeacon() {

	require_once plugin_dir_path( __FILE__ ) . 'inc/class-maps-buddybeacon-deactivator.php';
	Maps_BuddyBeacon_Deactivator::deactivate();

}

register_activation_hook( __FILE__, 'activate_maps_buddybeacon' );
register_deactivation_hook( __FILE__, 'deactivate_maps_buddybeacon' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-maps-buddybeacon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_maps_buddybeacon() {

	$plugin = new Maps_BuddyBeacon();
	$plugin->run();

}

run_maps_buddybeacon();
