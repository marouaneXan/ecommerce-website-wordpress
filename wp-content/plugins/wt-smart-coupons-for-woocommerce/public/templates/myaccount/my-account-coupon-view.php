<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

global $current_user, $woocommerce; 

$current_user = wp_get_current_user(); 
$user_id = ( isset( $current_user->ID ) ? (int) $current_user->ID : 0 );
$email = $current_user->user_email;
$coupons = Wt_Smart_Coupon_Public::get_available_coupons_for_user($current_user, 'my_account');
$printed_coupons=array(
    'available_coupons'=>array(),
    'used_coupons'=>array(),
    'expired_coupons'=>array(),
);
 
$smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();

Wt_Smart_Coupon_Public::print_coupon_css(); /* print coupon CSS */

/**
 *  @since 1.3.5 Deprecated  
 */
do_action_deprecated('wt_smart_coupon_before_my_acocount_coupons', array(), '1.3.5', 'wt_smart_coupon_before_my_account_coupons');

do_action('wt_smart_coupon_before_my_account_coupons');
if(!empty($coupons))
{
?>
    <div class="wt-mycoupons">
        <h4><?php _e('Available Coupons','wt-smart-coupons-for-woocommerce') ?></h4>
        <div class="wt_coupon_wrapper">
            <?php
            $expired_coupon = array();
            Wt_Smart_Coupon_Public::print_available_coupon_for_user($coupons, $printed_coupons, $expired_coupon, 'my_account');
            ?>
        </div>
    </div>
<?php
    
}else
{
    ?>
    <div class="wt-mycoupons">
        <?php  _e('Sorry you don\'t have any available coupons' ,'wt-smart-coupons-for-woocommerce'); ?>
    </div>
    <?php
}
//  Display used Coupons.
$coupon_used  = Wt_Smart_Coupon_Public::get_coupon_used_by_a_customer($current_user);
?>
    <div class="wt-used-coupons">
        <?php 
        $coupon_displayed = 0;
        if((!empty($coupon_used) && $smart_coupon_options['wt_display_used_coupons']) || ( !empty( $expired_coupon ) && $smart_coupon_options['wt_display_expired_coupons'] ) ) { ?>
            <h4>  <?php _e("Used / Expired Coupons","wt-smart-coupons-for-woocommerce"); ?></h4>
            <div class="wt_coupon_wrapper">
                <?php
                    if( ! empty( $coupon_used ) && $smart_coupon_options['wt_display_used_coupons'] ) {
                    foreach ($coupon_used as $coupon ) {
                        $coupon_post    = get_page_by_title( $coupon,'OBJECT','shop_coupon' );
                        if( !$coupon_post || $coupon_post->post_status != 'publish') {
                            continue;
                        }

                        $coupon_obj = new WC_Coupon( $coupon );

                        // Check is coupon restricted for the user roles.
                        $coupon_id    = $coupon_obj->get_id();
                        if( Wt_Smart_Coupon_Public::_wt_sc_check_valid_user_roles( $coupon_id ) === false ) {
                            continue;
                        }
                        
                        
                        $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                        $coupon_displayed++;
                        ?>
                            <div class="wt-single-coupon used-coupon">
                                <div class="wt-coupon-content">
                                    <div class="wt-coupon-amount">
                                        <span class="amount"> <?php echo $coupon_data['coupon_amount'].'</span><span> '.$coupon_data['coupon_type'] ; ?></span>
                                    </div>  
                                    <div class="wt-coupon-code"> <code> <?php echo $coupon ?></code></div>
                                </div>
                            </div>
                        <?php
                        $printed_coupons['used_coupons'][]=$coupon_obj;
                    }
                }
                if(!empty($expired_coupon) && $smart_coupon_options['wt_display_expired_coupons']) {
                    if(! empty( $coupon_used ))
                    {
                        $expired_coupon  = array_diff($expired_coupon , $coupon_used);
                    }
                    foreach ($expired_coupon as $coupon ) {
                        $coupon_post    = get_page_by_title( $coupon,'OBJECT','shop_coupon' );
                        if( !$coupon_post || $coupon_post->post_status != 'publish') {
                            continue;
                        }

                        $coupon_obj = new WC_Coupon( $coupon );
                        
                        $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                        $coupon_displayed++;
                        ?>
                            <div class="wt-single-coupon used-coupon expired">
                                <div class="wt-coupon-content">
                                    <div class="wt-coupon-amount">
                                        <span class="amount"> <?php echo $coupon_data['coupon_amount'].'</span><span> '.$coupon_data['coupon_type'] ; ?></span>
                                    </div>  
                                    <div class="wt-coupon-code"> <code> <?php echo $coupon ?></code></div>
                                    <div class="wt-coupon-expiry"><?php _e('Expired','wt-smart-coupons-for-woocommerce'); ?></div>
                                </div>
                            </div>
                        <?php
                        $printed_coupons['expired_coupons'][]=$coupon_obj;
                    }
                }
                ?>
            </div>
            <?php
            if( !$coupon_displayed ) {
                _e('Sorry no coupon to display' ,'wt-smart-coupons-for-woocommerce');
            }
        }
        ?>
    </div>
<?php
/**
 * @since 1.3.5
 */
do_action('wt_smart_coupon_after_my_account_coupons', $printed_coupons);
