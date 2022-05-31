<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'WT_smart_Coupon_Help' ) ) {

    
	class WT_smart_Coupon_Help {
        function __construct() {
            add_filter('wt_smart_coupon_admin_tab_items',array($this,'add_help_tab'),10,1);
            add_action('wt_smart_coupon_tab_content_help',array($this,'help_content'));
        }

        function add_help_tab( $tab_items ){
            $tab_items['help'] = __('Help','wt-smart-coupons-for-woocommerce');

            return $tab_items;
        }

        function help_content()
        {
            ?>
            <style type="text/css">
            .wt_smart_coupon_pro_features{ margin-top:10px; }
            </style>
            <div id="message-help"></div>
            <div id="normal-sortables-2" class="meta-box-sortables ui-sortable">
                <div id="wt_smart_coupon_help" class=" woocommerce">
                    <div class="wt_smart_coupon_help_page_content">
                        <div class="wt_sub_tab_container">
                            <div  class="wt_sub_tab_content active" id="documentation" style="float:left; width:100%; background-color:#fff;">
                                <?php $admin_img_path = WT_SMARTCOUPON_MAIN_URL.'admin/images/'; ?>
                                <ul class="wt-smartcoupon-help-links">
                                    <li>
                                        <img src="<?php echo $admin_img_path;?>documentation.png">
                                        <h3><?php _e('Documentation', 'wt-smart-coupons-for-woocommerce'); ?></h3>
                                        <p><?php _e('Refer to our documentation to set up and get started', 'wt-smart-coupons-for-woocommerce'); ?></p>
                                        <a target="_blank" href="https://www.webtoffee.com/smart-coupons-for-woocommerce-userguide/" class="button button-primary">
                                            <?php _e('Documentation', 'wt-smart-coupons-for-woocommerce'); ?>        
                                        </a>
                                    </li>
                                    <li>
                                        <img src="<?php echo $admin_img_path;?>support.png">
                                        <h3><?php _e('Help and Support', 'wt-smart-coupons-for-woocommerce'); ?></h3>
                                        <p><?php _e('We would love to help you on any queries or issues.', 'wt-smart-coupons-for-woocommerce'); ?></p>
                                        <a target="_blank" href="https://www.webtoffee.com/support/" class="button button-primary">
                                            <?php _e('Contact Us', 'wt-smart-coupons-for-woocommerce'); ?>
                                        </a>
                                    </li>               
                                </ul>
                                <?php do_action('wt_smart_coupon_after_help_documentation_contents');  ?>
                            </div>
                            <?php do_action('wt_smart_coupon_help_page_contents'); ?>
                        </div>
                    </div>
                    <?php  wt_smart_coupon_premium_features(); ?>
                </div>
            </div>
            <?php
        }
    }
    $help = new WT_smart_Coupon_Help();
}