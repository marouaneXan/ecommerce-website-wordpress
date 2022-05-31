<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * The admin-Settings functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.1
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 * 
 */

if( ! class_exists ( 'WT_smart_Coupon_Settings' ) ) {

    
	class WT_smart_Coupon_Settings {

        public static $option_prefix;

        public function __construct() {
            self::$option_prefix = 'wt_smart_coupon';
            add_action('wt_smart_coupon_tab_content_settings',array($this,'display_settings_form'));
        }

        function display_settings_form() {
            ?> 
            <div id="message-settings"></div>
            <div id="normal-sortables-2" class="meta-box-sortables ui-sortable">
                <div id="wt_smart_coupon_settings" class=" woocommerce">

                <?php
                $updated  = false;
                if( isset( $_POST[ 'update_wt_smart_coupon_settings'] ) && current_user_can('manage_woocommerce')) {
                        check_admin_referer('wt_smart_coupons_settings');

                        $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();

                        if( isset( $_POST['wt_active_coupon_bg_color'] ) && !empty( $_POST['wt_active_coupon_bg_color'] ) ) {
                            $wt_active_coupon_bg_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_active_coupon_bg_color'],'hex');
                            $smart_coupon_options['wt_active_coupon_bg_color'] = $wt_active_coupon_bg_color;
                        }
                        if( isset( $_POST['wt_active_coupon_border_color'] ) && !empty( $_POST['wt_active_coupon_border_color'] ) ) {
                            $wt_active_coupon_border_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_active_coupon_border_color'],'hex');
                            $smart_coupon_options['wt_active_coupon_border_color'] = $wt_active_coupon_border_color;
                        }

                        if( isset( $_POST['wt_display_used_coupons'] ) ) {
                            $wt_display_used_coupons = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_display_used_coupons']);
                            
                            if( $wt_display_used_coupons == 'on' ) {
                                $smart_coupon_options['wt_display_used_coupons'] = true;
                            } else {
                                $smart_coupon_options['wt_display_used_coupons'] = false;
                            }
                        } else {
                            $smart_coupon_options['wt_display_used_coupons'] = false;
                        }
                        if( isset( $_POST['wt_used_coupon_bg_color'] ) && !empty( $_POST['wt_used_coupon_bg_color'] ) ) {
                            $wt_used_coupon_bg_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_used_coupon_bg_color'],'hex');
                            $smart_coupon_options['wt_used_coupon_bg_color'] = $wt_used_coupon_bg_color;
                        }
                        if( isset( $_POST['wt_used_coupon_border_color'] ) && !empty( $_POST['wt_used_coupon_border_color'] ) ) {
                            $wt_used_coupon_border_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_used_coupon_border_color'],'hex');
                            $smart_coupon_options['wt_used_coupon_border_color'] = $wt_used_coupon_border_color;
                        }
                        if( isset( $_POST['wt_display_expired_coupons'] ) ) {
                            $wt_display_expired_coupons = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_display_expired_coupons']);
                            
                            if( $wt_display_expired_coupons == 'on' ) {
                                $smart_coupon_options['wt_display_expired_coupons'] = true;
                            } else {
                                $smart_coupon_options['wt_display_expired_coupons'] = false;
                            }
                        }
                        else {
                            $smart_coupon_options['wt_display_expired_coupons'] = false;
                        }
                        if( isset( $_POST['wt_expired_coupon_bg_color'] ) && !empty( $_POST['wt_expired_coupon_bg_color'] ) ) {
                            $wt_expired_coupon_bg_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_expired_coupon_bg_color'],'hex');
                            $smart_coupon_options['wt_expired_coupon_bg_color'] = $wt_expired_coupon_bg_color;
                        }
                        if( isset( $_POST['wt_expired_coupon_border_color'] ) && !empty( $_POST['wt_expired_coupon_border_color'] ) ) {
                            $wt_expired_coupon_border_color = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['wt_expired_coupon_border_color'],'hex');
                            $smart_coupon_options['wt_expired_coupon_border_color'] = $wt_expired_coupon_border_color;
                        }

                        update_option("wt_smart_coupon_options", $smart_coupon_options);
                        $updated = true;
                        do_action('wt_smart_coupon_settings_updated');

                    }

                ?>

                <?php  if( $updated) { ?>
				
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e( 'Done! Updated Smart Coupon settings.', 'wt-smart-coupons-for-woocommerce' ); ?></p>
                    </div>
                <?php } ?>
                
                <div class="wt_smart_coupon_admin_option">
                  
                    <div class="wt_smart_coupon_admin_form">

                        <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" name="wt_smart_coupon_settings">
                            
                            <?php
                                $this->render_settings_fields();
                
                                wp_nonce_field('wt_smart_coupons_settings');
                            ?>
                        </form>
                    </div>


                    <?php wt_smart_coupon_premium_features(); ?>

            </div>
        </div>
            <?php
        }

        /**
         *  @since 1.0.0 Settings fields
         *  @since 1.3.5 UI updated, HTML corrected
         */
        public function render_settings_fields()
        {
            $admin_options = Wt_Smart_Coupon_Admin::get_options();
            ?>
                      <div class="form-section">
                            
                            <div class="wt_section_title">
                                <h2 style="margin-bottom:0px;"><?php _e('Coupon layouts','wt-smart-coupons-for-woocommerce') ?></h2>
                            </div>
                            <div class="coupon_styling_settings available_coupons">
                                <div class="section-sub-title">
                                    <h4><?php _e('Available coupons','wt-smart-coupons-for-woocommerce') ?></h4>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="form-item">
                                        <label> <?php _e('Background color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_active_coupon_bg_color" id="wt_active_coupon_bg_color" type="text" value="<?php echo $admin_options['wt_active_coupon_bg_color']; ?>" class="wt_colorpick" data-default-color="#2890a8"  />
                                        </div>
                                    </div>

                                    <div class="form-item">
                                        <label> <?php _e('Foreground color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_active_coupon_border_color" id="wt_active_coupon_border_color" type="text" value="<?php echo $admin_options['wt_active_coupon_border_color']; ?>" class="wt_colorpick" data-default-color="#ffffff"  />
                                        </div>
                                    </div>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="coupon_preview active_coupon_preview"></div>
                                </div>

                            </div> <!-- Available Coupons -->

                            <div class="coupon_styling_settings used_coupons">
                                <div class="section-sub-title">
                                    <h4><?php _e('Used coupons','wt-smart-coupons-for-woocommerce') ?></h4>
                                </div>
                                
                                <div class="form-item">
                                    <?php 
                                        $wt_display_used_coupons =  $admin_options['wt_display_used_coupons']; 
                                        $checked = '';
                                        if( $wt_display_used_coupons ) {
                                            $checked = 'checked = checked';
                                        }
                                    
                                    ?>
                                    <input type="checkbox" style="float:left; margin-top:3px; margin-right:10px;" id="wt_display_used_coupons" name="wt_display_used_coupons" <?php echo $checked; ?>  ><label> <?php _e('Display used coupons in My account?','wt-smart-coupons-for-woocommerce'); ?></label>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="form-item">
                                        <label> <?php _e('Background color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_used_coupon_bg_color" id="wt_used_coupon_bg_color" type="text" value="<?php echo $admin_options['wt_used_coupon_bg_color']; ?>" class="wt_colorpick" data-default-color="#eeeeee"  />
                                        </div>

                                    </div>

                                    <div class="form-item">
                                        <label> <?php _e('Foreground color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_used_coupon_border_color" id="wt_used_coupon_border_color" type="text" value="<?php echo $admin_options['wt_used_coupon_border_color']; ?>" class="wt_colorpick" data-default-color="#000000"  />
                                        </div>
                                    </div>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="coupon_preview used_coupon_preview"></div>
                                </div>

                            </div> <!-- Used Coupons -->


                            <div class="coupon_styling_settings expired_coupons">
                                <div class="section-sub-title">
                                    <h4><?php _e('Expired coupons','wt-smart-coupons-for-woocommerce') ?></h4>
                                </div>

                                <div class="form-item">
                                    <?php 
                                        $wt_display_expired_coupons =  $admin_options['wt_display_expired_coupons']; 
                                        $checked = '';
                                        if( $wt_display_expired_coupons ) {
                                            $checked = 'checked = checked';
                                        }
                                    
                                    ?>
                                    <input type="checkbox" style="float:left; margin-top:3px; margin-right:10px;" id="wt_display_expired_coupons" name="wt_display_expired_coupons" <?php echo $checked; ?> ><label> <?php _e('Display expired coupons in My account?','wt-smart-coupons-for-woocommerce'); ?></label>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="form-item">
                                        <label> <?php _e('Background color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_expired_coupon_bg_color" id="wt_expired_coupon_bg_color" type="text" value="<?php echo $admin_options['wt_expired_coupon_bg_color']; ?>" class="wt_colorpick" data-default-color="#f3dfdf"  />
                                        </div>

                                    </div>

                                    <div class="form-item">
                                        <label> <?php _e('Foreground color','wt-smart-coupons-for-woocommerce') ?> </label>
                                        <div class="form-element">
                                            <input name="wt_expired_coupon_border_color" id="wt_expired_coupon_border_color" type="text" value="<?php echo $admin_options['wt_expired_coupon_border_color']; ?>" class="wt_colorpick" data-default-color="#eccaca"  />
                                        </div>
                                    </div>
                                </div>
                                <div style="float:left; width:50%;">
                                    <div class="coupon_preview expired_coupon_preview"></div>
                                </div>

                            </div> <!-- Expired Coupons -->

                            <?php do_action('wt_smart_coupon_after_coupon_settings_form'); ?>

                        <div class="wt_form_submit">
                            <div class="form-submit" style="text-align:right;">
                                <button id="update_wt_smart_coupon_settings" name="update_wt_smart_coupon_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce'); ?></button>
                            </div>
                        </div>   
                </div>
            <?php
        }

    }
    $settings_obj = new WT_smart_Coupon_Settings();
}