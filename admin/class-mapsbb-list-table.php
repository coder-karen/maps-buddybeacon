<?php
/**
 * Class for displaying maps created
 * in a WordPress-like Admin Table with row actions to 
 * perform map meta operations
 * 
 *
 * @since      0.1.0
 * 
 * @author     Karen Attfield
 */


if( ! class_exists( 'MBB_Legacy_WP_List_Table' ) ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/libraries/class-mbb-legacy-wp-list-table.php';
}



class MBB_Maps_List extends MBB_Legacy_WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'map', 'sp' ), 
			'plural'   => __( 'maps', 'sp' ), 
			'ajax'     => false 

		] );

	}


	/**
	 * Retrieve map data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_maps( $per_page = 5, $page_number = 1 ) {

	  	global $wpdb;

		$table_name = $wpdb->prefix.'mapsbb';

	  	$sql = "SELECT * FROM {$wpdb->prefix}mapsbb";

	  	if (empty($_REQUEST['orderby'] ) ) {
			$sql .=  ' ORDER BY id DESC';
	  	}


	  	if ( ! empty( $_REQUEST['orderby'] ) ) {
		    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  	}

	  	$sql .= " LIMIT $per_page";

	  	$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

	  	$result = $wpdb->get_results( $sql, 'ARRAY_A' );

	  	return $result;

	}


	/**
	 * Delete a map record.
	 *
	 * @param int $id map ID
	 */
	public static function delete_map( $id ) {
	  	global $wpdb;

	  	$wpdb->delete(
	    	"{$wpdb->prefix}mapsbb",
	    	[ 'ID' => $id ],
	    	[ '%d' ]
	  	);
	
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  	
	  	global $wpdb;

	  	$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mapsbb";

		return $wpdb->get_var( $sql );

	}


	/** Text displayed when no map data is available */
	public function no_items() {
	
	  	_e( 'No maps available.', 'sp' );
	
	}


	/**
	 * Method for map title column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_maptitle( $item ) {
		global $wpdb;
		$table_name = $wpdb->prefix.'mapsbb';
	  	
	  	// create a nonce
	  	$delete_nonce = wp_create_nonce( 'sp_delete_map' );

	  	$title = '<strong>' . $item['maptitle'] . '</strong>';
		$url = admin_url( 'admin.php?page=buddybeacon-maps&post=' . absint( $item['id'] ) );
		$edit_link = add_query_arg( array( 'action' => 'edit' ), $url );

		//Determine what the highest current map id is
		$result = $wpdb->get_results('SELECT * FROM ' . $table_name);

		$highest = 0;

		foreach ( $result as $id ) {

		    if($id->id > $highest) {

		    	$highest = $id->id;
		    	$new_copy_id = $highest+1;
		    }
		}

		$actions = array(
		   	'edit' => sprintf('<a href="?page=buddybeacon-add-map&id=%s">%s</a>', $item['id'], __('Edit', 'buddybeacon-maps')));

		$actions = array_merge( $actions, array(
			'copy' => sprintf('<a href="?page=%s&action=copy&id=%s&newid=%s">%s</a>', $_REQUEST['page'], $item['id'], $new_copy_id, __('Copy', 'buddybeacon-maps')),
			) );


		$actions = array_merge( $actions, array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&map=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
			) );

		$actions_object = (object) $actions;
		  
		return $title . $this->row_actions( $actions );
	}


	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
	  	
	  	switch ( $column_name ) {
		    case 'type':
		    case 'id':
		      	return $item[ $column_name ];
		    case 'mapwidth':
		    	if ($item[ $column_name ] == 0) return '100' . $item[ $column_name .'_type' ] ;
		    case 'mapheight':
		    	if ($item[ $column_name ] == 0) {return 'auto';} else { return $item[ $column_name ] . $item[ $column_name .'_type' ];} ;
		    case 'shortcode':
		    	return '[bb_maps id="' . $item['id'] . '"]';
		    default:
		      	return print_r( $item, true ); //Show the whole array for troubleshooting purposes
	  	}

	}


	/* Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	 
	function column_cb( $item ) {

		return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  
            $item['id']  
	        );

	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  	
	  	$columns = [
		    'cb'      => '<input type="checkbox" />',
		    'maptitle'    => __( 'Map Title', 'sp' ),
		    'mapwidth' => __( 'Map Width', 'sp' ),
		    'mapheight' => __( 'Map Height', 'sp' ),
		    'type'    => __( 'Map Type', 'sp' ),
		    'id' => __( 'ID', 'sp' ),
		    'shortcode'    => __( 'Shortcode', 'sp' )
	  	];

	  	return $columns;

	}



	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  	
	  	$sortable_columns = array(
		  	'id' => array( 'id', true ),
		    'maptitle' => array( 'maptitle', false ),
		    'type' => array( 'type', false )
	  	);

	  	return $sortable_columns;

	}


	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

	    $actions = array(
	        'delete'    => 'Delete',
	    );

	   return $actions;

	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		
		global $wpdb;
		$table_name = $wpdb->prefix.'mapsbb';

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		  	echo "Nothing here!";
		} 
		
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'maps_per_page', 5 );

		$current_page = $this->get_pagenum();

		$total_items  = self::record_count();

		$this->set_pagination_args( [
		    'total_items' => $total_items, 
		    'per_page'    => $per_page 
		] );


		$this->items = self::get_maps( $per_page, $current_page );

	}


	/**
	 * Processes bulk actions
	 */
	public function process_bulk_action() {

		global $wpdb;
		$table_name = $wpdb->prefix.'mapsbb';

	    if ('delete' === $this->current_action()) {

	       	if (isset($_REQUEST['map'])) {

				$map_id = ( is_array( $_REQUEST['map'] ) ) ? $_REQUEST['map'] : array( $_REQUEST['map'] );

	        	foreach ( $map_id as $id ) {

	            	$id = absint( $id );

	       			self::delete_map($id);
	        	}
	        	
			}

	    } // end if 'delete'

        if ('copy' === $this->current_action()) {

        	// Retrieve the id of the selected item
	    	$currentid = isset($_GET['id']) ? $_GET['id'] : '';
	    	$currentid = (int)$currentid;

	    	// Retrieve the newid for the new item
			$newid = isset($_GET['newid']) ? $_GET['newid'] : '';
			$newid = (int)$newid;

        	// Update the database (create new duplicate entry with incremented id)
        	$sql = "CREATE TEMPORARY TABLE tmp SELECT * FROM " . $table_name . " WHERE id = " . $currentid;
        	$wpdb->query($sql);
        	$sql = "UPDATE tmp SET id = " . $newid . " WHERE id = " . $currentid;
        	$wpdb->query($sql);
        	$sql = "INSERT INTO " . $table_name . " SELECT * FROM tmp WHERE id = " . $newid;
			$wpdb->query($sql);

        }

	}

 }