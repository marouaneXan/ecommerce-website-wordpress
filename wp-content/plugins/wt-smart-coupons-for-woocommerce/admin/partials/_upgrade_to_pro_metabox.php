<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Upgrade to pro metabox html
 * @since 1.3.3
 */

?>
<style type="text/css">
    #wt-sc-upgrade-to-pro{ border:none; border-radius:15px; }
    #wt-sc-upgrade-to-pro .postbox-header{ border:none; border-radius:15px; height:80px; background:url(<?php echo esc_attr(WT_SMARTCOUPON_MAIN_URL);?>admin/images/plugin_icon.svg) no-repeat 18px 18px #fff; padding-left:65px; box-shadow:0px 4px 16px rgba(0, 0, 0, 0.11); margin-bottom:18px;}
    #wt-sc-upgrade-to-pro .postbox-header h2{ color:#413DB2; font-size:16px; }
    #wt-sc-upgrade-to-pro .postbox-header .handle-actions{ display:none; }
    #wt-sc-upgrade-to-pro .inside{ background:#fff;  padding:0px 20px; padding-bottom:30px; border-radius:15px;}
    #wt-sc-upgrade-to-pro .wt-sc-metabox-pro-features{ float: left; margin-top:10px; margin-bottom:20px; }
    #wt-sc-upgrade-to-pro .wt-sc-metabox-pro-features li{ padding:3px 0px; font-size:12px; color:#040071; font-weight:bold;}
    #wt-sc-upgrade-to-pro .wt-sc-metabox-pro-features .dashicons{ background:#fff; color:#6ABE45; border-radius:20px; margin-right:5px; }
    #wt-sc-upgrade-to-pro .wt-sc-metabox-upgrade-to-pro-btn{ color:#fff; display:inline-block; text-transform:uppercase; text-decoration:none; text-align:center; font-size:13px; font-weight:bold; line-height:38px; width:212px; height:38px; background:linear-gradient(92.12deg, #5408DF 1.79%, #8021E0 100.99%); border-radius:5px;}
    #wt-sc-upgrade-to-pro .wt-sc-metabox-upgrade-to-pro-btn  img{ border:none; margin-right:5px; }
    #wt-sc-upgrade-to-pro .wt-sc-metabox-upgrade-to-pro-btn:hover{ color:#fff; }
</style>
<ul class="wt-sc-metabox-pro-features">
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Store credits', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Free gifts', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Purchase history-based coupons', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Restrict coupons by country', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Give away products', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Cart abandonment coupons', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Sign-up coupons', 'wt-smart-coupons-for-woocommerce');?></li>
    <li><span class="dashicons dashicons-yes-alt"></span><?php _e('Count-down sales banner', 'wt-smart-coupons-for-woocommerce');?></li>
</ul>
<div style="text-align: center;">
    <a href="https://www.webtoffee.com/product/smart-coupons-for-woocommerce/?utm_source=free_plugin_marketing_sidebar&utm_medium=smart_coupons_basic&utm_campaign=smart_coupons&utm_content=<?php echo esc_attr(WEBTOFFEE_SMARTCOUPON_VERSION);?>" class="wt-sc-metabox-upgrade-to-pro-btn" target="_blank">
        <img src="<?php echo esc_attr(WT_SMARTCOUPON_MAIN_URL);?>admin/images/pro_icon.svg"><?php _e('Upgrade to premium', 'wt-smart-coupons-for-woocommerce');?>
    </a>
</div>