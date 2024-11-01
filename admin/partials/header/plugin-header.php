<?php /*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ ?>
<div id="dotsstoremain">
	<div class="all-pad">
		<header class="dots-header">
			<div class="dots-logo-main">
				<img src="<?php echo esc_url( WABT_PLUGIN_URL . '/admin/images/logo.png' ); ?>">
			</div>
			<div class="dots-header-right">
				<div class="logo-detail">
					<strong><?php _e( WABT_PLUGIN_NAME ); ?></strong>
					<span><?php _e( 'Free Version' ); ?><?php echo WABT_PLUGIN_VERSION; ?></span>
				</div>
				<div class="button-dots">
					<span class="support_dotstore_image"><a target="_blank" href="https://store.multidots.com/dotstore-support-panel/"> <img
									src="<?php echo esc_url( WABT_PLUGIN_URL . 'admin/images/support_new.png' ); ?>"></a></span>
				</div>
			</div>

			<?php
			$experiment_listings_setting_menu_enable      = '';
			$add_experiment_setting_meny_enable           = '';
			$experiment_results_setting_meny_enable       = '';
			$about_plugin_setting_menu_enable             = '';
			$dotstore_setting_menu_enable                 = '';
			$dotpremium_setting_menu_enable               = '';
			$get_started_about_plugin_setting_menu_enable = '';
			$about_plugin_get_started                     = '';
			$about_plugin_quick_info                      = '';
			$premium_plugin_tab                           = '';

			if ( isset( $_GET['tab'], $_GET['page'] ) ) {
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'experiment-listings' ) {
					$experiment_listings_setting_menu_enable = "active";
				}
				if ( ! empty( $_GET['page'] ) && $_GET['page'] === WABT_PLUGIN_MENU_SLUG && empty( $_GET['tab'] ) ) {
					$experiment_listings_setting_menu_enable = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'add-experiment' ) {
					$add_experiment_setting_meny_enable = "active";
				}
				/*if (!empty($_GET['tab']) && $_GET['tab'] == 'experiment-results') {
					$experiment_results_setting_meny_enable = "active";
				}*/
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'get-started-dots-about-plugin-settings' ) {
					$about_plugin_setting_menu_enable = "active";
					$about_plugin_get_started         = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'dotstore_introduction' ) {
					$about_plugin_setting_menu_enable = "active";
					$about_plugin_quick_info          = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'dots-store-plugin-settings' ) {
					$dotstore_setting_menu_enable = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'dots-premium-plugin-settings' ) {
					$dotpremium_setting_menu_enable = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'dots-contact-supports-store-plugin-settings' ) {
					$dotstore_setting_menu_enable = "active";
				}
				if ( ! empty( $_GET['tab'] ) && $_GET['tab'] === 'get-started-dots-about-plugin-settings' ) {
					$get_started_about_plugin_setting_menu_enable = "acitve";
				}
			}
			?>
			<div class="dots-menu-main">
				<nav>
					<ul>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $experiment_listings_setting_menu_enable ); ?>"
							   href="<?php echo site_url( 'wp-admin/admin.php?page=' . WABT_PLUGIN_MENU_SLUG . '&tab=experiment-listings' ); ?>">Experiment List</a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $add_experiment_setting_meny_enable ); ?>"
							   href="<?php echo site_url( 'wp-admin/admin.php?page=' . WABT_PLUGIN_MENU_SLUG . '&tab=add-experiment' ); ?>">Add Experiment</a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $about_plugin_setting_menu_enable ); ?>"
							   href="<?php echo site_url( 'wp-admin/admin.php?page=' . WABT_PLUGIN_MENU_SLUG . '&tab=get-started-dots-about-plugin-settings' ); ?>">About Plugin</a>
							<ul class="sub-menu">
								<li><a class="dotstore_plugin <?php echo $about_plugin_get_started; ?>"
								       href="<?php echo site_url( 'wp-admin/admin.php?page=' . WABT_PLUGIN_MENU_SLUG . '&tab=get-started-dots-about-plugin-settings' ); ?>">Getting
										Started</a></li>
								<li><a class="dotstore_plugin <?php echo $about_plugin_quick_info; ?>"
								       href="<?php echo site_url( 'wp-admin/admin.php?page=' . WABT_PLUGIN_MENU_SLUG . '&tab=dotstore_introduction' ); ?>">Quick info</a></li>
							</ul>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $dotstore_setting_menu_enable ); ?>" href="#"><?php _e( 'Dotstore' ); ?></a>
							<ul class="sub-menu">
								<li><a target="_blank" href="https://store.multidots.com/go/flatrate-pro-new-interface-woo-plugins"><?php _e( 'WooCommerce Plugins' ); ?></a></li>
								<li><a target="_blank" href="https://store.multidots.com/go/flatrate-pro-new-interface-wp-plugins"><?php _e( 'Wordpress Plugins' ); ?></a></li>
								<br>
								<li><a target="_blank" href="https://store.multidots.com/go/flatrate-pro-new-interface-wp-free-plugins"><?php _e( 'Free Plugins' ); ?></a></li>
								<li><a target="_blank" href="https://store.multidots.com/go/flatrate-pro-new-interface-free-theme"><?php _e( 'Free Themes' ); ?></a></li>
								<li><a target="_blank" href="https://store.multidots.com/go/flatrate-pro-new-interface-dotstore-support"><?php _e( 'Contact Support' ); ?></a></li>
							</ul>
						</li>
					</ul>
				</nav>
			</div>
		</header>