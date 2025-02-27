<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/includes
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
 * @since      1.0.0
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Wordpress_Ab_Testing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wordpress_Ab_Testing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'simple-ab-testing';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wordpress_Ab_Testing_Loader. Orchestrates the hooks of the plugin.
	 * - Wordpress_Ab_Testing_i18n. Defines internationalization functionality.
	 * - Wordpress_Ab_Testing_Admin. Defines all hooks for the admin area.
	 * - Wordpress_Ab_Testing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-ab-testing-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-ab-testing-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordpress-ab-testing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordpress-ab-testing-public.php';

		$this->loader = new Wordpress_Ab_Testing_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wordpress_Ab_Testing_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wordpress_Ab_Testing_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        $plugin_admin = new Wordpress_Ab_Testing_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        if (empty($GLOBALS['admin_page_hooks']['dots_store'])) {
            $this->loader->add_action('admin_menu', $plugin_admin, 'dot_store_menu');
        }
        $this->loader->add_action('admin_init', $plugin_admin, 'welcome_ab_testing_screen_do_activation_redirect');
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'ab_testing_admin_menu', 30 );

        $this->loader->add_action('wp_ajax_ab_testing_single_delete_experiment', $plugin_admin, 'ab_testing_single_delete_experiment');
        $this->loader->add_action('wp_ajax_nopriv_ab_testing_single_delete_experiment',$plugin_admin, 'ab_testing_single_delete_experiment');

        $this->loader->add_action('wp_ajax_nopriv_ab_testing_multiple_delete_experiments', $plugin_admin, 'ab_testing_multiple_delete_experiments');
        $this->loader->add_action('wp_ajax_ab_testing_multiple_delete_experiments', $plugin_admin, 'ab_testing_multiple_delete_experiments');

        $this->loader->add_action('wp_ajax_ab_testing_change_experiment_status', $plugin_admin, 'ab_testing_change_experiment_status');
        $this->loader->add_action('wp_ajax_nopriv_ab_testing_change_experiment_status',$plugin_admin, 'ab_testing_change_experiment_status');

        $this->loader->add_action('admin_footer', $plugin_admin, 'ab_testing_footer_script');

        $this->loader->add_action('wp_ajax_ab_testing_post_name_search', $plugin_admin, 'ab_testing_post_name_search');
		$this->loader->add_action('wp_ajax_nopriv_ab_testing_post_name_search',$plugin_admin, 'ab_testing_post_name_search');

        $this->loader->add_action('wp_ajax_ab_testing_delete_variation_edit_page', $plugin_admin, 'ab_testing_delete_variation_edit_page');
        $this->loader->add_action('wp_ajax_nopriv_ab_testing_delete_variation_edit_page',$plugin_admin, 'ab_testing_delete_variation_edit_page');
                
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wordpress_Ab_Testing_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                
                $this->loader->add_action( 'init', $plugin_public, 'ab_testing_cookie' );
                
                $this->loader->add_action( 'wp_ajax_ab_visitor_click_count', $plugin_public, 'ab_visitor_click_count' );
		$this->loader->add_action( 'wp_ajax_nopriv_ab_visitor_click_count', $plugin_public, 'ab_visitor_click_count' );
                
		$this->loader->add_action( 'wp_head', $plugin_public, 'pluginname_ajaxurl' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'insert_ab_testing_code_head' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wordpress_Ab_Testing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
