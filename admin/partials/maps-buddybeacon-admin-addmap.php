<?php
/**
 * Creates the 'add map' sub-menu settings page
 *
 * This file is used to markup the admin-facing aspects of the plugin.
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

<h1><?php echo $GLOBALS['title'] ?></h1>
<hr>
<div id="wrap">
	<form method="post" action="options.php">

		<?php
	
			settings_fields( 'maps-buddybeacon-add-settings' );
			do_settings_sections( 'maps-buddybeacon-add-settings' );  
			submit_button();

		?>

	</form>
</div>
