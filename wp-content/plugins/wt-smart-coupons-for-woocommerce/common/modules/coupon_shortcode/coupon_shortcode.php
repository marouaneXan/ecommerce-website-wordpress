<?php
/**
 * Coupon shortcode admin/public section.
 *
 * @link       
 * @since 1.3.7    
 *
 * @package  Wt_Smart_Coupon  
 */
if (!defined('ABSPATH')) {
    exit;
}

class WT_Smart_Coupon_Shortcode
{
    public $module_base='coupon_shortcode';
    public $module_id='';
    public static $module_id_static='';
    private static $instance = null;
    private $is_coupon_style_added=false; /* limit the coupon style to be added to the page multiple times if the page has more shortcodes */
    public function __construct()
    {
        $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
        self::$module_id_static=$this->module_id;
        add_shortcode('wt-smart-coupon', array($this, 'display_coupon'));
    }

    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new WT_Smart_Coupon_Shortcode();
        }
        return self::$instance;
    }

    /**
     * Display coupon by shortcode
     * @since 1.3.7
     */
    public function display_coupon($atts)
    {
        if( ! $atts['id'] ) {
            return __('Invalid coupon','wt-smart-coupons-for-woocommerce');
        }

        $post_status = get_post_status($atts['id']);
        if('publish'!= $post_status)
        {
            return __('Invalid coupon', 'wt-smart-coupons-for-woocommerce');
        }

        $post_type = get_post_type($atts['id']);
        if( 'shop_coupon' != $post_type ) {
            return __('Invalid coupon', 'wt-smart-coupons-for-woocommerce');
        }

        $coupon=get_post($atts['id']);
        $expire_text='';

        $validate_coupon_display=apply_filters('wt_sc_validate_shortcode_coupon_display', true, $atts['id']);       
        if($validate_coupon_display)
        {
            $current_user = wp_get_current_user(); 
            $user_id = ( isset( $current_user->ID ) ? (int) $current_user->ID : 0 );
            $email = $current_user->user_email;

            $display_invalid_coupons=apply_filters('wt_smart_coupon_display_invalid_coupons', true);
            
            $expired_coupon=array();
            if(!Wt_Smart_Coupon_Public::coupon_is_valid_for_displaying($coupon, $email, $user_id, $display_invalid_coupons, $expired_coupon, $expire_text))
            {
                return false;
            }
        }
        
        $coupon_obj= new WC_Coupon($atts['id']);
        $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data($coupon_obj);
        $coupon_data['display_on_page'] = 'by_shortcode';

        if(!$this->is_coupon_style_added)
        {
            $this->is_coupon_style_added=true;
            Wt_Smart_Coupon_Public::print_coupon_css(); /* print coupon CSS */
        }
        $coupon_html =  Wt_Smart_Coupon_Public::get_coupon_html($coupon, $coupon_data, $expire_text);

        return $coupon_html;
    }
}

WT_Smart_Coupon_Shortcode::get_instance();