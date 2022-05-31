<?php
/**
 * Coupon shortcode admin section.
 *
 * @link       
 * @since 1.3.7     
 *
 * @package  Wt_Smart_Coupon  
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists ('WT_Smart_Coupon_Shortcode')) /* common module class not found so return */
{
    return;
}

class WT_Smart_Coupon_Shortcode_Admin extends WT_Smart_Coupon_Shortcode
{
    public $module_base='coupon_shortcode';
    public $module_id='';
    public static $module_id_static='';
    private static $instance = null;
    public function __construct()
    {
        $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
        self::$module_id_static=$this->module_id;

        add_filter('manage_edit-shop_coupon_columns', array($this, 'add_short_code_column' ), 10, 1);
        add_action('manage_shop_coupon_posts_custom_column', array($this, 'add_shortcode_column_content'), 10, 2);
    }

    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new WT_Smart_Coupon_Shortcode_Admin();
        }
        return self::$instance;
    }

    /**
     * Display shortcode column head in admin coupons table
     * @since 1.3.7
     */
    public function add_short_code_column($defaults) {
        $defaults['wt_short_code'] = __( 'Shortcode', 'wt-smart-coupons-for-woocommerce');

        return $defaults;
    }
    
    /**
     * Display shortcode column data in admin coupons table
     * @since 1.3.7
     */
    public function add_shortcode_column_content($column_name, $post_ID) {
        if ($column_name == 'wt_short_code')
        {
            echo '[wt-smart-coupon id='.$post_ID.']';
        }
    }
}
WT_Smart_Coupon_Shortcode_Admin::get_instance();