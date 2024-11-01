<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Wordpress_Ab_Testing_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            global $wpdb;
            $jal_db_version = '1.0';
            set_transient( '_welcome_screen_ab_testing_mode_activation_redirect_data', true, 30 );
            $table_name = $wpdb->prefix . 'ab_experiment';
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `experiment_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `experiment_name` VARCHAR( 255 ) NOT NULL ,
                    `experiment_url` VARCHAR( 255 ) NOT NULL ,
                    `target_url` TEXT NOT NULL ,
                    `experiment_created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `experiment_modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `experiment_status` INT NOT NULL
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $variations_table_name = $wpdb->prefix . 'ab_variations';
            $variations_sql = "CREATE TABLE IF NOT EXISTS $variations_table_name (
                    `variation_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `experiment_id` INT NOT NULL ,
                    `variation_name` VARCHAR( 255 ) NOT NULL ,
                    `variation_action` LONGTEXT NOT NULL ,
                    `variation_percentage` VARCHAR( 50 ) NOT NULL ,
                    `variation_count` INT NOT NULL ,
                    `variation_engagement` INT NOT NULL ,
                    `variation_created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `variation_modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
            ) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $variations_sql );

            add_option( 'jal_db_version', $jal_db_version );
	}
}
