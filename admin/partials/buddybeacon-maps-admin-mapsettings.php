<?php
/**
 * Creates the 'map settings' sub-menu settings page
 *
 *
 * @since      0.1.0
 *
 * @package    BuddyBeacon_Maps
 * @subpackage Admin/Partials
 */


// Check that the user is allowed to update options
if (!current_user_can('manage_options')) {

    wp_die('You do not have sufficient permissions to access this page.');

}

?>

<h1><?php _e('BuddyBeacon Maps ', 'buddybeacon-maps'); echo $GLOBALS['title'] ?></h1>
<hr>
<div id="wrap">
	<form method="post" action="options.php">
		
		<?php

			settings_errors(); 
			settings_fields( 'buddybeacon-maps-settings' );
			do_settings_sections( 'buddybeacon-maps-settings' );
			submit_button();

    	?>

	</form>

</div>
