<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 * @author     WebToffee <info@webtoffee.com>
 */
if( ! class_exists('Wt_Smart_Coupon_Admin') ) {
    class Wt_Smart_Coupon_Admin {

        private $plugin_name;
        private $version;

        /**
         * module list, Module folder and main file must be same as that of module name
         * Please check the `register_modules` method for more details
         * @since 1.3.5
         */
        public static $modules=array(
            'url_coupon',
            'limit_max_discount',
            'coupon_shortcode',
        );

        public static $existing_modules=array();
    
        public function __construct($plugin_name, $version) {
    
            $this->plugin_name = $plugin_name;
            $this->version = $version;
        }

        /**
         * Help links metabox html
         * @since 1.3.5
         */
        public function help_links_meta_box_html()
        {
            include WT_SMARTCOUPON_MAIN_PATH.'/admin/views/_help_links_meta_box.php';
        }


        /**
         * Help links metabox
         * @since 1.3.5
         */
        public function help_links_meta_box()
        {
            add_meta_box("wt-sc-help-links", __("Quick links", 'wt-smart-coupons-for-woocommerce'), array($this, "help_links_meta_box_html"), "shop_coupon", "side", "default", null);
        }

        /**
         * Upgrade to pro metabox html
         * @since 1.3.3
         */
        public function upgrade_to_pro_meta_box_html()
        {
           include WT_SMARTCOUPON_MAIN_PATH.'/admin/partials/_upgrade_to_pro_metabox.php';
        }

        /**
         * Upgrade to pro metabox
         * @since 1.3.3
         */
        public function upgrade_to_pro_meta_box()
        {
            add_meta_box("wt-sc-upgrade-to-pro", __("Smart Coupons for WooCommerce", 'wt-smart-coupons-for-woocommerce'), array($this, "upgrade_to_pro_meta_box_html"), "shop_coupon", "side", "low", null);
        }
    
    
        /**
         * Save Custom meata fields added in coupon 
         * @since 1.0.0
         */
        public function process_shop_coupon_meta($post_id, $post) {
            if (!current_user_can('manage_woocommerce')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce'));
            }
            if (!empty($_POST['_wt_sc_shipping_methods'])) {
                $wt_sc_shipping_methods = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_sc_shipping_methods'],'text_arr');
                update_post_meta($post_id, '_wt_sc_shipping_methods', implode(',', $wt_sc_shipping_methods ) );
            } else {
                update_post_meta($post_id, '_wt_sc_shipping_methods', '');
            }
    
            if (!empty($_POST['_wt_sc_payment_methods'])) {
                $wt_sc_payment_methods = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_sc_payment_methods'],'text_arr');
                update_post_meta($post_id, '_wt_sc_payment_methods', implode(',', $wt_sc_payment_methods ));
            } else {
                update_post_meta($post_id, '_wt_sc_payment_methods', '' );
            }
    
            if (!empty($_POST['_wt_sc_user_roles'])) {
                $wt_sc_user_roles = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_sc_user_roles'],'text_arr');
                update_post_meta($post_id, '_wt_sc_user_roles', implode(',',$wt_sc_user_roles ) );
            } else {
                update_post_meta($post_id, '_wt_sc_user_roles', '');
            }
    
            // Save Usage Restriction items.
    
            if( isset( $_POST['_wt_category_condition'] ) && !empty( $_POST['_wt_category_condition'] ) ) {
                $wt_category_condition = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_category_condition']);
                update_post_meta( $post_id, '_wt_category_condition', $wt_category_condition  );
            } else {
                update_post_meta($post_id, '_wt_category_condition', 'or');
            }
    
            if( isset( $_POST['_wt_product_condition'] ) && !empty( $_POST['_wt_product_condition'] ) ) {
                $wt_product_condition = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_product_condition']);
                update_post_meta( $post_id, '_wt_product_condition', $wt_product_condition  );
            } else {
                update_post_meta($post_id, '_wt_product_condition','or');
            }
    
            // Give away free Products.
    
            if( isset($_POST['_wt_free_product_ids']) && $_POST['_wt_free_product_ids']!='' ) {
                $wt_free_product_ids = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_free_product_ids'],'int_arr');
                update_post_meta($post_id, '_wt_free_product_ids', implode(',', $wt_free_product_ids ) );
            } else {
                update_post_meta($post_id, '_wt_free_product_ids', '');
            }
            // Matching products
            
            if( isset($_POST['_wt_min_matching_product_qty']) && $_POST['_wt_min_matching_product_qty']!='' ) {
                $wt_min_matching_product_qty = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_min_matching_product_qty'],'int');
                update_post_meta($post_id, '_wt_min_matching_product_qty', $wt_min_matching_product_qty );
            } else {
                update_post_meta($post_id, '_wt_min_matching_product_qty', '');
            }
    
            if( isset($_POST['_wt_max_matching_product_qty']) && $_POST['_wt_max_matching_product_qty']!='' ) {
                $wt_max_matching_product_qty = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_max_matching_product_qty'],'int');
                update_post_meta($post_id, '_wt_max_matching_product_qty', $wt_max_matching_product_qty );
            } else {
                update_post_meta($post_id, '_wt_max_matching_product_qty', '');
            }
    
            if( isset($_POST['_wt_min_matching_product_subtotal']) && $_POST['_wt_min_matching_product_subtotal']!='' ) {
                $wt_min_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_min_matching_product_subtotal'],'float');
                update_post_meta($post_id, '_wt_min_matching_product_subtotal', $wt_min_matching_product_subtotal );
            } else {
                update_post_meta($post_id, '_wt_min_matching_product_subtotal', '');
            }
    
            if( isset($_POST['_wt_max_matching_product_subtotal']) && $_POST['_wt_max_matching_product_subtotal']!='' ) {
                $wt_max_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_max_matching_product_subtotal'],'float');
                update_post_meta($post_id, '_wt_max_matching_product_subtotal', $wt_max_matching_product_subtotal );
            } else {
                update_post_meta($post_id, '_wt_max_matching_product_subtotal', '');
            }
    
            if( isset($_POST['_wt_valid_for_number']) ) {
                $wt_valid_for_number = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_valid_for_number']);
                if($wt_valid_for_number != '') {
                    update_post_meta($post_id, '_wt_valid_for_number', $wt_valid_for_number );
                }
                if ( isset( $_POST['_wt_valid_for_type'] ) && '' != $_POST['_wt_valid_for_type']  ) {
                    $wt_valid_for_type = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_valid_for_type']);
                } else {
                    $wt_valid_for_type = 'days';
                }
                update_post_meta($post_id, '_wt_valid_for_type', $wt_valid_for_type );
    
            }
    
            if(isset($_POST['_wc_make_coupon_available']) && $_POST['_wc_make_coupon_available']!='' )
            {              
                $_wc_make_coupon_available = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wc_make_coupon_available'], 'text_arr');
                update_post_meta($post_id, '_wc_make_coupon_available', implode(',', $_wc_make_coupon_available));
            }else
            {
                update_post_meta($post_id, '_wc_make_coupon_available',  '');
            }
    
            
        }
    
        /**
         * Enqueue Admin styles.
         * @since 1.0.0
         * @since 1.3.5 Styles limited to WC pages and Smart coupon settings pages
         */
        public function enqueue_styles()
        {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            
            if ( 
                (function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids())) || 
                (isset($_GET['page']) && ($_GET['page']==WT_SC_PLUGIN_NAME || strpos($_GET['page'], WT_SC_PLUGIN_NAME)===0))
            ) 
            {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wt-smart-coupon-admin.css', array(), $this->version, 'all');
                wp_enqueue_style( 'wp-color-picker' );
            }
        }
        
        /**
         * Enqueue Admin Scripts.
         * @since 1.0.0
         * @since 1.3.5 Scripts limited to WC pages and Smart coupon settings pages
         */
        public function enqueue_scripts()
        {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            
            if ( 
                (function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids())) || 
                (isset($_GET['page']) && ($_GET['page']==WT_SC_PLUGIN_NAME || strpos($_GET['page'], WT_SC_PLUGIN_NAME)===0))
            ) 
            {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wt-smart-coupon-admin.js', array('jquery','wp-color-picker'), $this->version, false);               
                
                $script_parameters=array(
                    'msgs'=>array(
                        'copied'=>__("Copied to clipboard", 'wt-smart-coupons-for-woocommerce'),
                    ),
                    'ajaxurl'=>admin_url( 'admin-ajax.php' ),
                );
                wp_localize_script($this->plugin_name,'WTSmartCouponOBJ', $script_parameters );
            }
    
        }
    
        /**
         * Add tabs to the coupon option page.
         * @since 1.0.0
         */
        public function admin_coupon_options_tabs($tabs) {
    
            $tabs['wt_coupon_checkout_options'] = array(
                'label' => __('Checkout options', 'wt-smart-coupons-for-woocommerce'),
                'target' => 'webtoffee_coupondata_checkout1',
                'class' => 'webtoffee_coupondata_checkout1',
            );
    
            $tabs['wt_give_away_free_product'] = array(
                'label'  => __( 'Give away products', 'wt-smart-coupons-for-woocommerce' ),
                'target' => 'wt_give_away_free_products',
                'class'  => '',
            );
    
            return $tabs;
        }
    
        /**
         * wt_coupon_checkout_options Page content
         * @since 1.0.0
         */
        public function admin_coupon_options_panels() {
    
            global $thepostid, $post;
            $thepostid = empty($thepostid) ? $post->ID : $thepostid;
            ?>
            <div id="webtoffee_coupondata_checkout1" class="panel woocommerce_options_panel">
            <?php
            do_action('webtoffee_coupon_metabox_checkout', $thepostid, $post);
            ?>
            </div>
    
            <?php
        }
    
        /**
         * Checkout tab form elements
         * @since 1.0.0
         */
        public function admin_coupon_metabox_checkout2($thepostid, $post) {
    
    
            $wc_help_icon_uri = WC()->plugin_url() . "/assets/images/help.png";
    
            $coupon_shipping_method_id_s = get_post_meta($thepostid, '_wt_sc_shipping_methods',true);
            if( '' !=  $coupon_shipping_method_id_s &&  !is_array( $coupon_shipping_method_id_s ) ) {
                $coupon_shipping_method_id_s = explode(',',$coupon_shipping_method_id_s);
            }
    
            // $coupon_shipping_method_ids = isset($coupon_shipping_method_id_s[0]) ? $coupon_shipping_method_id_s[0] : array();
            ?>
    
            <!-- Shipping methods -->
            <p class="form-field">
                <label for="_wt_sc_shipping_methods"><?php _e('Shipping methods', 'wt-smart-coupons-for-woocommerce'); ?></label>
                <select id="_wt_sc_shipping_methods" name="_wt_sc_shipping_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any shipping method', 'wt-smart-coupons-for-woocommerce'); ?>">
                    <?php
                    $shipping_methods = WC()->shipping->load_shipping_methods();
    
                    if (!empty($shipping_methods)) {
    
                        foreach ($shipping_methods as $shipping_method) {
    
                            if ( !empty( $coupon_shipping_method_id_s ) && in_array($shipping_method->id, $coupon_shipping_method_id_s)) {
                                echo '<option value="' . esc_attr($shipping_method->id) . '" selected>' . esc_html($shipping_method->method_title) . '</option>';
                            } else {
                                echo '<option value="' . esc_attr($shipping_method->id) . '">' . esc_html($shipping_method->method_title) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
               <?php echo wc_help_tip( __('Coupon will be applicable if any of these shipping methods are selected.', 'wt-smart-coupons-for-woocommerce') ); ?>
    
            </p>
    
            <!-- Payment methods -->
            <p class="form-field"><label for="_wt_sc_payment_methods"><?php _e('Payment methods', 'wt-smart-coupons-for-woocommerce'); ?></label>
    
                <select id="webtoffee_payment_methods" name="_wt_sc_payment_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any payment method', 'wt-smart-coupons-for-woocommerce'); ?>">
                    <?php
                    $coupon_payment_method_id_s = get_post_meta($thepostid, '_wt_sc_payment_methods',true);
                    if( '' !=  $coupon_payment_method_id_s && !is_array( $coupon_payment_method_id_s ) ) {
                        $coupon_payment_method_id_s = explode(',',$coupon_payment_method_id_s);
                    }
                    // $coupon_payment_method_ids = isset($coupon_payment_method_id_s[0]) ? $coupon_payment_method_id_s[0] : array();
    
                    $payment_methods = WC()->payment_gateways->payment_gateways();
    
                    if (!empty($payment_methods)) {
    
                        foreach ($payment_methods as $payment_method) {
    
                            if ('yes' === $payment_method->enabled) {
                                if ( !empty( $coupon_payment_method_id_s ) && in_array($payment_method->id, $coupon_payment_method_id_s)) {
                                    echo '<option value="' . esc_attr($payment_method->id) . '" selected>' . esc_html($payment_method->title) . '</option>';
                                } else {
                                    echo '<option value="' . esc_attr($payment_method->id) . '">' . esc_html($payment_method->title) . '</option>';
                                }
                            }
                        }
                    }
                    ?>
                </select>
                <?php echo wc_help_tip( __('Coupon will be applicable if any of these payment methods are selected.', 'wt-smart-coupons-for-woocommerce') ); ?>
            </p>
    
    
            <p class="form-field"><label for="_wt_sc_user_roles"><?php _e('Applicable Roles', 'wt-smart-coupons-for-woocommerce'); ?></label>
                <?php
                     $_wt_sc_user_roles_s = get_post_meta($thepostid, '_wt_sc_user_roles',true);
                     if( !is_array( $_wt_sc_user_roles_s ) &&  '' != $_wt_sc_user_roles_s  ) {
                         $_wt_sc_user_roles_s = explode(',',$_wt_sc_user_roles_s);
                     }
                     $available_roles = array_reverse(get_editable_roles());
    
                ?>
                <select id="_wt_sc_user_roles" name="_wt_sc_user_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any role', 'wt-smart-coupons-for-woocommerce'); ?>">
                    <?php
                    $available_roles = ( isset( $available_roles ) && is_array( $available_roles ) ) ? $available_roles : array();
                    foreach ($available_roles as $role_id => $role) {
                        $role_name = translate_user_role($role['name']);
    
                        echo '<option value="' . esc_attr($role_id) . '"'
                        . selected( !empty( $_wt_sc_user_roles_s ) && in_array($role_id, $_wt_sc_user_roles_s), true, false) . '>'
                        . esc_html($role_name) . '</option>';
                    }
                    ?>
                </select> 
                <?php echo wc_help_tip( __('Coupon will be applicable if customer belongs to any of these User Roles.', 'wt-smart-coupons-for-woocommerce') ); ?>
            </p>
    
            <?php
        }
        
    
        /**
         * Plugin action link.
         * @since 1.0.0
         */
        public function add_plugin_links_wt_smartcoupon($links) {
    
            $out=array(
                'settings' => '<a href="'.get_admin_url().'?page='.WT_SC_PLUGIN_NAME.'&tab=settings">'.__('Settings', 'wt-smart-coupons-for-woocommerce') .' </a>',
                'support' => '<a target="_blank" href="https://wordpress.org/support/plugin/wt-smart-coupons-for-woocommerce/">' . __('Support', 'wt-smart-coupons-for-woocommerce') . '</a>',
                'documentation' => '<a target="_blank" href="https://www.webtoffee.com/smart-coupons-for-woocommerce-userguide/">' . __('Documentation', 'wt-smart-coupons-for-woocommerce') . '</a>',
            );
            foreach($links as $link_key=>$link_html)
            {
                if($link_key==='deactivate')
                {
                    $out['deactivate'] = str_replace('<a', '<a class="smartcoupon-deactivate-link"', $link_html);
                }else
                {
                    $out[$link_key] = $link_html;
                }
            }
            $out['review'] = '<a target="_blank" href="https://wordpress.org/support/plugin/wt-smart-coupons-for-woocommerce/reviews/?rate=5#new-post">' . __('Review', 'wt-smart-coupons-for-woocommerce') . '</a>';
            $out['premium-upgrade'] = '<a target="_blank" href="https://www.webtoffee.com/product/smart-coupons-for-woocommerce/?utm_source=free_plugin_listing&utm_medium=smart_coupons_basic&utm_campaign=smart_coupons&utm_content='.WEBTOFFEE_SMARTCOUPON_VERSION.'" style="color: #3db634; font-weight: 500;">' . __('Premium Upgrade', 'wt-smart-coupons-for-woocommerce') . '</a>';

            return $out;
        }
        
        /**
         * Extra Options on usage restrictions.
         * @since 1.0.0
         */
        public function admin_coupon_usage_restrictions( $post ){
            $coupon    = new WC_Coupon( $post );
    
            $wt_product_condition = ( get_post_meta( $post, '_wt_product_condition', true ) )? get_post_meta( $post, '_wt_product_condition', true ) : 'or';
    
    
            woocommerce_wp_radio(
                array(
                    'id'      => '_wt_product_condition',
                    'value'     => $wt_product_condition,
                    'class'     => 'wt_product_restrictions',
                    'label'     => __('Product condition:', 'wt-smart-coupons-for-woocommerce'),
                    'options'   => array
                        (
                            'or' => __('Any.', 'wt-smart-coupons-for-woocommerce'),
                            'and' => __('All.', 'wt-smart-coupons-for-woocommerce')
                        ),
                    'description' => __( 'Coupon will be applied if any of the products from the below is available in the cart; all of option requires that the cart contains all of the listed products.', 'wt-smart-coupons-for-woocommerce' ),
                    'desc_tip'    => true,
                )
            );
    
    
            // Categories
    
            $wt_category_condition = ( get_post_meta( $post, '_wt_category_condition', true ) )? get_post_meta( $post, '_wt_category_condition', true ) : 'or' ;
     
            woocommerce_wp_radio(
                array(
                    'id'      => '_wt_category_condition',
                    'value'     => $wt_category_condition,
                    'class'     => 'wt_category_condition',
                    'label'     => __('Category condition:', 'wt-smart-coupons-for-woocommerce'),
                    'options'   => array
                        (
                            'or' => __('Any.', 'wt-smart-coupons-for-woocommerce'),
                            'and' => __('All.', 'wt-smart-coupons-for-woocommerce')
                        ),
    
                    'description' => __( 'Coupon will be applied if any of the categories from the below is available in the cart; all of option requires that the cart contains all of the listed categories.', 'wt-smart-coupons-for-woocommerce' ),
                    'desc_tip'    => true,
                )
            );
    
            echo '<h3>' . esc_html( __( 'Matching products', 'wt-smart-coupons-for-woocommerce' ) ) . "</h3>\n";
    
    
            // Minimum quantity of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_min_matching_product_qty',
                        'label'       => __( 'Minimum quantity of matching products', 'wt-smart-coupons-for-woocommerce' ),
                        'placeholder' => __( 'No minimum', 'wt-smart-coupons-for-woocommerce' ),
                        'description' => __( 'Minimum quantity of the products that match the given product or category restrictions. If no product or category restrictions are specified, the total number of products is used.', 'wt-smart-coupons-for-woocommerce' ),
                        'data_type'   => 'decimal',
                        'desc_tip'    => true,
                    )
                );
    
                // Maximum quantity of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_max_matching_product_qty',
                        'label'       => __( 'Maximum quantity of matching products', 'wt-smart-coupons-for-woocommerce' ),
                        'placeholder' => __( 'No maximum', 'wt-smart-coupons-for-woocommerce' ),
                        'description' => __( 'Maximum quantity of the products that match the given product or category restrictions. If no product or category restrictions are specified, the total number of products is used.', 'wt-smart-coupons-for-woocommerce' ),
                        'data_type'   => 'decimal',
                        'desc_tip'    => true,
                    )
                );
    
                // Minimum subtotal of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_min_matching_product_subtotal',
                        'label'       => __( 'Minimum subtotal of matching products', 'wt-smart-coupons-for-woocommerce' ),
                        'placeholder' => __( 'No minimum', 'wt-smart-coupons-for-woocommerce' ),
                        'description' => __( 'Minimum price subtotal of the products that match the given product or category restrictions.', 'wt-smart-coupons-for-woocommerce' ),
                        'data_type'   => 'price',
                        'desc_tip'    => true,
                    )
                );
    
                // Maximum subtotal of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_max_matching_product_subtotal',
                        'label'       => __( 'Maximum subtotal of matching products', 'wt-smart-coupons-for-woocommerce' ),
                        'placeholder' => __( 'No maximum', 'wt-smart-coupons-for-woocommerce' ),
                        'description' => __( 'Maximum price subtotal of the products that match the given product or category restrictions.', 'wt-smart-coupons-for-woocommerce' ),
                        'data_type'   => 'price',
                        'desc_tip'    => true,
                    )
                );
    
    
        }
    
    
        /**
         * Give away Product tab content
         * @since 1.0.0
         * @since 1.3.6 [Bug fix] Variable parent product excluded from search
         */
        public function give_away_free_product_tab_content($post) 
        {       
            ?>
            <div id="wt_give_away_free_products" class="panel woocommerce_options_panel">
                <?php
                    $free_products = get_post_meta( $post, '_wt_free_product_ids', true );
                    if(''!=$free_products &&  !is_array($free_products))
                    {
                        $free_products = explode(',', $free_products);
                    }
                    $free_products = is_array($free_products) ? $free_products : array() ;
                ?>
                <div class="options_group">
                    <div class="error_message wt_coupon_error"> 
                        <?php _e('Please choose a simple product','wt-smart-coupons-for-woocommerce');?>
                    </div>
                    <p class="form-field"><label><?php _e( 'Free Products', 'wt-smart-coupons-for-woocommerce' ); ?></label>
                        <select id="wt_give_away_product" class="wc-product-search" style="width: 50%;" name="_wt_free_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wt-smart-coupons-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations_without_parent"  data-allow_clear="true">
                            <?php  
                                foreach($free_products as $product_id)
                                {
                                    $product = wc_get_product( $product_id );
                                    if ( is_object( $product ) ) {
                                        echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                                    }
                                }
                            ?>
                        </select><?php echo wc_help_tip( __("A single quantity of the specified free product is added to the customer's cart when the coupon is applied. However, the corresponding tax and shipping charges are not exempted.", 'wt-smart-coupons-for-woocommerce' ) ); ?>
                    </p>
                </div>
            </div>   
            <?php
        }

        /**
         * Alter product search - exclude parent product from list
         * @since 1.3.6
         */
        public function wt_products_and_variations_no_parent()
        {
            check_ajax_referer('search-products', 'security');
            if(!current_user_can('manage_woocommerce')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce'));
            }
            add_filter('woocommerce_json_search_found_products', array($this, 'exclude_parent_product_from_search'), 10, 1);
            
            WC_AJAX::json_search_products('',true);        
        }

        /**
         * Exclude parent product from search
         * @since 1.3.6
         */
        public function exclude_parent_product_from_search($products)
        {
            foreach($products as $product_id =>$product)
            {
                $product_obj = wc_get_product($product_id);
                if($product_obj->has_child())
                {
                    unset($products[$product_id]);
                }
            }
            return $products;
        }


        /**
         * Add coupon visibility options in coupon general settings section
         * @since 1.3.7 Added option to show coupons in checkout page
         */
        function add_new_coupon_options( $coupon_id, $coupon )
        {
            $wc_make_coupon_available = get_post_meta($coupon_id , '_wc_make_coupon_available', true );
            $coupon_available_arr=($wc_make_coupon_available ? explode(',', $wc_make_coupon_available) : array()); 
            
            $coupon_availability_options = array(
                'my_account'    => __('My Account', 'wt-smart-coupons-for-woocommerce'),
                'checkout'      => __('Checkout', 'wt-smart-coupons-for-woocommerce'),
            );
            ?>
            <p class="form-field">
                <label for="_wc_make_coupon_available"><?php _e('Make coupon available in', 'wt-smart-coupons-for-woocommerce'); ?></label>
                <select id="_wc_make_coupon_available" name="_wc_make_coupon_available[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce'); ?>">
                    <?php
                    foreach($coupon_availability_options as $section => $name)
                    {
                        $selected =(in_array($section, $coupon_available_arr) ? 'selected = selected' : '');                       
                        echo '<option value="'.esc_attr($section).'" '.$selected.'>'.esc_html($name).'</option>';
                    }                  
                    ?>
                </select> 
                <?php echo wc_help_tip(__('Make coupon available in the selected pages', 'wt-smart-coupons-for-woocommerce')); ?>
            </p>
            <?php
        }

    
        /**
         * Ajax action function for checking product type
         * @since 1.0.0
         */
    
        function check_product_type(  ) {

            if ( check_ajax_referer( 'wt_smart_coupons_nonce', 'security' ) && current_user_can('manage_woocommerce')) {
                
                $product_id = isset( $_POST['product']) ? Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['product'],'int') : '';
                if( '' == $product_id  )  {
                    return false;
                }
                $product = wc_get_product( $product_id );
                echo $product->get_type( );
                die();
            }
        }
    
        /**
         * Smart coupon admin pages
         */
        public function wt_smart_coupon_admin_page() {
            add_submenu_page(
                '',
                'WT Smart Coupon - options',
                'WT Smart Coupon',
                'manage_options',
                WT_SC_PLUGIN_NAME,
                array($this,'wt_smart_coupon_admin_page_callback') ); 
        }
    
        /**
         * admin page callback
         * @since 1.0.0
         */
        public function wt_smart_coupon_admin_page_callback() {
            ?>
            <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Coupons', 'wt-smart-coupons-for-woocommerce');?></h1>
    
            <a href="<?php echo get_admin_url().'/post-new.php?post_type=shop_coupon'; ?>" class="page-title-action"><?php _e('Add coupon','wt-smart-coupons-for-woocommerce');  ?> </a>
            <hr class="wp-header-end">
            <?php
            $this->smart_coupon_admin_tabs();

            if( isset($_GET['tab']) ) {
                $tab = $_GET['tab'];
    
                $tab_items = array_keys( $this->smart_coupon_admin_tab_items() );
                if( !in_array($tab, $tab_items ) ) {
                    wp_safe_redirect( admin_url('edit.php?post_type=shop_coupon'), 303 );
                    exit;
                
                }
                
                do_action('wt_smart_coupon_tab_content_'.$tab);
            }
        }
    
        /**
         * Smart coupon admin tab items
         * @since 1.0.0
         * @since 1.3.5 Added coupon category tab item
         */
        function smart_coupon_admin_tab_items (){
            $tab_items = array(
                'coupons' => __('Coupon','wt-smart-coupons-for-woocommerce'),
                'coupon_category' => __('Coupon category','wt-smart-coupons-for-woocommerce'),
                'settings' => __('Layouts','wt-smart-coupons-for-woocommerce'),               
            );
    
            return apply_filters( 'wt_smart_coupon_admin_tab_items',  $tab_items );
        }
    
        /**
         * Smart coupon admin menus
         * @since 1.0.0
         * @since 1.2.4 Updated
         * @since 1.3.5 Added coupon category
         */
        function smart_coupon_admin_tabs() {
            $coupon_tabs = $this->smart_coupon_admin_tab_items();
            $active_tab = ( isset( $_GET['tab'] ) )? $_GET['tab'] : '';
            $actual_link = get_admin_url().'admin.php?page='.WT_SC_PLUGIN_NAME;
            $coupon_page = get_admin_url().'edit.php?post_type=shop_coupon';
            $coupon_category_page = get_admin_url().'edit-tags.php?post_type=shop_coupon&taxonomy=shop_coupon_cat';

            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
            do_action('wt_smart_coupon_before_admin_tabs',$active_tab);
    
            if( is_array( $coupon_tabs ) && !empty( $coupon_tabs ) ) {
                
                echo '<nav class="nav-tab-wrapper smart-coupon-tabs">';
                foreach( $coupon_tabs as $coupon_tab => $tab_name )
                {
                    $class = '';
                    if(($coupon_tab == $active_tab) 
                        || ($current_url == $coupon_page && $coupon_tab == 'coupons') 
                        || ($current_url == $coupon_category_page && $coupon_tab == 'coupon_category') )
                    {
                        $class = ' nav-tab-active';
                    }
    
                    $tab_link = $actual_link.'&tab='.$coupon_tab;
                    if( $coupon_tab == 'coupons' )
                    {
                        $tab_link = $coupon_page;

                    }elseif($coupon_tab == 'coupon_category')
                    {
                        $tab_link = $coupon_category_page;
                    }
    
                    if( $coupon_tab == 'licence') {
                        $plugin_name = 'wtsmartcoupon';
                        $status = get_option( $plugin_name.'_activation_status' );
                        
                        if( !$status ) {
                            $status_icon = '( <span class="dashicons dashicons-warning"></span> )';
                        } else {
                            $status_icon = '( <span class="dashicons dashicons-yes"></span> )';
                        }
                    } else {
                        $status_icon = '';
                    }
    
                    ?>
                    <a class="nav-tab <?php echo $class; ?>" href="<?php echo $tab_link; ?>">
                        <?php echo $tab_name. $status_icon ;  ?>
                    </a>
                    <?php
                }
                echo '</nav>';
            }
        }
    
    
        /**
         * Add smart Coupon tabs into Coupon page.
         * @since 1.0.0
         */
        public function smart_coupons_views_row( $views = null ) {
    
            global $typenow;
    
            if ( $typenow == 'shop_coupon' ) {
                do_action( 'smart_coupons_display_views' );
            }
    
            return $views;
    
        }
    
        /**
         * Get Smartcoupon Settings options
         * @since 1.0.1
         */
        public static function get_options() {
            $smart_coupon_options = apply_filters('wt_smart_coupo_default_options',array(
                'wt_active_coupon_bg_color'         => '#2890a8' ,
                'wt_active_coupon_border_color'     => '#ffffff' ,
    
                'wt_display_used_coupons'           => true,
                'wt_used_coupon_bg_color'           => '#eeeeee',
                'wt_used_coupon_border_color'       => '#000000',
    
                'wt_display_expired_coupons'        => true,
                'wt_expired_coupon_bg_color'        => '#f3dfdf',
                'wt_expired_coupon_border_color'    => '#eccaca',
    
            ));
            $smart_coupon_saved_option = get_option("wt_smart_coupon_options");
            
            if ( !empty($smart_coupon_saved_option) ) {
                foreach ( $smart_coupon_saved_option as $key => $option ) {
                    $smart_coupon_options[$key] = $option;
                }
            }
            update_option("wt_smart_coupon_options", $smart_coupon_options);
            return $smart_coupon_options;
        }
    
        /**
         * helper function for getting formatted price
         * @since 1.2.9
         */
        public static function get_formatted_price( $amount ) {
            $currency = get_woocommerce_currency_symbol();
            $currentcy_positon = get_option('woocommerce_currency_pos');
    
            switch( $currentcy_positon ) {
                case 'left' : 
                    return $currency.$amount;
                case 'left_space' : 
                    return $currency.' '.$amount;
                case 'right_space' : 
                    return $amount.' '.$currency;
                default  : 
                    return $amount.$currency;
            }
    
            
        }
    

        /**
         *  Registers modules    
         *  @since 1.3.5     
         */
        public function register_modules()
        { 
            Wt_Smart_Coupon::register_modules(self::$modules, 'wt_sc_admin_modules', plugin_dir_path( __FILE__ ), self::$existing_modules);  
        }

        /**
         *  Check module enabled    
         *  @since 1.3.5     
         */
        public static function module_exists($module)
        {
            return in_array($module, self::$existing_modules);
        }
    
    }
}
