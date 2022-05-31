<?php
/**
 * URL coupon
 *
 * @link       
 * @since 1.3.5    
 *
 * @package  Wt_Smart_Coupon  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wt_Smart_Coupon_Url_Coupon_Admin
{
    public $module_base='url_coupon';
    public $module_id='';
    public static $module_id_static='';
    private static $instance = null;
    public function __construct()
    {
        $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
        self::$module_id_static=$this->module_id;

        /*
        *   Init
        */
        add_action('admin_init', array($this, 'init'));

        /**
         *  To show coupon URL preview.
         */
        add_action("admin_footer", array($this, "url_coupon_preview"));
    }

    /**
     * Get Instance
     * @since 1.3.5
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new Wt_Smart_Coupon_Url_Coupon_Admin();
        }
        return self::$instance;
    }

    /**
    *   Initiate module
    */
    public function init()
    {
        /**
        *   Add settings tab
        */
        add_filter('wt_smart_coupon_admin_tab_items', array( $this, 'settings_tabhead'), 11);
        add_action('wt_smart_coupon_tab_content_'.$this->module_base, array($this, 'out_settings_form'));
        
    }

    /**
     * Shows sample URL for URL coupon in coupon edit/add page. If URL coupon pro not installed.
     * @since 1.3.5
     */
    public function url_coupon_preview()
    {
        if(defined('WT_URL_COUPONS_PRO_VERSION'))
        {
           return;
        }
        $screen = get_current_screen();
        if( ('post'===$screen->base && 'shop_coupon'===$screen->post_type) || (isset($_GET['page']) && WT_SC_PLUGIN_NAME==$_GET['page']) )
        {
            ?>
            <script type="text/javascript">
                function wt_sc_show_coupon_url_preview()
                {
                    var coupon_code_elm=jQuery('[name="post_title"]');
                    if(jQuery('[name="wt_sc_url_coupon_name"]').length>0)
                    {
                        coupon_code_elm=jQuery('[name="wt_sc_url_coupon_name"]');
                    }
                    
                    if(coupon_code_elm.length==0)
                    {
                        return;
                    }


                    var coupon_code=coupon_code_elm.val().trim();
                    if(coupon_code!="")
                    {
                        var cart_url='<?php echo esc_url(wc_get_cart_url()); ?>';
                        var coupon_url=cart_url+'?wt_coupon='+coupon_code.toLowerCase();
                        jQuery('.wt_sc_url_coupon_preview_box').show();
                        jQuery('.wt_sc_url_preview').html(coupon_url);
                    }else
                    {
                        jQuery('.wt_sc_url_coupon_preview_box').hide();
                    }
                }
                jQuery(document).ready(function(){
                    var url_coupon_preview_box=jQuery('.wt_sc_url_coupon_preview_box');
                    if(url_coupon_preview_box.length==0)
                    {
                        var beside_elm=jQuery('.generate-coupon-code');
                        if(jQuery('[name="wt_sc_url_coupon_name"]').length>0)
                        {
                            beside_elm=jQuery('[name="wt_sc_url_coupon_name"]');
                        }
                        beside_elm.after('<span class="wt_sc_url_coupon_preview_box"><span class="wt_sc_url_preview_label"><?php echo esc_html(__("Coupon URL:", "wt-smart-coupons-for-woocommerce-pro")); ?></span><span class="wt_sc_url_preview"></span><span class="wt_sc_copy_to_clipboard" data-target="wt_sc_url_preview"><?php echo esc_html(__("Copy to clipboard", "wt-smart-coupons-for-woocommerce-pro")); ?></span></span>');
                    }
                    jQuery('[name="post_title"], [name="wt_sc_url_coupon_name"]').on('keyup', function(){
                        wt_sc_show_coupon_url_preview();
                    });
                    jQuery('a.generate-coupon-code').on('click', function(){
                        setTimeout(function(){ wt_sc_show_coupon_url_preview(); }, 100);
                    });
                    wt_sc_show_coupon_url_preview();
                });
            </script>
            <style type="text/css">
               .wt_sc_url_coupon_preview_box{ padding-left:15px; margin-top:7px; display:inline-block; } 
               .wt_sc_url_coupon_preview_box .wt_sc_url_preview_label{ font-weight:bold; color:#666; display:inline-block; } 
               .wt_sc_url_coupon_preview_box .wt_sc_url_preview{ color:#333; display:inline-block; padding-left:4px; line-height:12px; text-decoration:underline; } 
            </style>
            <?php
        }
    }

    /**
     *  @since 1.3.5
     *  Tab head for plugin settings page
     **/
    public function settings_tabhead($arr)
    {
        if(defined('WT_URL_COUPONS_PRO_VERSION'))
        {
           return $arr;
        }
        $added=0;
        $out_arr=array();
        foreach($arr as $k=>$v)
        {
            $out_arr[$k]=$v;
            if($k=='settings' && $added==0)
            {               
                $out_arr[$this->module_base]=__('URL coupon', 'wt-smart-coupons-for-woocommerce');
                $added=1;
            }
        }
        if($added==0){
            $out_arr[$this->module_base]=__('URL coupon', 'wt-smart-coupons-for-woocommerce');
        }
        return $out_arr;
    }

    /**
     * @since 1.3.5
     * URL coupon tab content
     **/
    public function out_settings_form($args)
    {
        if(defined('WT_URL_COUPONS_PRO_VERSION'))
        {
           return;
        }
        $image_path=plugin_dir_url( __FILE__ ).'assets/images/';

        $view_params=array(
            'image_path'=>$image_path,
        );
        include plugin_dir_path( __FILE__ ).'views/_tab_data.php';
        include plugin_dir_path( __FILE__ ).'views/_upgrade_to_pro.php';
    }
}
Wt_Smart_Coupon_Url_Coupon_Admin::get_instance();