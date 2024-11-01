<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Ab_Testing
 * @subpackage Ab_Testing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Ab_Testing
 * @subpackage Wordpress_Ab_Testing/admin
 * @author     Multidots <inquiry@multidots.in>
 */
class Wordpress_Ab_Testing_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
        if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'ab-testing') {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wordpress-ab-testing-admin.css', array(), $this->version, 'all');
        }
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'ab-testing' && isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'get-started-dots-about-plugin-settings') {
            wp_enqueue_style($this->plugin_name . '-jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), $this->version, 'all');
        }
    }

	/**
	 * Register the JavaScript for the admin area.
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
        if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'ab-testing') {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wordpress-ab-testing-admin.js', array('jquery', 'jquery-ui-dialog'), $this->version, false);
        }

	}
         /**
        * Function For Redirect after activation of plugin
        *
        */
       public function welcome_ab_testing_screen_do_activation_redirect() {
           if (!get_transient('_welcome_screen_ab_testing_mode_activation_redirect_data')) {
               return;
           }

        // Delete the redirect transient
           delete_transient('_welcome_screen_ab_testing_mode_activation_redirect_data');

        // if activating from network, or bulk
           if (is_network_admin() || isset($_GET['activate-multi'])) {
               return;
           }

           wp_safe_redirect(add_query_arg(array('page' => 'ab-testing&tab=get-started-dots-about-plugin-settings'), admin_url('admin.php')));
       }

       
        /**
        * Function For add new menu in theme option 
        * 
        * 
        */
       public function dot_store_menu() {
           global $GLOBALS;
           if (empty($GLOBALS['admin_page_hooks']['dots_store'])) {
               add_menu_page(
                       'DotStore Plugins', __('DotStore Plugins',WABT_PLUGIN_SLUG), 'manage_option', 'dots_store', array($this, 'dot_store_menu_page'), plugin_dir_url(__FILE__) . 'images/menu-icon.png', 25
               );
           }
       }

       /**
        * Default dots store main page
        *
        */
       public function dot_store_menu_page() {

       }
  
        /**
         * AB Teting admin Menu
         */
        public function ab_testing_admin_menu() {
            
            $settings =  add_submenu_page("dots_store", WABT_PLUGIN_MENU_NAME, __(WABT_PLUGIN_MENU_NAME,WABT_PLUGIN_SLUG), "manage_options", WABT_PLUGIN_MENU_SLUG, array($this, "ab_testing_admin_menu_content"), "", 99);
            add_action( "load-{$settings}", array($this,'ab_testing_save_experiment_page') );
	}
        
        public function ab_testing_admin_menu_content(){
            global $wpdb,$post_type, $pagenow;
            include_once('partials/header/plugin-header.php');

            $active_tab = "experiment-listings";
            if (!empty($_GET["tab"])) {

            if ($_GET["tab"] == "experiment-listings") {
                $this->ab_testing_experiment_listing();
            }
            if ($_GET["tab"] == "add-experiment") {
                $this->ab_testing_add_experiment();
            }
            /*if ($_GET["tab"] == "experiment-results") {
                $this->ab_testing_experiment_results();
            }*/
            if ($_GET["tab"] == "win-variation") {
                $this->ab_testing_win_variation();
            }
            if ($_GET['tab'] === 'dots-contact-supports-store-plugin-settings') {
                $this->contact_supports_dotstore_plugin_settings();
            }
            if ($_GET['tab'] === 'get-started-dots-about-plugin-settings') {
                $this->get_started_dots_about_plugin_settings();
            }
            if ($_GET['tab'] === 'dotstore_introduction') {
                $this->ab_testing_plugin_introduction();
            }
            
        } else {
            $this->ab_testing_experiment_listing();
        }
        ?>

        <!-- end here !-->	
        <?php include_once('partials/header/plugin-sidebar.php'); ?>

        </div>

        </div>
        </body>			
        <html>
       <?php
        }        
    public function ab_testing_experiment_listing(){
        global $wpdb;
        $query = 'Select * from '.$wpdb->prefix.'ab_experiment';
        $get_exp = $wpdb->get_results($query);
        $i = 1; ?>
        <div class="wabt-table-main res-cl">
          <div class="product_header_title">
              <h2>
                <?php _e('Experiments List',$this->plugin_name)?> <a class="add-new-btn" href="<?php echo admin_url('admin.php?page=ab-testing&tab=add-experiment'); ?>"><?php _e('Add Experiment',WABT_PLUGIN_SLUG); ?></a>
                <a id="detete-multiple-experiment" class="detete-multiple-experiment button-primary"><?php _e('Delete (Selected)',''); ?></a>
            </h2>
          </div>  
            <table class="wabt-tableouter experiment_listing">
                <thead>
                      <tr class="wabt-head">
                        <th><input type="checkbox" name="check_all" class="check-all-experiment"></th>
                        <th><?php _e('Experiment Name',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Created Date',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Status',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Actions',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Results',WABT_PLUGIN_SLUG); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($get_exp)){
                            foreach($get_exp as $exp){
                                if ($exp->experiment_status==1) {
                                        $status_str = 'checked="checked"';
                                }else{
                                        $status_str = '';
                                } ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="multiple_delete_exp[]" class="multiple_delete_exp" value="<?php echo $exp->experiment_id; ?>">
                                    </td>
                                    <td><?php echo $exp->experiment_name; ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($exp->experiment_created_date)); ?></td>
                                    <td>
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="status_<?php echo $exp->experiment_id; ?>" class="experiment-status onoffswitch-checkbox" data-status="<?php echo $exp->experiment_status; ?>" data-id="<?php echo $exp->experiment_id; ?>" <?php echo $status_str; ?>>
                                            <label class="onoffswitch-label" for="status_<?php echo $exp->experiment_id; ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=ab-testing&tab=add-experiment&exp-id='.$exp->experiment_id); ?>" class="button button-primary button-large"><?php _e('Edit',WABT_PLUGIN_SLUG); ?></a>
                                        <a class="detete-single-experiment button button-primary button-large" id="<?php echo $exp->experiment_id;?>"><?php _e('Delete',WABT_PLUGIN_SLUG); ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=ab-testing&tab=win-variation&exp-id='.$exp->experiment_id); ?>" class="button button-primary button-large"><?php _e('Results',WABT_PLUGIN_SLUG); ?></a>
                                    </td>
                                  </tr> 
                           <?php $i++;
                          }
                        }else{ ?>
                            <tr>
                                <td scope="row" class="center" colspan="7"><?php _e('No Record Found',WABT_PLUGIN_SLUG); ?></td>
                            </tr>
                    <?php } ?>
                    </tbody>
            </table>
         </div>
    <?php }
    public function ab_testing_add_experiment(){
        global  $wpdb;
        $_COOKIE['insert_ab_testing_code'] = '';
        setcookie('insert_ab_testing_code', null, -1, '/');
        if(isset($_REQUEST['exp-id'])){
            $add_title = 'Edit';
            $exp_id = $_REQUEST['exp-id'];
            $expValue = $wpdb->get_results("SELECT experiment_name,experiment_url,target_url FROM ".$wpdb->prefix."ab_experiment WHERE experiment_id=".$exp_id);
            $experiment_name = $expValue[0]->experiment_name;
            $experiment_url = $expValue[0]->experiment_url;
            $target_url = $expValue[0]->target_url;
            $targeturlArray = explode(',',$target_url);
            $variationValue = $wpdb->get_results("SELECT variation_id,variation_name,variation_action,variation_percentage FROM ".$wpdb->prefix."ab_variations WHERE experiment_id=".$exp_id);
            $buttonType = '<input type="hidden" name="exp_id" value="'.$exp_id.'"><input type="submit" value="Save Experiment" name="editrecord" class="saverecord button button-primary button-large" id="updaterecord"><a href="'.admin_url('admin.php?page=ab-testing').'" class="cancel-button button button-primary button-large">Cancel</a>';        }else{
            $add_title = 'Add New';
            $experiment_name = '';
            $experiment_url = '';
            $targeturlArray = array();
            $variationValue = array();
            $target_url = '';
            $buttonType = '<input type="submit" value="Save Experiment" name="saverecord" class="saverecord button button-primary button-large" id="saverecord">';
        }
	?>
	<div class="wabt-table-main res-cl">
            <h2>
                <?php _e($add_title.' Experiment',WABT_PLUGIN_SLUG); ?>
                <a id="help-experiment" target="_blank" href="<?php echo admin_url('admin.php?page='.WABT_PLUGIN_MENU_SLUG.'&tab=get-started-dots-about-plugin-settings'); ?>" class="help-experiment button-primary"><?php _e('Help',''); ?></a>
            </h2>
            <form action="<?php echo admin_url( 'admin.php?page=ab-testing&tab=add-experiment' ); ?>" method="post">
            <table class="form-table table-outer add-experiment-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="experiment_name"><?php _e('Experiment Name:', WABT_PLUGIN_SLUG); ?><span class="required-star">*</span></label></th>
                    <td class="forminp">
                        <input type="text" name="experiment_name" class="experiment_name" value="<?php echo $experiment_name; ?>" required="1">
                    </td>

                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="experiment_url"><?php _e('Experiment Page', WABT_PLUGIN_SLUG); ?><span class="required-star">*</span></label></th>
                    <td class="forminp">
                        <?php $editable = ( isset($experiment_url) && $experiment_url != '' ) ? 'readonly=readonly' : ''; ?>
                        <input type="text" name="experiment_url" value="<?php echo $experiment_url; ?>" class="experiment_url" required="1" <?php echo $editable; ?>>
                        <?php echo ($experiment_url == '') ?  '<a class="clear_exp_url">Clear</a>' : ''; ?>
                        <ul class="url-ajax-search"></ul>
                    </td>

                </tr>
                 <?php  if(!empty($variationValue)){
                            $j=1;
                            foreach ($variationValue as $value){ ?>
                                <tr valign="top" class="variation-outer" id="variation<?php echo $j; ?>">
                                    <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Variation '.$j, WABT_PLUGIN_SLUG); ?><span class="required-star">*</span></label></th>
                                    <td class="forminp">
                                        <div class="variation-detail">
                                            <input type="hidden" name="variation[variation_id][]" value="<?php echo $value->variation_id; ?>">
                                            <div class="var-input"><input type="text" name="variation[variation_name][]" class="VariationName" value="<?php echo $value->variation_name; ?>" required="1" placeholder="Variation Name" ></div> 
                                            <div class="var-input"><input type="number" min="1" max="100" name="variation[percentage][]" value="<?php echo $value->variation_percentage; ?>" placeholder="Percentage" required="1" class="persantage_value"></div>
                                            <input type="button" name="" value="Edit" class="edit_variation">
                                            <?php if($value === end($variationValue)){ ?>
                                                    <input type="button" name="" class="add_variation edit-page-variation" value="+">
                                            <?php } elseif($value != end($variationValue)){ ?>
                                                <input type="button" name="" class="del_variation delete_cheaf" value="" data-id="<?php echo $value->variation_id;?>">
                                            <?php  } ?>
                                        </div>
                                        <div class="edit-variation">
                                            <textarea rows="10" cols="100" placeholder="Enter javascript" name="variation[action][]"><?php echo stripslashes($value->variation_action); ?></textarea>
                                        </div>
                                    </td>
                                </tr>
                        <?php $j++;
                            }
                             }else{ ?>
                                <tr valign="top" class="variation-outer" id="variation1" style="display: none;">
                                    <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Variation 1:', WABT_PLUGIN_SLUG); ?><span class="required-star">*</span></label></th>
                                    <td class="forminp">
                                        <div class="variation-detail">
                                            <div class="var-input"><input type="text" name="variation[variation_name][]" value="" class="VariationName" required="1" placeholder="Variation Name"> </div>
                                            <div class="var-input"><input type="number" min="1" max="100" name="variation[percentage][]" value="" placeholder="Percentage" required="1" class="persantage_value"></div>
                                            <input type="button" name="" value="Edit" class="edit_variation">
                                            <input type="button" name="" class="add_variation" value="+">
                                        </div>
                                        <div class="edit-variation">
                                                <textarea rows="10" cols="100" name="variation[action][]" placeholder="Enter javascript"></textarea>
                                        </div>
                                    </td>
                                </tr>
                        <?php } ?>
            </tbody>
            </table>
             <input type="hidden" name="targeting_url[]" value="<?php echo $target_url; ?>" class="targeting_url">
            <p class="submit"><?php echo $buttonType; ?> </p>
            </form>
        </div> 
 <?php }
    
    public function ab_testing_experiment_results(){
       global $wpdb;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            $post_id = $_REQUEST['id'];
            $wpdb->delete( $wpdb->prefix.'ab_experiment', array( 'experiment_id' => $post_id ), array( '%d' ) );
            wp_redirect(admin_url('admin.php?page=ab-testing'));
            exit;
        }
        $query = 'Select * from '.$wpdb->prefix.'ab_experiment';
        $get_exp = $wpdb->get_results($query);
        $i = 1; ?>
        <div class="wabt-table-main res-cl">
          <div class="product_header_title">
              <h2>
                <?php _e('Experiments List',$this->plugin_name)?> <a class="add-new-btn" href="<?php echo admin_url('admin.php?page=ab-testing&tab=add-experiment'); ?>"><?php _e('Add Experiment',WABT_PLUGIN_SLUG); ?></a>
                <a id="detete-multiple-experiment" class="detete-multiple-experiment button-primary"><?php _e('Delete (Selected)',''); ?></a>
            </h2>
          </div>  
            <table class="wabt-tableouter experiment_listing">
                <thead>
                      <tr class="wabt-head">
                        
                        <th><?php _e('Experiment Name',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Created Date',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Status',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Actions',WABT_PLUGIN_SLUG); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($get_exp)){
                            foreach($get_exp as $exp){
                                if ($exp->experiment_status==1) {
                                        $status_str = 'Running';
                                }else{
                                        $status_str = 'Paused';
                                } ?>
                                <tr>
                                   
                                    <td><?php echo $exp->experiment_name; ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($exp->experiment_created_date)); ?></td>
                                    <td><a id="status_<?php echo $exp->experiment_id; ?>" class="experiment-status" data-status="<?php echo $exp->experiment_status; ?>" data-id="<?php echo $exp->experiment_id; ?>"><?php echo $status_str; ?></a></td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=ab-testing&tab=win-variation&exp-id='.$exp->experiment_id); ?>" class="button button-primary button-large"><?php _e('Results',WABT_PLUGIN_SLUG); ?></a>
                                        
                                    </td>
                                  </tr> 
                           <?php $i++;
                          }
                        }else{ ?>
                            <tr>
                                <td scope="row" class="center" colspan="7"><?php _e('No Record Found',WABT_PLUGIN_SLUG); ?></td>
                            </tr>
                    <?php } ?>
                    </tbody>
            </table>
         </div> 
    <?php }
    public function ab_testing_win_variation(){ 
        
        global $wpdb;
        $experiment_id = $_REQUEST['exp-id'];
        $exp_query = 'Select * from '.$wpdb->prefix.'ab_experiment where experiment_id='.$experiment_id;
        $get_exp = $wpdb->get_results($exp_query);

        $total_count_query = 'Select sum(variation_count) total from '.$wpdb->prefix.'ab_variations where experiment_id='.$experiment_id;
        $get_total_count = $wpdb->get_results($total_count_query);
        if(isset($get_total_count) && !empty($get_total_count) && $get_total_count[0]->total != ''){
                $getTotalCount = $get_total_count[0]->total;
        }else{
                $getTotalCount = '0';
        }
        $query = 'Select * from '.$wpdb->prefix.'ab_variations where experiment_id='.$experiment_id;
        $get_var = $wpdb->get_results($query);

        $i = 1;
        $now =  date('Y-m-d H:i:s');
        $date1 = new DateTime(date('Y-m-d', strtotime($now)));
        $date2 = new DateTime(date('Y-m-d', strtotime($get_exp[0]->experiment_created_date)));
        $totalDays = $date1->diff($date2)->days; // 0 ?>
        <div class="wabt-table-main res-cl">
          <div class="wabt_win_header_title">
            <h2><?php _e($get_exp[0]->experiment_name,$this->plugin_name)?></h2>
            <div class="user-info">
                <div class="for-date">
                        <?php echo $totalDays.' days' ?>
                </div>
                <div class="totle-user">
                    <?php echo $getTotalCount; ?>
		</div>
            </div>
          </div>  
            <table class="wabt-tableouter win_experiment_listing">
                <thead>
                      <tr class="wabt-head">
                        <th><?php _e('Sr No',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Variation Name',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Visitors',WABT_PLUGIN_SLUG); ?></th>
                        <th><?php _e('Engagement',WABT_PLUGIN_SLUG); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($get_var)){
                            $i=1;
                            foreach($get_var as $var){
                                $getWinResultarray[] = ($var->variation_count != 0 && $var->variation_engagement != 0) ? ($var->variation_engagement * 100) / $var->variation_count : 0;
                            }
                            $getWinResultarraykey = max($getWinResultarray);
                            foreach($get_var as $var){
                                $getWinResult = ($var->variation_count != 0 && $var->variation_engagement != 0) ? ($var->variation_engagement * 100) / $var->variation_count : 0;
                                $var_engagement = $var->variation_engagement;
                                $checkPer = is_float($getWinResult) ? number_format($getWinResult, 2, '.', '') : $getWinResult;
                                $getWinPer = $getWinResult != 0 ? $checkPer : 0;
                                $win_class = '';
                                $wintitle = '';
                                if($getWinResultarraykey == $getWinResult && $var_engagement != 0) {
                                    $win_class = 'win_class';
                                    $wintitle = "title='win'";
                                } 
                                 ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $var->variation_name; ?></td>
                                    <td><?php echo $var->variation_count; ?></td>
                                    <td <?php echo $wintitle; ?> class="<?php echo $win_class; ?>"><?php echo $var->variation_engagement.' ('.$getWinPer.'%)'; ?></td>
                                  </tr> 
                           <?php 
                           $i++;
                          }
                        }else{ ?>
                            <tr>
                                <td scope="row" class="center" colspan="7"><?php _e('No Record Found',WABT_PLUGIN_SLUG); ?></td>
                            </tr>
                    <?php } ?>
                    </tbody>
            </table>
         </div> 
    <?php }
    public function contact_supports_dotstore_plugin_settings() {
        ?>
        <form id="cw_plugin_form_id"  > 
            <div class="under-table third-tab">
                <div class="set">
                    <h2><?php echo __("Contact Supports", WABT_PLUGIN_SLUG); ?></h2>
                </div>
                <table class="wabt-tableouter">
                    <tbody>  
                        <tr>
                    <iframe src="https://store.multidots.com/dotstore-support/#/home" width="100%" height="500px"></iframe>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
        <?php
    }
    
    /**
     * function for custom get started page 
     *
     */
   public function get_started_dots_about_plugin_settings() {

        $plugin_name = WABT_PLUGIN_NAME;
        $plugin_version = WABT_PLUGIN_VERSION;
       
        ?>			
        <div class="wabt-table-main res-cl">
            <h2><?php _e('Thanks For Installing '.WABT_PLUGIN_NAME.' Plugin',WABT_PLUGIN_SLUG); ?></h2>
            <table class="wabt-tableouter get-started-table">
                <tbody>
                    <tr>
                        <td class="fr-2">
                            <p class="block gettingstarted"><strong><?php _e('Getting Started',WABT_PLUGIN_SLUG); ?></strong></p>
                            <p class="block textgetting"></p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 1 :</strong> Click ‘Add Experiment’ to add new experiment',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_01.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 2 :</strong> Add Experiment Name. Enter page name and select the page on which you want to perform A/B testing.',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_02.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 3 :</strong> Once you add the experiment, variation panel will be visible, where you can add variation name and % value (which indicates how much % of your visitors should see this variation when they visit this experiment page).',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_03.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 4 :</strong> You can click ‘Edit’ which will open the page, where you can right-click on any section to hide any of the section for this variation. Click on ‘Save’ button in top right corner to save the changes. Click ‘x’ to close the page.',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_04.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 5 :</strong> In the textarea, you can add/edit the script which will run for this variation.',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_05.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 6 :</strong> Clicking on ‘+’ button will generate a new variation, which you can configure like the first one. You can generate as many variations as you need. Clicking on ‘X’ button will remove the variation.',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_06.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 7 :</strong> Click on ‘Save Experiment’ to save the experiment.',WABT_PLUGIN_SLUG); ?>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 8 :</strong> You can view all experiments in the list. By default the experiment will be ON (in Running stage). You can pause the experiment by flipping the switch to OFF. Click on ‘Results’ button to view results of a selected experiment.',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_08.png'; ?>"></span>
                            </p>
                            <p class="block textgetting">
                                <?php _e('<strong>Step 9 :</strong> Results will show no. of visitors and engagement, which is no. of visitors who have interacted with this page (clicked on the page)',WABT_PLUGIN_SLUG); ?>
                                <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo WABT_PLUGIN_URL . 'admin/images/Getting_Started_09.png'; ?>"></span>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
   
    /**
     * function for custom get started page 
     *
     */
    public function ab_testing_plugin_introduction() {
        $plugin_name = WABT_PLUGIN_NAME;
        $plugin_version = WABT_PLUGIN_VERSION;
        ?>			
        <div class="wabt-table-main res-cl">
            <h2>Quick info</h2>
            <table class="wabt-tableouter">
                <tbody>
                    <tr>
                        <td class="fr-1"><?php _e('Product Type',WABT_PLUGIN_SLUG); ?></td>
                        <td class="fr-2"><?php _e('WordPress Plugin',WABT_PLUGIN_SLUG); ?></td>
                    </tr>
                    <tr>
                        <td class="fr-1"><?php _e('Product Name',WABT_PLUGIN_SLUG); ?></td>
                        <td class="fr-2"><?php echo $plugin_name; ?></td>
                    </tr>
                    <tr>
                        <td class="fr-1"><?php _e('Installed Version',WABT_PLUGIN_SLUG); ?></td>
                        <td class="fr-2"><?php echo $plugin_version; ?></td>
                    </tr>
                    <tr>
                        <td class="fr-1"><?php _e('Localization',WABT_PLUGIN_SLUG); ?></td>
                        <td class="fr-2"><?php _e('English',WABT_PLUGIN_SLUG); ?></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <?php
    }
    
    
    public function ab_testing_save_experiment_page(){
        global $wpdb;
        if(isset($_POST['saverecord'])){
            $experiment_name = isset($_POST['experiment_name']) ? $_POST['experiment_name'] : '';
            $experiment_url = isset($_POST['experiment_url']) ? $_POST['experiment_url'] : '';
            $targeting_url = isset($_POST['targeting_url']) ? $_POST['targeting_url'] : array();
            $targetUrl = '';
            if(!empty($targeting_url)){
                    foreach ($targeting_url as $url){
                            $url = str_replace("http://","",$url);
                            $url = str_replace("www.","",$url);
                            $url = rtrim($url,"/");
                            $targetUrl .= $url.',';
                    }
            }
            $experiment_insert = $wpdb->insert( 
            $wpdb->prefix.'ab_experiment', 
                array( 
                    'experiment_name' => $experiment_name, 
                    'experiment_url' => $experiment_url, 
                    'target_url' => substr($targetUrl,0,-1),
                    'experiment_created_date' => current_time( 'mysql' ) ,
                    'experiment_modified_date' => current_time( 'mysql' ) ,
                    'experiment_status' => 1 
                )
            );
            $lastInsertId = $wpdb->insert_id;
            $variationArray = array();
            $variation = isset($_POST['variation']) ? $_POST['variation'] : array(); 
            $variation_name = $variation['variation_name'];
            $action = $variation['action'];
            $percentage =  $variation['percentage'];
            $size = count($variation_name);
            for($i = 0 ; $i < $size ; $i++){

                $variationArray[] = array(
                    'variation_name'   		=> $variation_name[$i], 
                    'variation_action' 		=> $action[$i],
                    'variation_percentage'  => $percentage[$i]
                );
            }
            if(!empty($variationArray)){
                    foreach ($variationArray as $var){

                            $variationPercentage = $var['variation_percentage'];
                            $variation_insert = $wpdb->insert( 
                            $wpdb->prefix.'ab_variations', 
                                    array( 
                                            'experiment_id' => $lastInsertId, 
                                            'variation_name' => $var['variation_name'], 
                                            'variation_action' => stripslashes($var['variation_action']), 
                                            'variation_percentage' => str_replace('%','',$variationPercentage),
                                            'variation_count' => 0 ,
                                            'variation_engagement' => 0,
                                            'variation_created_date' => current_time( 'mysql' ) ,
                                            'variation_modified_date' => current_time( 'mysql' ) ,
                                    )
                            );
                    }
            }
            wp_redirect( admin_url('admin.php?page=ab-testing' ));
                //exit;
        }elseif(isset($_POST['editrecord'])){
            $exp_id = $_POST['exp_id'];
            $experiment_name = isset($_POST['experiment_name']) ? $_POST['experiment_name'] : '';
            $experiment_url = isset($_POST['experiment_url']) ? $_POST['experiment_url'] : '';
            $targeting_url = isset($_POST['targeting_url']) ? $_POST['targeting_url'] : array();
            $targetUrl = '';
            if(!empty($targeting_url)){
                    foreach ($targeting_url as $url){
                            $url = str_replace("http://","",$url);
                            $url = str_replace("www.","",$url);
                            $url = rtrim($url,"/");
                            $targetUrl .= $url.',';
                    }
            }
            $experiment_update = $wpdb->update( 
            $wpdb->prefix . 'ab_experiment',
            array( 
                    'experiment_name' => $experiment_name,  
                    'experiment_url' => $experiment_url,   
                    'target_url' => substr($targetUrl,0,-1),   
                    'experiment_modified_date' => current_time( 'mysql' )    
                             ), 
                array( 'experiment_id' => $exp_id )
            );
            $lastInsertId = $wpdb->insert_id;
            $variationArray = array();
            $variation = isset($_POST['variation']) ? $_POST['variation'] : array();
            $variation_id = $variation['variation_id'];
            $variation_name = $variation['variation_name'];
            $action = $variation['action'];
            $percentage =  $variation['percentage'];
            $size = count($variation_name);
            for($i = 0 ; $i < $size ; $i++){

                $variationArray[] = array(
                    'variation_id'   		=> $variation_id[$i], 
                    'variation_name'   		=> $variation_name[$i], 
                    'variation_action' 		=> $action[$i],
                    'variation_percentage'  => $percentage[$i]
                );
            }
            if(!empty($variationArray)){
                    foreach ($variationArray as $var){
                            $variationPercentage = $var['variation_percentage'];
                            if($var['variation_id'] != ''){
                                    $experiment_update = $wpdb->update( 
                                    $wpdb->prefix.'ab_variations',
                                    array( 
                                                    'variation_name' => $var['variation_name'], 
                                                    'variation_action' => stripslashes($var['variation_action']), 
                                                    'variation_percentage' => str_replace('%','',$variationPercentage),
                                                    'variation_modified_date' => current_time( 'mysql' ) ,    
                                                    ), 
                                        array( 'experiment_id' => $exp_id , 'variation_id' => $var['variation_id'] )
                                    );
                            }else{
                                    $args = array( 
                                                    'experiment_id' => $exp_id, 
                                                    'variation_name' => $var['variation_name'], 
                                                    'variation_action' => $var['variation_action'], 
                                                    'variation_percentage' => $variationPercentage,
                                                    'variation_count' => 0 ,
                                                    'variation_engagement' => 0,
                                                    'variation_created_date' => current_time( 'mysql' ) ,
                                                    'variation_modified_date' => current_time( 'mysql' ) 
                                            );

                                    $variation_insert = $wpdb->insert( 
                                    $wpdb->prefix.'ab_variations', $args);
                            }
                    }
            }
            wp_redirect( admin_url('admin.php?page=ab-testing' ));
//			exit;
        }
    }
    public function ab_testing_single_delete_experiment(){
        global $wpdb;
        if (isset($_REQUEST['id'])) {
            $post_id = $_REQUEST['id'];
            $wpdb->delete( $wpdb->prefix.'ab_variations', array( 'experiment_id' => $post_id ), array( '%d' ) );
            $wpdb->delete( $wpdb->prefix.'ab_experiment', array( 'experiment_id' => $post_id ), array( '%d' ) );
            exit;
        }
    }

    public function ab_testing_multiple_delete_experiments() {
       global $wpdb;
       $result = 0;
       $allVals = isset($_POST['allVals']) ? $_POST['allVals'] : array();
       if (!empty($allVals)) {
           $allValStr = implode(',', $allVals);
           $wpdb->query( "DELETE FROM ".$wpdb->prefix."ab_experiment WHERE experiment_id IN(".$allValStr.")" );
           $result = 1;
       }
       echo $result;
       wp_die();
    }

    /**
     * ab testing change status running or pause
     */
    public function ab_testing_change_experiment_status(){
        global $wpdb;
        extract($_POST);
        $changeStatus = isset($status) && $status == 1 ? 0 : 1;
        if(isset($exId)) {
            $updateStatus = "UPDATE " . $wpdb->prefix . "ab_experiment SET experiment_status=" . $changeStatus . " WHERE experiment_id=" . $exId;
            $wpdb->query($updateStatus);
        }
        exit;
    }
    
    public function ab_testing_footer_script(){ ?>
            <script type="text/javascript">
                var ajaxloaderimg = '<?php echo plugin_dir_url( __FILE__ ).'images/ajax-loader.gif';?>';
                var inputAjaxloaderimg = '<?php echo plugin_dir_url( __FILE__ ).'images/input-ajax-loader.gif';?>';

            </script>
    <?php }
    public function ab_testing_post_name_search() {
        if(isset($_POST['term']) && !empty($_POST['term'])){
            $args = array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => 'date',
            's' =>$_POST['term'],
            'posts_per_page' =>10
            );
            $query = new WP_Query( $args );
            if($query->have_posts()){
            while ($query->have_posts()) {
            $query->the_post();
            ?>
            <li id="<?php the_ID(); ?>" data-url="<?php the_permalink(); ?>"><?php the_title(); ?></li>
            <?php
            }
            }else{
            ?>
            <li id="no-found">No Record Found...</li>
            <?php
            }
        }else{ ?>
            <li id="no-found">Please Enter Page Name</li>
        <?php }
        exit;
    }
    public function ab_testing_delete_variation_edit_page(){
        global $wpdb;
        if (isset($_REQUEST['data_id'])) {
            $post_id = $_REQUEST['data_id'];
            $wpdb->delete( $wpdb->prefix.'ab_variations', array( 'variation_id' => $post_id ), array( '%d' ) );
            exit;
        }
    }
}
