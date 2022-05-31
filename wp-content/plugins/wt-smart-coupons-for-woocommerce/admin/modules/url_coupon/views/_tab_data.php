<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style type="text/css">
.nav-tab-active { background:#fff; margin-bottom:-2px; border-bottom:solid 1px #fff;}
.nav-tab-active:hover { background:#fff; border-bottom:solid 1px #fff;}
.wt_sc_coupon_url_preview{ padding:10px 0px; }
.wt_sc_coupon_url_structure li{ list-style:disc; margin-left:20px; }
.wt_sc_generate_coupon_url_box input{ border-color:#e6e9ec; }
</style>
<div class="wt_section_title" style="background:#fff; padding:10px 20px 30px 20px; box-sizing:border-box; box-shadow:0px 2px 2px #ccc;">
    <h2><?php _e('URL coupon','wt-smart-coupons-for-woocommerce') ?></h2>
    <p><?php _e('The plugin auto generates a unique URL for all the coupons created in your store. Visiting the URL associated with a coupon will automatically redirect the users to the cart page by applying the coupon. You can embed a URL in a button, and your customer can click the button to apply the coupon.','wt-smart-coupons-for-woocommerce') ?></p>
    <p>
        <b><?php _e('Prerequisite:','wt-smart-coupons-for-woocommerce'); ?> </b><?php _e('Ensure that you have created a coupon with the required configuration to use it as a URL coupon.','wt-smart-coupons-for-woocommerce') ?>
    </p>
    <p><b><?php _e('URL coupon format:','wt-smart-coupons-for-woocommerce') ?> {site_url}/?wt_coupon={coupon_code}</b> </p>
    
    <div style="background:#efefef; padding:5px 15px; color:#666">
        <p><?php _e('A sample URL coupon will be in the given format:','wt-smart-coupons-for-woocommerce'); ?>, https://www.webtoffee.com/cart/?wt_coupon=flat30</p>
        <div>
            <?php _e('In the above example,', 'wt-smart-coupons-for-woocommerce'); ?>
            <ul class="wt_sc_coupon_url_structure">
                <li>'https://www.webtoffee.com/cart/' <?php _e('corresponds to the site URL', 'wt-smart-coupons-for-woocommerce'); ?></li>
                <li><?php _e("'?wt_coupon' refers to the URL coupon key", 'wt-smart-coupons-for-woocommerce'); ?></li>
                <li><?php _e("'flat30' is the coupon code", 'wt-smart-coupons-for-woocommerce'); ?></li>
            </ul>
        </div>
    </div>

    <h2 style="margin-top:35px;">
        <?php _e('Get coupon URL','wt-smart-coupons-for-woocommerce'); ?>                  
    </h2>
    <p>
        <?php _e('Enter the coupon code of an already created coupon in the field provided. The URL will be automatically generated for you.','wt-smart-coupons-for-woocommerce'); ?>
    <p>
    <div class="wt_sc_generate_coupon_url_box">
        <input type="text" name="wt_sc_url_coupon_name" placeholder="<?php esc_attr_e("Type coupon code", 'wt-smart-coupons-for-woocommerce');?>">
    </div>
    <p>
        <?php _e('Visiting a URL coupon ensures that the underlying coupon is applied as per its respective configuration.','wt-smart-coupons-for-woocommerce'); ?> <br />
        <?php _e('e.g allow discount, giveaway free product whatever the case maybe.', 'wt-smart-coupons-for-woocommerce'); ?>
    </p>
</div>