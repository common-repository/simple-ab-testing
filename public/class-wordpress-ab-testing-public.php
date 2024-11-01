<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/public
 * @author     Multidots <inquiry@multidots.in>
 */
class Wordpress_Ab_Testing_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Ab_Testing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Ab_Testing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordpress-ab-testing-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Ab_Testing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Ab_Testing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wordpress-ab-testing-public.js', array( 'jquery' ), $this->version, false );

	}

	public function ab_testing_cookie() {
		global $wpdb, $wp, $post;

		$url = esc_url( "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$url = str_replace( array( 'http://', 'www.' ), array( '', '' ), $url );

		$url = rtrim( $url, "/" );
		if ( isset( $_COOKIE['insert_ab_testing_code'] ) ) {
			unset( $_COOKIE['insert_ab_testing_code'] );
			setcookie( 'insert_ab_testing_code', '', time() - ( 15 * 60 ) );
		}
		$ab_confirme_variation_query        = $wpdb->prepare( 'Select * FROM ' . $wpdb->prefix . 'ab_experiment AB_EXE INNER JOIN ' . $wpdb->prefix . 'ab_variations AB_VARI ON AB_EXE.experiment_id = AB_VARI.experiment_id WHERE AB_EXE.experiment_status = 1 AND FIND_IN_SET( "%s", AB_EXE.target_url )', $url );
		$ab_confirme_variation_query_result = $wpdb->get_results( $ab_confirme_variation_query );

		if ( count( $ab_confirme_variation_query_result ) > 0 ) {

			foreach ( $ab_confirme_variation_query_result as $ab_confirme_variation_query_value ) {
				$experiment_id = $ab_confirme_variation_query_value->experiment_id;
				$variation_id  = $ab_confirme_variation_query_value->variation_id;

				if ( isset( $_COOKIE[ 'AB_Testing_experiment_' . $experiment_id ] ) && isset( $_COOKIE[ 'AB_Testing_variation_' . $variation_id ] ) ) {
					$ab_variation_action     = $ab_confirme_variation_query_value->variation_action;
					$ab_experiment_id_cookie = 'AB_Testing_experiment_' . $experiment_id;
					$ab_variation_id_cookie  = 'AB_Testing_variation_' . $variation_id;
					$ab_variation_user_count = $ab_confirme_variation_query_value->variation_count + 1;
					break;
				}
			}

			if ( ! isset( $ab_experiment_id_cookie ) && ! isset( $ab_variation_id_cookie ) ) {

				$experiment_result_id  = $ab_confirme_variation_query_result[0]->experiment_id;
				$variation_count_query = $wpdb->prepare( "SELECT sum(variation_count) total FROM " . $wpdb->prefix . "ab_variations WHERE experiment_id =%d",
					$experiment_result_id );
				$variation_count_total = $wpdb->get_results( $variation_count_query );
				$variationCount        = $variation_count_total[0]->total;

				$variation_query        = $wpdb->prepare( "Select variation_id,variation_count,variation_percentage,variation_action from " . $wpdb->prefix . "ab_variations where experiment_id =%d", $experiment_result_id );
				$variation_query_result = $wpdb->get_results( $variation_query );
				$draw                   = true;
				if ( ! empty( $variation_query_result ) && $variationCount > 0 ) {
					foreach ( $variation_query_result as $var ) {
						$formula = $var->variation_count * 100 / $variationCount;
						if ( $formula < $var->variation_percentage ) {
							$experiment_id           = $experiment_result_id;
							$variation_id            = $var->variation_id;
							$ab_variation_action     = $var->variation_action;
							$ab_experiment_id_cookie = 'AB_Testing_experiment_' . $experiment_result_id;
							$ab_variation_id_cookie  = 'AB_Testing_variation_' . $var->variation_id;
							$ab_variation_user_count = $var->variation_count + 1;
							$draw                    = false;
							break;
						}
					}
				}
				if ( $draw == true ) {
					$variation_max_percentage_query  = $wpdb->prepare( "SELECT variation_id,variation_count,variation_action,max(variation_percentage) max FROM " . $wpdb->prefix . "ab_variations WHERE experiment_id=%d", $experiment_result_id );
					$variation_max_percentage_result = $wpdb->get_results( $variation_max_percentage_query );
					$experiment_id                   = $experiment_result_id;
					$variation_id                    = $variation_max_percentage_result[0]->variation_id;
					$ab_variation_action             = $variation_max_percentage_result[0]->variation_action;
					$ab_experiment_id_cookie         = 'AB_Testing_experiment_' . $experiment_result_id;
					$ab_variation_id_cookie          = 'AB_Testing_variation_' . $variation_max_percentage_result[0]->variation_id;
					$ab_variation_user_count         = $variation_max_percentage_result[0]->variation_count + 1;
				}
			}
			if (
				isset( $ab_experiment_id_cookie, $ab_variation_id_cookie )
				&& ! isset( $_COOKIE[ $ab_experiment_id_cookie ] )
				&& ! isset( $_COOKIE[ $ab_variation_id_cookie ] )
			) {
				$UpdateVariationCount = $wpdb->prepare( "UPDATE " . $wpdb->prefix . "ab_variations SET variation_count =%d WHERE variation_id =%d",
					$ab_variation_user_count, $variation_id
				);
				$wpdb->query( $UpdateVariationCount );
			}
			setcookie( $ab_experiment_id_cookie, "true", time() + 82800 );
			if ( ! isset( $_COOKIE[ $ab_experiment_id_cookie ] ) ) {
				$_COOKIE[ $ab_experiment_id_cookie ] = "true";
			}
			setcookie( $ab_variation_id_cookie, "true", time() + 82800 );
			if ( ! isset( $_COOKIE[ $ab_variation_id_cookie ] ) ) {
				$_COOKIE[ $ab_variation_id_cookie ] = "true";
			}
			if ( ! empty( $ab_variation_action ) ) {
				setcookie( 'insert_ab_testing_code', stripcslashes( $ab_variation_action ), time() + 82800 );
				if ( ! isset( $_COOKIE['insert_ab_testing_code'] ) ) {
					$_COOKIE['insert_ab_testing_code'] = stripcslashes( $ab_variation_action );
				}
			}
		}
	}

	public function insert_ab_testing_code_head() {
		if ( isset( $_COOKIE['insert_ab_testing_code'] ) && ! empty( $_COOKIE['insert_ab_testing_code'] ) && ! isset( $_REQUEST['edit_exp'] ) ) { ?>
			<script>
							jQuery( document ).ready( function( $ ) {
				  <?php echo stripcslashes( $_COOKIE['insert_ab_testing_code'] ); ?>
							} );
			</script>
		<?php }
	}

	function pluginname_ajaxurl() {
		?>
		<script type="text/javascript">
					var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		</script>
		<?php
	}

	public function ab_visitor_click_count() {
		global $wpdb;
		$url = isset( $_POST['curr_url'] ) ? esc_url( $_POST['curr_url'] ) : '';

		$url = str_replace( array( "http://", "www." ), array( "", "" ), $url );

		$url     = rtrim( $url, "/" );
		$cookies = isset( $_POST['cookie_list'] ) ? $_POST['cookie_list'] : '';
		/* $cookies = [];
		 $headers = headers_list();
		 foreach($headers as $header) {
			 if (strpos($header, 'Set-Cookie: ') === 0) {
				 $value = str_replace('&', urlencode('&'), substr($header, 12));
				 parse_str(current(explode(';', $value, 1)), $pair);
				 $cookies = array_merge_recursive($cookies, $pair);
			 }
		 }*/
		$ab_confirme_variation_query = $wpdb->prepare( 'Select * FROM ' . $wpdb->prefix . 'ab_experiment AB_EXE INNER JOIN ' . $wpdb->prefix . 'ab_variations AB_VARI ON AB_EXE.experiment_id = AB_VARI.experiment_id WHERE AB_EXE.experiment_status = 1 AND FIND_IN_SET( "%s", AB_EXE.target_url )', $url );

		$ab_confirme_variation_query_result = $wpdb->get_results( $ab_confirme_variation_query );

		if ( count( $ab_confirme_variation_query_result ) > 0 ) {
			foreach ( $ab_confirme_variation_query_result as $ab_confirme_variation_query_value ) {
				$experiment_id           = $ab_confirme_variation_query_value->experiment_id;
				$ab_variation_engagement = $ab_confirme_variation_query_value->variation_engagement;
				$variation_id            = $ab_confirme_variation_query_value->variation_id;
				$ab_experiment_id_cookie = isset( $cookies[ 'AB_Testing_experiment_' . $experiment_id ] ) ? $cookies[ 'AB_Testing_experiment_' . $experiment_id ] : '';
				$ab_variation_id_cookie  = isset( $cookies[ 'AB_Testing_variation_' . $variation_id ] ) ? $cookies[ 'AB_Testing_variation_' . $variation_id ] : '';

				if ( isset( $ab_experiment_id_cookie, $ab_variation_id_cookie ) && ! empty( $ab_experiment_id_cookie ) && ! empty( $ab_variation_id_cookie ) ) {
					if ( ! isset( $_COOKIE['set_engagement'] ) ) {
						$ab_variation_engagement = $ab_confirme_variation_query_value->variation_engagement + 1;
						$UpdateVariationCount    = $wpdb->prepare( "UPDATE " . $wpdb->prefix . "ab_variations SET variation_engagement =%s WHERE variation_id =%d",
							$ab_variation_engagement, $variation_id );

						$wpdb->query( $UpdateVariationCount );
					}
					setcookie( 'set_engagement', "true", time() + 82800 );
					if ( ! isset( $_COOKIE['set_engagement'] ) ) {
						$_COOKIE['set_engagement'] = "true";
					}
					break;
				}
			}
		}
		exit();
	}

}
