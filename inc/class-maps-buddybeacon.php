<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      0.1.0
 *
 * @package    Maps_BuddyBeacon
 * @subpackage Inc
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Maps_BuddyBeacon
 * @subpackage Inc
 * @author     Karen Attfield <mail@karenattfield.com>
 */

class Maps_BuddyBeacon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Maps_BuddyBeacon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {

		if ( defined( 'MAPS_BUDDYBEACON_VERSION' ) ) {

			$this->version = MAPS_BUDDYBEACON_VERSION;

		} 

		else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'maps-buddybeacon';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->mb_check_plugin_version();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Maps_BuddyBeacon_Loader. Orchestrates the hooks of the plugin.
	 * - Maps_BuddyBeacon_i18n. Defines internationalization functionality.
	 * - Maps_BuddyBeacon_Admin. Defines all hooks for the admin area.
	 * - Maps_BuddyBeacon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/class-maps-buddybeacon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/class-maps-buddybeacon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-maps-buddybeacon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'pub/class-maps-buddybeacon-public.php';

		$this->loader = new Maps_BuddyBeacon_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Maps_BuddyBeacon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Maps_BuddyBeacon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 *
	 * Checks the current version to see if it has changed
	 *
	 * @since    0.1.0
	 * @access   public
	 */
	 function mb_check_plugin_version() {

		if (MAPS_BUDDYBEACON_VERSION !== get_option('mb_plugin_version')) {

	
			$plugin_admin = new Maps_BuddyBeacon_Admin( $this->get_plugin_name(), $this->get_version());
		
			// Adds the correct plugin version number
			$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'mb_rerun_activation' );

		}


		
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Maps_BuddyBeacon_Admin( $this->get_plugin_name(), $this->get_version() ) ;

		// Hook our scripts and styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//Hook our settings pages
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_pages' );

		// Hook our map settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_map_settings' );

		// Hook our settings page empty boxes warning function
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'settings_page_empty_boxes_warning' );

	

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Maps_BuddyBeacon_Public( $this->get_plugin_name(), $this->get_version());

		// Hook our scripts and styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Hook our shortcode function
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

		// Hook our async function
		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'google_maps_script_attributes', 10, 2 );	
		
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Maps_BuddyBeacon_Loader    Orchestrates the hooks of the plugin.
	 */

	public function get_loader() {

		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;
		
	}

}
