<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.multidots.com/
 * @since             1.0.1
 * @package           Wordpress_Ab_Testing
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress And WooCommerce A/B Testing
 * Plugin URI:        https://wordpress.org/plugins/simple-ab-testing
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.2
 * Author:            Multidots
 * Author URI:        http://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-ab-testing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!defined('WABT_PLUGIN_URL'))
    define('WABT_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('WABT_PLUGIN_VERSION'))
    define('WABT_PLUGIN_VERSION', '1.2');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-ab-testing-activator.php
 */
function activate_wordpress_ab_testing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-ab-testing-activator.php';
	Wordpress_Ab_Testing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-ab-testing-deactivator.php
 */
function deactivate_wordpress_ab_testing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-ab-testing-deactivator.php';
	Wordpress_Ab_Testing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordpress_ab_testing' );
register_deactivation_hook( __FILE__, 'deactivate_wordpress_ab_testing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-ab-testing.php';

require plugin_dir_path(__FILE__) . 'includes/wordpress-ab-testing-constant.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordpress_ab_testing() {

	$plugin = new Wordpress_Ab_Testing();
	$plugin->run();

}
run_wordpress_ab_testing();
