<?php
/**
 * Creates the 'map settings' sub-menu settings page
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

<h1><?php _e('Maps for BuddyBeacon ', 'maps-buddybeacon'); echo $GLOBALS['title'] ?></h1>
<hr>
<div id="wrap">
	<form method="post" action="options.php">
		
		<?php

			settings_errors(); 
			settings_fields( 'maps-buddybeacon-settings' );
			do_settings_sections( 'maps-buddybeacon-settings' );
			submit_button();

    	?>

	</form>

</div>
