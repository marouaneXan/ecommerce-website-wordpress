<div class="wt_smcpn_upgrade_to_pro_bottom_banner">
    <div class="wt_smcpn_upgrade_to_pro_bottom_banner_hd">
        <?php _e('Upgrade to Smart Coupons for WooCommerce Premium to get hold of advanced features.', 'wt-smart-coupons-for-woocommerce');?>
    </div>
    <a class="wt_smcpn_upgrade_to_pro_bottom_banner_btn" href="https://www.webtoffee.com/product/smart-coupons-for-woocommerce/?utm_source=free_plugin_comparison&utm_medium=smart_coupons_basic&utm_campaign=smart_coupons&utm_content=<?php echo WEBTOFFEE_SMARTCOUPON_VERSION;?>" target="_blank">
        <?php _e('UPGRADE TO PREMIUM', 'wt-smart-coupons-for-woocommerce'); ?>
    </a>
    <div class="wt_smcpn_upgrade_to_pro_bottom_banner_feature_list_main">
        <?php
            foreach($pro_upgarde_features as $pro_upgarde_feature)
            { 
                ?>
                <div class="wt_smcpn_upgrade_to_pro_bottom_banner_feature_list">
                    <?php echo $pro_upgarde_feature;?>
                </div>
                <?php
            }
        ?> 
    </div>
</div>