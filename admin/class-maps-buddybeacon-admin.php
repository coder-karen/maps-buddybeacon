<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Admin
 * @author     Karen Attfield <mail@karenattfield.com>
 */


include_once( 'class-mapsbb-list-table.php' );


class Maps_BuddyBeacon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $loader     Maintains and registers all hooks for the plugin.
     */
    private $loader;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        
	}

	/**
	 * Add menu pages under the Settings menu item
	 *
	 * @since  0.1.0
	 */
	public function add_menu_pages() {

		$icon = 'dashicons-location-alt';

		if( version_compare( $GLOBALS['wp_version'], '3.8', '<' ) ) {
			$icon = plugin_dir_url() . '/maps-buddybeacon/assets/menu-icon.png';
		}
	
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Maps for BuddyBeacon Settings', 'maps-buddybeacon' ),
			__( 'Maps for BuddyBeacon', 'maps-buddybeacon' ),
			'manage_options',
			'buddybeacon-map-settings',
			array( $this, 'display_explanations_page' ), $icon
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'buddybeacon-map-settings', 
			__( 'How to use', 'maps-buddybeacon' ),  
			__( 'How to use', 'maps-buddybeacon' ), 
			'manage_options', 
		 'buddybeacon-map-settings',
			array( $this, 'display_explanations_page' ) 
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'buddybeacon-map-settings',
			__( 'Add Map', 'maps-buddybeacon' ),
			__( 'Add Map', 'maps-buddybeacon' ),
			'manage_options',
		 'buddybeacon-add-map',
			array( $this, 'display_addmap_subpage' )
		);

		$page_hook = add_submenu_page(
			'buddybeacon-map-settings',
			__( 'Manage Maps', 'maps-buddybeacon' ),
			__( 'Manage Maps', 'maps-buddybeacon' ),
			'manage_options',
		 'buddybeacon-manage-maps',
			array( $this, 'display_managemaps_subpage' )  
		);


		add_action( 'load-'.$page_hook, array( $this, 'maps_buddybeacon_register_manage_maps_setting' ) );
		

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'buddybeacon-map-settings',
			__( 'Settings', 'maps-buddybeacon' ),
			__( 'Settings', 'maps-buddybeacon' ),
			'manage_options',
		 'maps-buddybeacon-settings',
			array( $this, 'display_mapsettings_subpage' )
		);
	
	}



	/**
	 * Render the main settings landing page for plugin
	 *
	 * @since  0.1.0
	 */
	public function display_explanations_page() {

		include_once 'partials/maps-buddybeacon-admin-settings.php';

	}

	/**
	 * Render the 'add map' sub-menu page for plugin
	 *
	 * @since  0.1.0
	 */
	public function display_addmap_subpage() {

	    global $wpdb;
        $table_name = $wpdb->prefix . 'mapsbb'; 

        $message = '';
        $notice = '';

        $dateTime = date('Y-m-d G:i:s');

        // this is default $item which will be used for new records
        $default = array(
            'maptitle' => '',
            'mapwidth' => '',
            'mapwidth_type' => '',
            'mapheight' => '',
            'mapheight_type' => '',
            'id' => 0,
            'alignment' => '',
            'info_box_display' => 0,
            'ib_background' => '#939393',
            'ib_text' => '#ffffff',
            'ib_distance' => '',
            'type' => '',
            'daterange_from' => '',
            'dateend_choice' => '',
            'daterange_to' => $dateTime,     
            'number_beacons' => '',
            'track_colour' => '#ff0000',
            'beacon_delete_lon' => '',
            'beacon_delete_lat' => '',
            'beacon_shape' => '',
            'beacon_colour' => '#ff0000',
            'beacon_opacity' => '0.8',
            'stroke_weight' => 0,
           'stroke_colour' => '#ff0000'
        );
   
 

    if ((isset($_REQUEST['nonce'])) && wp_verify_nonce($_REQUEST['nonce'] , basename(__FILE__))) {

        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = $this->validate_map($item);

        if ($item_valid === true) {
                
            // if id is zero insert otherwise update
            if ($item['id'] == 0) {

                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;

                if ($result) {

                    $message = __('Map was successfully saved', 'maps-buddybeacon');

                } 

                else {

                    $notice = __('There was an error while saving the map', 'maps-buddybeacon');

                }
            } 

            else {

                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));

                if (false === $result) {

                    $notice = __('There was an error while updating item', 'maps-buddybeacon');

                } 

                else {

                    $message = __('Item was successfully updated', 'maps-buddybeacon');

                }
            }

        } // end 'if item valid === true'

        else {

            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;

        }

    }

    else {

        // if this is not post back we load item to edit or give new one to create
        $item = $default;

        if (isset($_REQUEST['id'])) {

            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);

            if (!$item) {

                $item = $default;
                $notice = __('Map not found', 'maps-buddybeacon');

            }
        }

    }
         
    // Here we adding our custom meta box
    add_meta_box('maps_form_meta_box', __( 'Map Information', 'maps-buddybeacon' ), array(&$this, 'maps_buddybeacon_form_meta_box_handler'), 'buddybeacon-add-map', 'normal', 'default');

    ?>
    
    <h1><?php _e('Add Map','maps-buddybeacon')?>
    <div class="icon32 icon32-posts-post" id="icon-edit" style="margin-bottom: 1em;"><br></div>
    </h1>
    <div class="wrap">
        <h1>
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=buddybeacon-add-map');?>"><?php _e('Add new map', 'maps-buddybeacon')?></a>
        </h1>


        <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif; ?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>

            <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
            <div class="settings-pages" style="margin-top:1.2em;" id="add-map-page">
                <div id="settings-body">
                    <div id="settings-content">
                        <?php /* And here we call our custom meta box */  ?>
                        <?php do_meta_boxes('buddybeacon-add-map', 'normal', $item ); 
                        ?>
                        <input type="submit" value="<?php _e('Save', 'maps-buddybeacon')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div> <!-- end #settings-pages -->

        </form>
    </div> <!-- end .wrap -->
    <?php
    }


    /**
     * This function renders our custom meta box
     * $item is row
     *
     * @param $item
     */
    public function maps_buddybeacon_form_meta_box_handler($item) {

        ?>
        <!-- Map data table -->
        <table cellspacing="2" cellpadding="5"  class="form-table mapsbb-form-table" >
            <tbody>
            	<h2 class="table-heading mapsbb-table-heading" >Map data</h2>
            	<hr>

                <!-- Form field to echo map shortcode, if map is being edited (id is already set) -->
                <?php if (isset($item['id'])  && ($item['id'] != 0)) {
                    ?>

                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="id"><?php _e('Map Shortcode', 'maps-buddybeacon')?></label>
                        </th>
                        <td>
                            <small> [bb_maps id="<?php echo $item['id']; ?>"]</small>            
                        </td>
                    </tr>

                <?php
                }
                ?>

            	<!-- Form field for map title -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="maptitle"><?php _e('Map Title', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="maptitle" name="maptitle" type="text" value="<?php echo esc_attr($item['maptitle'])?>"
                               size="50" class="code" placeholder="<?php _e('Map Title', 'maps-buddybeacon')?>" required>
                    </td>
                </tr>

                <!-- Form field for map width -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="mapwidth"><?php _e('Map Width', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="mapwidth" name="mapwidth" type="text" value="<?php if (esc_attr($item['mapwidth']) == 0 ) echo ''; else echo esc_attr($item['mapwidth']) ?>"  size="50" class="code" placeholder="<?php _e('Map Width', 'maps-buddybeacon')?>" >
            	        <select id="mapwidth_type" name="mapwidth_type">
                            <option value="%" <?php if ($item['mapwidth_type'] === '%') echo 'selected="true"' ?> >%</option>
        	                <option value="px" <?php if ($item['mapwidth_type'] === 'px') echo 'selected="true"' ?>>px</option>

            	        </select>
            	        <small>Default is 100%, for a responsive full width map.</small>
                    </td>
                </tr>

                <!-- Form field for map height -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="mapheight"><?php _e('Map Height', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="mapheight" name="mapheight" type="text" value="<?php if (esc_attr($item['mapheight']) == 0 ) echo ''; else echo esc_attr($item['mapheight']) ?>"  size="50" class="code" placeholder="<?php _e('Map Height', 'maps-buddybeacon')?>">
                        <select id="mapheight_type" name="mapheight_type">
                            <option value="px" <?php if ($item['mapheight_type'] === 'px') echo 'selected="true"' ?> >px</option>
                            <option value="%" <?php if ($item['mapheight_type'] === '%') echo 'selected="true"' ?> >%</option>
                        </select>
                        <small>Default is 'auto'.</small>
                    </td>
                </tr>

                <!-- Form field for map alignment -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="alignment"><?php _e('Map Alignment', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <select id="alignment" name="alignment">
                            <option value="Left" <?php if ($item['alignment'] === 'Left') echo 'selected="true"' ?> ><?php _e('Left', 'maps-buddybeacon')?></option>
                            <option value="Right" <?php if ($item['alignment'] === 'Right') echo 'selected="true"' ?> ><?php _e('Right', 'maps-buddybeacon')?></option>
                            <option value="Center" <?php if ($item['alignment'] === 'Center') echo 'selected="true"' ?> ><?php _e('Center', 'maps-buddybeacon')?></option>
                            <option value="None" <?php if ($item['alignment'] === 'None') echo 'selected="true"' ?> ><?php _e('None', 'maps-buddybeacon')?></option>
                        </select>
                    </td>
                </tr>

                <!-- Form field for map type -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="type"><?php _e('Map Type', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <select id="type" name="type">
                            <option value="roadmap" <?php if ($item['type'] === 'roadmap') echo 'selected="true"' ?>>Roadmap</option>
                            <option value="satellite" <?php if ($item['type'] === 'satellite') echo 'selected="true"' ?>>Satellite</option>
                            <option value="hybrid" <?php if ($item['type'] === 'hybrid') echo 'selected="true"' ?>>Hybrid</option>
                            <option value="terrain" <?php if ($item['type'] === 'terrain') echo 'selected="true"' ?>>Terrain</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- Info Box data table -->
        <table cellspacing="2" cellpadding="5" class="form-table">
            <tbody>
                <h2 class="table-heading">Info Box Data</h2>
                <hr>   

                <!-- Form field to hide info box underneath map -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="info_box_display"><?php _e('Hide Info Box', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <!-- Here we are comparing stored value with 1. Stored value is 1 if user checks the checkbox otherwise empty string. -->
                        <input type="checkbox" name="info_box_display" id="info_box_display" value="1" <?php checked(1, $item['info_box_display'],true); ?> /> 
                        <small>Default (unchecked) means the info box under the map will be visible.</small>
                    </td>
                </tr>

                <!-- Form field for info box background colour -->
                <tr class="form-field info-box-info">
                    <th valign="top" scope="row">
                        <label for="ib_background"><?php _e('Info Box Background Colour', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="ib_background" name="ib_background" type="color"  value="<?php echo ($item['ib_background'])?>"
                               size="100" class="code" placeholder="<?php _e('Info Box Background Colour', 'maps-buddybeacon')?>" required>
                    </td>
                </tr>

                 <!-- Form field for info box text colour -->
                <tr class="form-field info-box-info">
                    <th valign="top" scope="row">
                        <label for="ib_text"><?php _e('Info Box Text Colour', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="ib_text" name="ib_text" type="color"  value="<?php echo ($item['ib_text'])?>"
                               size="100" class="code" placeholder="<?php _e('Info Box Text Colour', 'maps-buddybeacon')?>" required>
                    </td>
                </tr>

                <!-- Form field for info box distance measurement -->
                <tr class="form-field info-box-info">
                    <th valign="top" scope="row">
                        <label for="ib_distance"><?php _e('Info Box Distance Type', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <select id="ib_distance" name="ib_distance">
                        <option value="Kilometres" <?php if ($item['ib_distance'] === 'Kilometres') echo 'selected="true"' ?> >Kilometres</option>
                        <option value="Miles" <?php if ($item['ib_distance'] === 'Miles') echo 'selected="true"' ?> >Miles</option>
                        </select>
                    </td>
                </tr>
            
            </tbody>
        </table>


        <!-- Beacon data table -->
        <table cellspacing="2" cellpadding="5" class="form-table">
            <tbody>
                <h2 class="table-heading">Beacon Data</h2>
                <hr>   

                <!-- Form field for beacon date range -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="daterange_from"><?php _e('Date Range', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <p>Date/time from:</p>
                        <input class="datechoice" id="daterange_from" name="daterange_from" type="text" size="100" class="code" placeholder="<?php _e('Start date', 'maps-buddybeacon')?>" required data-date-format="YYYY-MM-DD HH:mm:ss" value="<?php echo $item['daterange_from'] ?>"><p>Date/time to:</p>
                        <select id="dateend_choice" name="dateend_choice" autocomplete="off">
                            <option id="currentdate" value="currentdate" <?php if ($item['dateend_choice'] === 'currentdate') echo 'selected="true"' ?> >Current date</option>
                            <option id="selectdate" value="selectdate"  <?php if ($item['dateend_choice'] === 'selectdate') echo 'selected="true"' ?> > Select date</option>
                        </select>

                        <input class="datechoice" id="daterange_to" name="daterange_to" type="hidden"
                       size="100" class="code" placeholder="<?php _e('End date', 'maps-buddybeacon')?>" required data-date-format="YYYY-MM-DD HH:mm:ss" value="<?php echo $item['daterange_to'] ?>">
                       
                    </td>
                </tr>

                <!-- Form field for max number of beacons -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="number_beacons"><?php _e('Max. number beacons', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="number_beacons" name="number_beacons" type="text"  value="<?php if (esc_attr($item['number_beacons']) == 0 ) echo ''; else echo esc_attr($item['number_beacons'])?>"
                               size="50" class="code" placeholder="<?php _e('Max. number beacons', 'maps-buddybeacon')?>">
                        <small> Maximum number of beacons to display. Default (empty) is no max.</small>
                    </td>
                </tr>

                <!-- Form field for track colour -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="track_colour"><?php _e('Track Colour', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="track_colour" name="track_colour" type="color"  value="<?php echo ($item['track_colour'])?>"
                               size="100" class="code" placeholder="<?php _e('Track Colour', 'maps-buddybeacon')?>" required>
                    </td>
                </tr>

                  <!-- Form field for deleting beacons -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="beacon_delete_lon"><?php _e('Delete Beacon', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="beacon_delete_lon" name="beacon_delete_lon" type="text"  value="<?php echo ($item['beacon_delete_lon'])?>"
                               size="50" class="code" placeholder="<?php _e('Enter longitude of beacon', 'maps-buddybeacon')?>">
                                <input id="beacon_delete_lat" name="beacon_delete_lat" type="text"  value="<?php echo ($item['beacon_delete_lat'])?>"
                               size="50" class="code" placeholder="<?php _e('Enter latitude of beacon', 'maps-buddybeacon')?>" >
                               <br/><small>Enter the latitude and longitude exactly as they appear in the map Info Window for the beacon you wish to delete.</small>
                    </td>
                </tr>
                


                <!-- Beacon style sub-heading -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <h4 class="table-sub-heading">Beacon Style</h4>
                    </th>
                </tr>

                <!-- Form field for beacon shape -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="beacon_shape"><?php _e('Beacon Shape', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <select id="beacon_shape" name="beacon_shape">
                            <option value="Circle" <?php if ($item['beacon_shape'] === 'Circle') echo 'selected="true"' ?> >Circle</option>
                            <option value="Square" <?php if ($item['beacon_shape'] === 'Square') echo 'selected="true"' ?> >Square</option>
                        </select>
                    </td>
                </tr>

                <!-- Form field for beacon colour -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="beacon_colour"><?php _e('Beacon Colour', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="beacon_colour" name="beacon_colour" type="color"  value="<?php echo ($item['beacon_colour'])?>"
                               size="100" class="code" placeholder="<?php _e('Beacon Colour', 'maps-buddybeacon')?>" required>
                    </td>
                </tr>

                <!-- Form field for beacon opacity -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="beacon_opacity"><?php _e('Beacon Opacity', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <select id="beacon_opacity" name="beacon_opacity">
                            <option value="0" <?php if ($item['beacon_opacity'] === '0') echo 'selected="true"' ?> >0</option>
                            <option value="0.1" <?php if ($item['beacon_opacity'] === '0.1') echo 'selected="true"' ?> >0.1</option>
                            <option value="0.2" <?php if ($item['beacon_opacity'] === '0.2') echo 'selected="true"' ?> >0.2</option>
                            <option value="0.3" <?php if ($item['beacon_opacity'] === '0.3') echo 'selected="true"' ?> >0.3</option>
                            <option value="0.4" <?php if ($item['beacon_opacity'] === '0.4') echo 'selected="true"' ?> >0.4</option>
                            <option value="0.5" <?php if ($item['beacon_opacity'] === '0.5') echo 'selected="true"' ?> >0.5</option>
                            <option value="0.6" <?php if ($item['beacon_opacity'] === '0.6') echo 'selected="true"' ?> >0.6</option>
                            <option value="0.7" <?php if ($item['beacon_opacity'] === '0.7') echo 'selected="true"' ?> >0.7</option>
                            <option value="0.8" <?php if ($item['beacon_opacity'] === '0.8') echo 'selected="true"' ?> >0.8</option>
                            <option value="0.9" <?php if ($item['beacon_opacity'] === '0.9') echo 'selected="true"' ?> >0.9</option>
                            <option value="1" <?php if ($item['beacon_opacity'] === '1') echo 'selected="true"' ?> >1</option>
                        </select>
                        <small>The lower the value, the more transparent the beacon fill colour.</small>
                    </td>
                </tr>

                <!-- Form field for beacon stroke weight -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                    <label for="stroke_weight"><?php _e('Stroke Weight', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="stroke_weight" name="stroke_weight" type="text" value="<?php echo esc_attr($item['stroke_weight'])?>"
                               size="50" class="code" placeholder="<?php _e('Stroke Weight', 'maps-buddybeacon')?>" required>
            	       <small>Beacon border size, in px. Default is 0 (no border).</small>
                    </td>
                </tr>

                <!-- Form field for beacon stroke colour -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="stroke_colour"><?php _e('Stroke Colour', 'maps-buddybeacon')?></label>
                    </th>
                    <td>
                        <input id="stroke_colour" name="stroke_colour" type="color"  value="<?php echo ($item['stroke_colour'])?>"
                               size="100" class="code" placeholder="<?php _e('Stroke Colour', 'maps-buddybeacon')?>" required>
                        <small>Beacon border colour.</small>
                    </td>
                </tr>

            </tbody>
        </table>

    <?php
    }

    /**
     * Simple function that validates data and retrieve bool on success
     * and error message(s) on error
     *
     * @param $item
     * @return bool|string
     */
    function validate_map($item) {
    
        $messages = array();

        if (empty($item['maptitle'])) $messages[] = __('Map Title is required', 'maps-buddybeacon');

        if (($item['mapheight'] != '') && !ctype_digit($item['mapheight'])) $messages[] = __('Map height in wrong format', 'maps-buddybeacon');
        
        if (($item['mapwidth'] != '') && !ctype_digit($item['mapwidth'])) $messages[] = __('Map width in wrong format', 'maps-buddybeacon');
        
        if (($item['number_beacons'] != '') && !ctype_digit($item['number_beacons'])) $messages[] = __('Number of beacons in wrong format', 'maps-buddybeacon');
        
        if (!ctype_digit($item['stroke_weight'])) $messages[] = __('Stroke weight in wrong format', 'maps-buddybeacon');

        if(($item['daterange_to'] < $item['daterange_from']) ) $messages[] = __('"To" date and time earlier than "From" date and time', 'maps-buddybeacon');

         if (($item['beacon_delete_lon'] != '') && !is_numeric($item['beacon_delete_lon'])) $messages[] = __('"Delete Beacon" longitude in wrong format', 'maps-buddybeacon');
          if (($item['beacon_delete_lat'] != '') && !is_numeric($item['beacon_delete_lat'])) $messages[] = __('"Delete Beacon" latitude in wrong format', 'maps-buddybeacon');

            if ((($item['beacon_delete_lon'] != '') && ($item['beacon_delete_lat'] == '')) || (($item['beacon_delete_lon'] == '') && ($item['beacon_delete_lat'] != '')) ) $messages[] = __('Both longitude and latitude needed in order to delete beacon.');

        if (empty($messages)) return true;

        return implode('<br />', $messages);

	}


	/**
	 * Render the 'manage maps' sub-menu page for plugin
	 *
	 * @since  0.1.0
	 */
	public function display_managemaps_subpage() {

	   include_once 'partials/maps-buddybeacon-admin-managemaps.php';

	}


	/**
	 * Render the 'settings' sub-menu page for plugin
	 *
	 * @since  0.1.0
	 */
	public function display_mapsettings_subpage() {

		include_once 'partials/maps-buddybeacon-admin-mapsettings.php';

	}


    /**
	* Screen options for the List Table
	*
	* Callback for the load-($page_hook_suffix)
	* Called when the plugin 'manage maps' page is loaded
	* 
	* @since    0.1.0
	*/
	public function maps_buddybeacon_register_manage_maps_setting() {
				
		$option = 'per_page';
	    $args   = [
		'label'   => 'Maps',
		'default' => 5,
		'option'  => 'maps_per_page'
	    ];

    	add_screen_option( $option, $args );

    	$this->mapsbb_obj = new MBB_Maps_List();	
		
	}



    /**
     * Register the settings for our map settings page.
     *
     * @since    0.1.0
     */
    public function register_map_settings() {

    	add_settings_section(
    		$this->plugin_name . '-settings', 
    		__( 'Google Maps & ViewRanger Settings', 'maps-buddybeacon' ), 
    		array( $this, 'maps_buddybeacon_settings_section' ), 
    		$this->plugin_name . '-settings'  
    	);

    	// Adding the Google API settings field
    	add_settings_field(
    		$this->plugin_name . '-settings',  
    		__( 'Google Maps API Key', 'maps-buddybeacon' ),
    		array( $this, 'maps_buddybeacon_googleapi_text' ), 
    		$this->plugin_name . '-settings', 
    		$this->plugin_name . '-settings',
    		array(
    			'label_for' => $this->plugin_name . '_googleapi'  
    		)
    	);

        // Adding the Viewranger API settings field
        add_settings_field(
            $this->plugin_name . '_viewrangerapi', 
            __( 'Viewranger API Key', 'maps-buddybeacon' ),
            array( $this, 'maps_buddybeacon_viewrangerapi_text' ), 
            $this->plugin_name . '-settings', 
            $this->plugin_name . '-settings',
            array(
                'label_for' => $this->plugin_name . '_viewrangerapi', 
            )
        );

        // Adding the BuddyBeacon username settings field
        add_settings_field(
            $this->plugin_name . '_bbuser', 
            __( 'BuddyBeacon Username', 'maps-buddybeacon' ), 
            array( $this, 'maps_buddybeacon_bbusername_text' ), 
            $this->plugin_name . '-settings', 
            $this->plugin_name . '-settings', 
            array(
                'label_for' => $this->plugin_name . '_bbuser', 
            )
        );

        add_settings_field(
            $this->plugin_name . '_bbpin',
            __( 'BuddyBeacon Pin', 'maps-buddybeacon' ), 
            array( $this, 'maps_buddybeacon_bbpin_text' ),
            $this->plugin_name . '-settings', 
            $this->plugin_name . '-settings',
            array(
                'label_for' => $this->plugin_name . '_bbpin',
            )
        );


        register_setting(
            'maps-buddybeacon-settings', 
            'maps-buddybeacon-settings', 
            array( $this, 'maps_buddybeacon_register_map_setting' )
        );

    }


    /**
     * Sanitize the info in the 'map settings' settings page
     *
     * @since    0.1.0
     */
    public function maps_buddybeacon_register_map_setting($input ) {
     
        // Create our array for storing the validated options
        $output = array();
         
        // Loop through each of the incoming options
        foreach( $input as $key => $value ) {
   
            // Check to see if the current option has a value. If so, process it.
            if( isset( $input[$key] ) ) {

                if ('maps-buddybeacon_bbpin' == $key && ($input[$key] != '')) {

                    $numlength = mb_strlen($input[$key]);

                     if ((!ctype_digit($input[$key]))) {
                        add_settings_error(
                            'maps-buddybeacon_bbpin',
                            'key-not-numeric',
                            'BuddyBeacon Pin must be made up of numbers only',
                            'error'

                        );
                        $input[$key] = '';

                    }

                    elseif (($numlength != 4))  {
                        add_settings_error(
                            'maps-buddybeacon_bbpin',
                            'key-wrong-length',
                            'BuddyBeacon Pin must be 4 digits long',
                            'error'

                        );
                        $input[$key] = '';
                    }
                 
                   
                    else {
                        $output[$key] = $input[$key];
                    }

                }

                // Strip all HTML and PHP tags and properly handle quoted strings
                $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

            } // end if isset $input[$key]
         
        } // end foreach
   
     
        // Return the array processing any additional functions filtered by this action
        return $output;

    }


    /**
     * Notifies the user that there are blank fields on the settings page 
     * @return void
     */
    public function settings_page_empty_boxes_warning() {

        // check the current screen
        $screen = get_current_screen();

        // if the current screen matches the add-map screen
        if ( 'maps-buddybeacon_page_buddybeacon-add-map' == $screen->id ) {

            $googleapi = get_option('maps-buddybeacon-settings')['maps-buddybeacon_googleapi'];
            $vrkey = get_option('maps-buddybeacon-settings')['maps-buddybeacon_viewrangerapi'];
            $username = get_option('maps-buddybeacon-settings')['maps-buddybeacon_bbuser'];
            $pin = get_option('maps-buddybeacon-settings')['maps-buddybeacon_bbpin'];

            if (($googleapi == '') || ($vrkey == '') || ($username == '') || ($pin == ''))  {
                ?>
                <!-- echo out an error message -->
                <div class="notice notice-warning">
                    <p>
                        <strong>
                            <a href="?page=maps-buddybeacon-settings" target="_blank">
                            <?php 
                            _e( 'API and BuddyBeacon settings</a> must be filled in before any maps will display.', 'maps-buddybeacon' );
                            ?>
                            </a>
                        </strong>
                    </p>
                </div>

                <?php
            }
        
        }  

    }


    /**
	 * Render the text for the 'Google Maps Api' settings section
	 *
	 * @since  0.1.0
	 */
    public function maps_buddybeacon_settings_section() {
    		
    	echo '<p>' . __( 'In order for your maps to show your locations, you need will need to input valid information in all of the below fields.', 'maps-buddybeacon' ) . '</p>';
    }



	/**
	 * Function for the Google API text field section
	 *
	 * @since  0.1.0
	 */
	public function maps_buddybeacon_googleapi_text($args) {
		
        $field_id = $args['label_for'];
        $name = $this->plugin_name . '-settings[' . $field_id . ']';
        $options = get_option('maps-buddybeacon-settings');
         
        ?>

        <input type="text" name="<?php echo $name; ?>" id="<?php echo $name ?>" value="<?php echo esc_attr($options[$field_id]) ; ?>" class="regular-text" />
        <small>
            <?php 
            $url = "//developers.google.com/maps/documentation/javascript/"; 
            echo sprintf( wp_kses( __( 'Create a <a href="%s" target="_blank">Google Maps API Key</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url ) );
            ?>
        </small>

        <?php
    }


    /**
	 * Function for the Viewranger API text field section
	 *
	 * @since  0.1.0
	 */
	public function maps_buddybeacon_viewrangerapi_text($args) {
		
         $field_id = $args['label_for'];
         $name = $this->plugin_name . '-settings[' . $field_id . ']';
         $options = get_option('maps-buddybeacon-settings');

    	?>
	   
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name ?>" value="<?php echo esc_attr($options[$field_id]) ; ?>" class="regular-text" />
        <small>
            <?php 
            $url = "//www.viewranger.com/developers/register/"; 
            echo sprintf( wp_kses( __( 'Create a <a href="%s" target="_blank">ViewRanger API Key</a>.', 'maps-buddybeacon' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $url ) );
            ?>
        </small>
   
        <?php
    }


    /**
	 * Function for the BuddyBeacon Username text field section
	 *
	 * @since  0.1.0
	 */
	public function maps_buddybeacon_bbusername_text($args) {
		
        $field_id = $args['label_for'];
    	$name = $this->plugin_name . '-settings[' . $field_id . ']';
        $options = get_option('maps-buddybeacon-settings');

        ?>
       
        <input type="text" name="<?php echo $name; ?>" id="<?php echo $name ?>" value="<?php echo esc_attr($options[$field_id]) ; ?>" class="regular-text" />

    	<?php
    }


    /**
	 * Function for the BuddyBeacon Pin text field section
	 *
	 * @since  0.1.0
	 */
	public function maps_buddybeacon_bbpin_text($args) {
			
        $field_id = $args['label_for'];
    	$name = $this->plugin_name . '-settings[' . $field_id . ']';
        $options = get_option('maps-buddybeacon-settings');
        ?>
       
        <input type="text" name="<?php echo $name; ?>" id="<?php echo $name ?>" value="<?php echo esc_attr($options[$field_id]) ; ?>" class="regular-text" max-length="4" />

	   <?php
    }


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

		wp_enqueue_style('jquery-style-boot', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');

		wp_enqueue_style('jquery-style-time', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css');

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/maps-buddybeacon-admin.css', array(), $this->version, 'all' );

    }


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/maps-buddybeacon-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script('jquery-script-time-moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js');

		wp_enqueue_script('jquery-script-time', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js');

	}

}
