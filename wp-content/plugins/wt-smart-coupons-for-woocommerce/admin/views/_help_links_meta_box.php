<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Help links metabox html
 * @since 1.3.5
 */

$help_links=array(
    array(
        'title'=>__("Create 'Seasonal Discount' offer", 'wt-smart-coupons-for-woocommerce'),
        'link'=>'https://www.webtoffee.com/how-to-offer-seasonal-discounts-in-woocommerce/?utm_source=Smart_coupons_free_plugin&utm_medium=SC_free_plugin_documentation&utm_campaign=Smart_Coupons_Documentation',
    ),
    array(
        'title'=>__('Auto apply coupons on checkout', 'wt-smart-coupons-for-woocommerce'),
        'link'=>'https://www.webtoffee.com/how-to-auto-apply-coupon-on-checkout-in-woocommerce/?utm_source=Smart_coupons_free_plugin&utm_medium=SC_free_plugin_documentation&utm_campaign=Smart_Coupons_Documentation',
    ),
    array(
        'title'=>__("Offer 'Giveaway'", 'wt-smart-coupons-for-woocommerce'),
        'link'=>'https://www.webtoffee.com/how-to-offer-giveaway-in-woocommerce/?utm_source=Smart_coupons_free_plugin&utm_medium=SC_free_plugin_documentation&utm_campaign=Smart_Coupons_Documentation',
    ),
    array(
        'title'=>__('Buy X Get Y discounts', 'wt-smart-coupons-for-woocommerce'),
        'link'=>'https://www.webtoffee.com/how-to-offer-quantity-based-discounts-in-woocommerce/?utm_source=Smart_coupons_free_plugin&utm_medium=SC_free_plugin_documentation&utm_campaign=Smart_Coupons_Documentation',
    ),
    array(
        'title'=>__('Offer discount based on Shipping/Payment/User role', 'wt-smart-coupons-for-woocommerce'),
        'link'=>'https://www.webtoffee.com/how-to-offer-discounts-based-on-shipping-payment-or-user-role/?utm_source=Smart_coupons_free_plugin&utm_medium=SC_free_plugin_documentation&utm_campaign=Smart_Coupons_Documentation',
    ),
);
?>
<style type="text/css">
.wt_sc_help_links{width:100%; }
.wt_sc_help_links li{ line-height:12px; box-sizing:border-box; width:100%; padding:3px 7px 3px 7px; margin-left:15px; list-style:square; line-height:16px; }
.wt_sc_help_link_more{ width:100%; text-align:right; margin-top:25px; }
.wt_sc_help_link_more .dashicons{ font-size:16px; line-height:20px; }
</style>
<p>
    <?php esc_html_e("Here are a few links that explains types of offers you can create.", 'wt-smart-coupons-for-woocommerce'); ?> 
</p>
<ul class="wt_sc_help_links">
    <?php
    foreach($help_links as $help_link)
    {
        ?>
        <li>
            <a href="<?php echo esc_attr($help_link['link']);?>" target="_blank">
                <?php esc_html_e($help_link['title'], 'wt-smart-coupons-for-woocommerce'); ?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
<div class="wt_sc_help_link_more">
    <?php esc_html_e("To know more, read ", 'wt-smart-coupons-for-woocommerce'); ?> 
    <a href="https://www.webtoffee.com/smart-coupons-for-woocommerce-userguide/" target="_blank">
        <?php esc_html_e("documentation", "wt-smart-coupons-for-woocommerce-pro"); ?>.
    </a>
</div>