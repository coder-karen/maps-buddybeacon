<?php
/**
 * Creates the 'manage map' sub-menu settings page
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


<h1><?php _e('Manage Maps', 'maps-buddybeacon'); ?>
<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
</h1>
<div class="wrap">
	<h1>
	   <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=buddybeacon-add-map');?>"><?php _e('Add new map', 'maps-buddybeacon')?></a>
	</h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-1">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable" id="manage-mapsbb">
					<form method="post">
						
						<?php
						
						$this->mapsbb_obj->prepare_items();

						$this->mapsbb_obj->display(); ?>
					</form>

				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>

