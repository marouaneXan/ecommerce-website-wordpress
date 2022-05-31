<?php
if (!defined('ABSPATH')) {
    exit;
}
$image_path=(isset($view_params['image_path']) ? $view_params['image_path'] : '');
?>
<style type="text/css">
.wt_sc_url_coupon_upgrade_to_pro{ margin-top:50px; padding:30px; background:#fff; border-radius:15px; box-shadow:0px 2px 2px #ccc;}
.wt_sc_url_coupon_upgrade_to_pro_head{ color:#413DB2; font-size:22px; line-height:56px; font-weight:bold; text-align:center; }
.wt_sc_url_coupon_upgrade_to_pro_head img{ float:left; border-radius:5px; margin-right:10px; }
.wt_sc_url_coupon_upgrade_to_pro table{margin-top:10px;}
.wt_sc_url_coupon_upgrade_to_pro table td{width:50%; vertical-align:top;}
.wt_sc_url_pro_features li{ padding:5px 0px; font-weight:500; float:left; }
.wt_sc_url_pro_features li b{ font-weight:900;}
.wt_sc_url_pro_features .wt_sc_icon_box{ float:left; width:30px; height:20px;}
.wt_sc_url_pro_features .dashicons{ background:#fff; color:#6ABE45; border-radius:20px; margin-right:5px; }
.wt-sc-url-upgrade-to-pro-btn{ color:#fff; display:inline-block; text-transform:uppercase; text-decoration:none; text-align:center; font-size:13px; font-weight:bold; line-height:38px; padding:4px 15px; background:linear-gradient(92.12deg, #5408DF 1.79%, #8021E0 100.99%); border-radius:5px;}
.wt-sc-url-upgrade-to-pro-btn  img{ border:none; margin-right:5px; }
.wt-sc-url-upgrade-to-pro-btn:hover{ color:#fff; }
.wt-sc-url-upgrade-to-pro-btn:active, .wt-sc-url-upgrade-to-pro-btn:focus{ color:#fff; }
</style>
<div class="wt_sc_url_coupon_upgrade_to_pro">
    <div class="wt_sc_url_coupon_upgrade_to_pro_head">
        <div style="display:inline-block;"><img src="<?php echo esc_attr($image_path);?>url_pro_icon.svg"><?php _e('Make the most out of URL coupons with URL coupons Pro', 'wt-smart-coupons-for-woocommerce');?></div>
    </div>
    <table>
        <tr>
           <td>
               <ul class="wt_sc_url_pro_features">
                   <li>
                        <span class="wt_sc_icon_box">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </span>
                        <b><?php _e('Custom URLs', 'wt-smart-coupons-for-woocommerce');?></b>: <?php _e('Generate customized coupon URLs (example:www.mystore.com/happybirthday)', 'wt-smart-coupons-for-woocommerce');?>
                    </li>
                   <li>
                        <span class="wt_sc_icon_box">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </span>
                        <b><?php _e('Set up a redirect page', 'wt-smart-coupons-for-woocommerce');?></b>: <?php _e('Configure the URL to redirect to either cart or checkout page when it is clicked.', 'wt-smart-coupons-for-woocommerce');?>
                    </li>
               </ul>
           </td> 
           <td>
               <ul class="wt_sc_url_pro_features">
                   <li>
                        <span class="wt_sc_icon_box">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </span>
                        <b><?php _e('Create QR code coupons', 'wt-smart-coupons-for-woocommerce');?></b>: <?php _e('Embed URL coupons in QRCodes to create QR code coupons.', 'wt-smart-coupons-for-woocommerce');?>
                    </li>
                   <li>
                        <span class="wt_sc_icon_box">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </span>
                        <b><?php _e('Automatically add products', 'wt-smart-coupons-for-woocommerce');?></b>: <?php _e('Assign a product to the URL to be automatically added to the customer’s cart upon activating it.', 'wt-smart-coupons-for-woocommerce');?>
                    </li>                 
               </ul>
           </td>
        </tr>
    </table>
    <div style="text-align:center;">
        <a href="https://www.webtoffee.com/product/url-coupons-for-woocommerce/?utm_source=free_plugin_CTA&utm_medium=smart_coupons_basic&utm_campaign=URL_Coupons&utm_content=<?php echo esc_attr(WEBTOFFEE_SMARTCOUPON_VERSION);?>" class="wt-sc-url-upgrade-to-pro-btn" target="_blank">
            <img src="<?php echo esc_attr($image_path);?>pro_icon.svg"><?php _e('Upgrade to URL coupons Pro', 'wt-smart-coupons-for-woocommerce');?>
        </a>
    </div>
</div>