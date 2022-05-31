<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/partials
 */


 if( !function_exists('wt_smart_coupon_premium_features')) {
     function wt_smart_coupon_premium_features(){
         ?>
         <div class="wt_smart_coupon_pro_features">
            <div class="wt_smart_coupon_premium">
                <div class="wt_sc_upgrade_pro_main">
                    <img src="<?php echo WT_SMARTCOUPON_MAIN_URL;?>images/crown.svg" style="margin: 0 auto 20px auto; display:inline-block;">
                    <div class="wt_sc_upgrade_pro_main_hd"><?php _e( 'Get access to advanced features of Smart coupon.', 'wt-smart-coupons-for-woocommerce' ); ?></div>
                    <div style="float:left; width:100%;">
                        <div style="border-right:1px solid #E8E8E8;" class="wt_sc_upgrade_pro_half_box">
                            <div class="wt_sc_upgrade_pro_icon_box">
                                <img src="<?php echo WT_SMARTCOUPON_MAIN_URL;?>images/money-back-badge.svg" style="float:left;">
                            </div>
                            <div class="wt_sc_upgrade_pro_icon_info_box">
                                <?php _e( '30 Day Money Back Guarantee', 'wt-smart-coupons-for-woocommerce' ); ?>
                            </div>
                        </div>
                        <div  class="wt_sc_upgrade_pro_half_box">
                            <div class="wt_sc_upgrade_pro_icon_box">
                                <img src="<?php echo WT_SMARTCOUPON_MAIN_URL;?>images/support.svg" style="float:left;">
                            </div>
                            <div class="wt_sc_upgrade_pro_icon_info_box">
                                <?php _e( 'Fast and Superior Support', 'wt-smart-coupons-for-woocommerce' ); ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="https://www.webtoffee.com/product/smart-coupons-for-woocommerce/?utm_source=free_plugin_sidebar&utm_medium=smart_coupons_basic&utm_campaign=smart_coupons&utm_content=<?php echo WEBTOFFEE_SMARTCOUPON_VERSION;?>" target="_blank" class="button button-primary button-go-pro"><?php _e( 'Upgrade to Premium', 'wt-smart-coupons-for-woocommerce'); ?></a>
                    </div>
                </div>

                <div style="float:left; border-top: 1px solid #dfdfdf; padding: 35px 15px;">
                    <h3 style=" text-align: left; font-size: 16px; font-weight: 500; padding-left: 13px;margin: 0; "><?php _e('Premium features', 'wt-smart-coupons-for-woocommerce'); ?></h3>
                    <ul class="ticked-list">
                        <li><?php echo sprintf(__( '%sOffer store credits%s: Create and sell store credits of custom or pre-defined amounts.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sGift cards%s: Create attractive gift cards of any amount range by associating a store credit product.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sGive away products (one or more)%s: Configure coupons that give away selected product(s).','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sCoupons based on past purchases%s: Create and offer first order, next order, or any nth order discounts.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sRestrict coupons by country%s: Allow discounts for users from selected countries/locations.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sSign-up coupons%s: Create and offer sign-up discount coupons.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sCart abandonment coupons%s: Configure cart abandonment coupons to regain abandoned carts.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sCustomizable count-down sales banner%s: Easily create a count-down discount banner by choosing a template.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sBulk generate coupons%s: Generate and manage bulk coupons with add to store/email/ export to CSV options.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sImport-export coupons%s: Import and export coupons simultaneously by emailing them directly to the recipients.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sCoupon embeds%s: Shortcode for displaying coupons on any page.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                        <li><?php echo sprintf(__( '%sCombo coupons%s: Provision to use combo coupons for purchase.','wt-smart-coupons-for-woocommerce'), '<b>', '</b>'); ?></li>
                    </ul>
                </div>
                <center>
                    <a href="https://www.webtoffee.com/category/documentation/smart-coupons-for-woocommerce/" target="_blank" style="margin-bottom: 15px;" class="button button-doc-demo"><?php _e( 'Documentation', 'wt-smart-coupons-for-woocommerce'); ?></a>
                </center>

            </div>
            <div class="wt-review-widget"><?php
                echo '<div class=""><p><i>';
                echo sprintf( __('If you like the plugin please leave us a %s review','wt-smart-coupons-for-woocommerce'),'<a href="https://wordpress.org/support/plugin/wt-smart-coupons-for-woocommerce/reviews/?rate=5#new-post" target="_blank" class="wt-rating-link" data-reviewed="' . esc_attr__('Thanks for the review.', 'wf-woocommerce-packing-list') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>');
                echo '</i><p></div>';
                ?>
            </div>
        </div>
         <?php
     }
 }