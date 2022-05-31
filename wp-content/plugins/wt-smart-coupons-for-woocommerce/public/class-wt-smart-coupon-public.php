<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/public
 * @author     markhf <info@webtoffee.com>
 */
if( ! class_exists('Wt_Smart_Coupon_Public') ) {
    class Wt_Smart_Coupon_Public {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;
    
        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;
        
        private static $coupon_obj=null;
    
        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string    $plugin_name       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version) {
    
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            add_action( 'woocommerce_add_cart_item', array( $this,'wt_smartcoupon_recalculate_price') );
    
        }
        
    
        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {
    
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wt-smart-coupon-public.css', array(), $this->version, 'all');
        }
    
        /**
         * Register the JavaScript for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {
    
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wt-smart-coupon-public.js', array('jquery'), $this->version, false);
            wp_localize_script($this->plugin_name,'WTSmartCouponOBJ',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }
    
        /**
         * Filter Function updating woocommcerce coupon validation.
         * @param $valid
         * @param $coupon - Coupon code
         * @since 1.0.0
         */
        public function wt_woocommerce_coupon_is_valid($valid, $coupon) {
    
            if (!$valid) {
                return false;
            }
    
            $coupon_id                   = $coupon->get_id();
            $coupon_shipping_method_ids = get_post_meta($coupon_id, '_wt_sc_shipping_methods',true);
    
            if( ''!=$coupon_shipping_method_ids && ! is_array( $coupon_shipping_method_ids ) ) {
                $coupon_shipping_method_ids = explode(',',$coupon_shipping_method_ids);
            } else {
                $coupon_shipping_method_ids = array();
            }
            
            $coupon_payment_method_ids  = get_post_meta($coupon_id, '_wt_sc_payment_methods',true);
            if( ''!= $coupon_payment_method_ids && ! is_array( $coupon_payment_method_ids ) ) {
                $coupon_payment_method_ids = explode(',',$coupon_payment_method_ids);
            } else {
                $coupon_payment_method_ids = array();
            }
           
            $_wt_sc_user_roles         = get_post_meta($coupon_id, '_wt_sc_user_roles',true);
            if( ''!= $_wt_sc_user_roles && ! is_array( $_wt_sc_user_roles ) ) {
                $_wt_sc_user_roles = explode(',',$_wt_sc_user_roles);
            } else {
                $_wt_sc_user_roles = array();
            }
            
            // shipping method check
            if(sizeof($coupon_shipping_method_ids)>0)
            { 
                $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');
               
                /**
                 * @since 1.3.7
                 * [Bug fix] Shows a warning when `Hide shipping costs until an address is entered` option enabled.
                 */
                if($chosen_shipping_methods)
                {
                    $chosen_shipping = $chosen_shipping_methods[0];
                    $chosen_shipping = substr($chosen_shipping, 0, strpos($chosen_shipping, ":"));
                    if (!in_array($chosen_shipping, $coupon_shipping_method_ids)) {
                        $valid = false;
                    }
        
                    if (!$valid) {
                        throw new Exception( __( 'Sorry, this coupon is not applicable to selected shipping method', 'wt-smart-coupons-for-woocommerce' ), 109 );
                    } 
                }
                
            }
    
            // payment method check
            if (sizeof($coupon_payment_method_ids) > 0) {
    
                $chosen_payment_method = isset(WC()->session->chosen_payment_method) ? WC()->session->chosen_payment_method : array();
                
                if (!in_array($chosen_payment_method, $coupon_payment_method_ids)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable to selected Payment method', 'wt-smart-coupons-for-woocommerce' ), 109 );
                }
            }
    
            // user role check
            if (sizeof($_wt_sc_user_roles) > 0) {
    
                $user = wp_get_current_user();
                $user_roles = (array) $user->roles;
    
                if (!array_intersect($_wt_sc_user_roles, $user_roles)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable for your Role', 'wt-smart-coupons-for-woocommerce' ), 109 );
                }
            }
    
    
            // Usage restriction "AND" for products
            $wt_product_condition = get_post_meta($coupon_id,'_wt_product_condition',true);
            if( $wt_product_condition == 'and') {
                $valid = true;
                $coupon_products = $coupon->get_product_ids();
                if ( count( $coupon_products ) > 0 ) {
                    global $woocommerce;
                    $items = $woocommerce->cart->get_cart();
                    $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
                    $items_to_check = array();
                    foreach( $items as $item ) {
                        array_push($items_to_check,$item['product_id']);
                    }
                    foreach( $coupon_products as $coupon_product  ) {
                        if ( !in_array( $coupon_product, $items_to_check ) ) {
                            $valid = false;
                            break;
                        }
                    }
    
                    if ( ! $valid ) {
                        throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'wt-smart-coupons-for-woocommerce' ), 109 );
                    }
                }
            }
    
            // Usage restriction "AND" for Categories
            $wt_category_condition = get_post_meta($coupon_id,'_wt_category_condition',true);
            if( $wt_category_condition == 'and') {
                $valid = true;
                global $woocommerce;
                $coupon_categores = $coupon->get_product_categories() ;
                $coupon_categores = ( isset( $coupon_categores ) && is_array( $coupon_categores ) ) ? $coupon_categores : array();
                $items = $woocommerce->cart->get_cart();
                $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
                $items_to_check = array();
                foreach( $items as $item ) {
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );
                    $items_to_check = array_merge( $items_to_check,$product_cats );
                }
    
                foreach( $coupon_categores as $coupon_categry ) {
                    if ( !in_array( $coupon_categry, $items_to_check ) ) {
                        $valid = false;
                        break;
                    }
    
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'wt-smart-coupons-for-woocommerce' ), 109 );
                }
    
    
            }
    
            // Quantity of matching Products
            $wt_min_matching_product_qty = get_post_meta($coupon_id,'_wt_min_matching_product_qty',true);
            $wt_max_matching_product_qty = get_post_meta($coupon_id,'_wt_max_matching_product_qty',true);
    
            if( $wt_min_matching_product_qty > 0 ||  $wt_max_matching_product_qty > 0 ) {
                $quantity_of_matching_product = $this->get_quantity_of_matching_product( $coupon );
                if( $wt_min_matching_product_qty > 0 && $quantity_of_matching_product < $wt_min_matching_product_qty ) {
                    $valid = false;
                    throw new Exception(
                        sprintf( __( 'The minimum quantity of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce' ), $wt_min_matching_product_qty ),110
    
                    );
                }
                if( $wt_max_matching_product_qty >0 && $quantity_of_matching_product > $wt_max_matching_product_qty ) {            
                    $valid = false;                
                    throw new Exception(
                        sprintf( __( 'The maximum quantity of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce' ), $wt_max_matching_product_qty ),111
                    );
                }
            }
    
            // Subtotal of matching products
    
            $wt_min_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item( get_post_meta( $coupon_id,'_wt_min_matching_product_subtotal',true ) ,'float' );
            $wt_max_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item( get_post_meta( $coupon_id,'_wt_max_matching_product_subtotal',true ) ,'float' );
    
            if( $wt_min_matching_product_subtotal !== 0 ||  $wt_max_matching_product_subtotal !== 0 ) {
                $subtotal_of_matching_product = $this->get_sub_total_of_matching_products($coupon);
                if( $wt_min_matching_product_subtotal > 0 && $subtotal_of_matching_product < $wt_min_matching_product_subtotal ) {
                    $valid = false;
                    throw new Exception(
                        sprintf( __( 'The minimum subtotal of matching products for this coupon is %d.', 'wt-smart-coupons-for-woocommerce' ), $wt_min_matching_product_subtotal ),112
    
                    );
                }
                if( $wt_max_matching_product_subtotal >0 && $subtotal_of_matching_product > $wt_max_matching_product_subtotal ) {            
                    $valid = false;                
                    throw new Exception(
                        sprintf( __( 'The maximum subtotal of matching products for this coupon is %d.', 'wt-smart-coupons-for-woocommerce' ), $wt_max_matching_product_subtotal ),113
                    );
                }
            }
            
    
            return $valid;
        }
    
        public function get_sub_total_of_matching_products( $coupon ) {
            global $woocommerce;        
            $coupon_products =  $coupon->get_product_ids();
            $inc_tax         = wc_prices_include_tax();
            $coupon_categores = $coupon->get_product_categories() ;
            $items = $woocommerce->cart->get_cart();
            $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
            $total = 0;
            if( count( $coupon_products ) > 0 || count($coupon_categores) > 0  ) { // check with matching products by include condition.
                foreach( $items as $item ) {
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );

                    if( ( count( $coupon_products ) && in_array( $item['product_id'],$coupon_products ) ) ||  ( count($coupon_categores) && count( array_intersect($coupon_categores,$product_cats) ) > 0 ) ){
                       
                        if ( true === $inc_tax ) {
                            $total += $item['line_subtotal'] + $item['line_subtotal_tax'];
                           
                        } else {
                            $total += $item['line_subtotal'];
                        }
                    }
                  
                }
            } else {
                foreach( $items as $item ) {
                    if ( true === $inc_tax ) {
                        $total += $item['line_subtotal'] + $item['line_subtotal_tax'];
                       
                    } else {
                        $total += $item['line_subtotal'];
                    }
                }
            }
            return $total;
        }
    
        /**
         *  @since 1.3.2 Excluded free products from calculating total quantity of matching products
         */
        public function get_quantity_of_matching_product( $coupon )
        {
            global $woocommerce;        
            $coupon_products =  $coupon->get_product_ids();
            $coupon_products = ( isset( $coupon_products ) && is_array( $coupon_products ) ) ? $coupon_products : array();
            $coupon_categores = $coupon->get_product_categories() ;
            $coupon_categores = ( isset( $coupon_categores ) && is_array( $coupon_categores ) ) ? $coupon_categores : array();
            $items = $woocommerce->cart->get_cart();
            $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
            $qty = 0;

            if( count( $coupon_products ) > 0 || count($coupon_categores) > 0  ) { // check with matching products by include condition.
                foreach( $items as $item )
                {
                    if(isset($item['free_product']) && $item['free_product'] == "wt_give_away_product")
                    {
                        continue;
                    }
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );
                    $product_id = $item['product_id'];
                    if(isset($item['variation']) && count($item['variation']) > 0 ){
                        $product_id = $item['variation_id'];
                    }
                    if( ( count( $coupon_products ) && in_array( $product_id,$coupon_products ) ) || ( count($coupon_categores) && count( array_intersect($coupon_categores,$product_cats) ) > 0 ) ){
                        $qty += $item['quantity'];
                    }
                }
            } else {
                foreach( $items as $item )
                {
                    if(isset($item['free_product']) && $item['free_product'] == "wt_give_away_product")
                    {
                        continue;
                    }
                    $qty += $item['quantity'];
                }
            }
            return apply_filters('wt_smartcoupon_matching_product_quantity', $qty, $coupon );
        }
    
        /**
         * get free product applicable for a coupon
         */
    
         public function get_free_product_for_a_coupon( $coupon_code ) {
            $coupon = new WC_Coupon($coupon_code);
            $coupon_id      = $coupon->get_id();
            $free_products  = get_post_meta( $coupon_id, '_wt_free_product_ids', true );
    
            if( ''!= $free_products && ! is_array( $free_products ) ) {
                $free_products = explode(',',$free_products);
            } else {
                $free_products = array();
            }
    
            return $free_products;
         }

        /**
         * Get free product added message
         * @since 1.3.3
         */
        public function get_free_product_added_message($product_id, $coupon_code)
        {
            return apply_filters('wt_smart_coupon_free_product_added_message', __('Congrats you got a free gift!!', 'wt-smart-coupons-for-woocommerce'), $product_id, $coupon_code);
        }
    
        /**
         *  Action function for displaying give-away products on cart.
         *  @since 1.0.0
         *  @since 1.3.1  [Bug fix] Giveaway product is added repeatedly when logged in back to the site.
         */
        public function add_free_product_into_cart( $coupon_code ) {

            $free_products = $this->get_free_product_for_a_coupon( $coupon_code );
            if( empty( $free_products ) )  return;
            $first_product = wc_get_product( $free_products[0] );
            if( $first_product->is_type( 'simple' ) ) 
            {
                $free_product_id = $free_products[0];
                if ( sizeof( WC()->cart->get_cart() ) > 0 )
                {
                    /* check the free product already added for the current coupon */
                    $found      = false;
                    $cart_items = WC()->cart->get_cart();
                    $cart_items = ( isset( $cart_items ) && is_array( $cart_items ) ) ? $cart_items : array();
                    foreach ( $cart_items as $cart_item_key => $values )
                    {   
                        $_product = $values['data'];
                        if($_product->get_id() == $free_product_id /* product already added */
                            && isset( $values['free_product'] ) && $values['free_product'] == "wt_give_away_product"  /* product added as give away product */
                            && isset( $values['free_gift_coupon'] ) && $values['free_gift_coupon'] == $coupon_code ) /* product already added for the current coupon */
                        {
                            $found=true;
                        }
                    }

                    if(!$found) /* not already added */
                    {
                        $success_message=$this->get_free_product_added_message($free_product_id, $coupon_code);
                        if($success_message!="")
                        {
                            wc_add_notice( $success_message, 'success' );
                        }
                        
                        $quantity  =1;
                        $variation_id = '';
                        $variation = array();
                        $cart_item_data = array(
                            'free_product' => 'wt_give_away_product',
                            'free_gift_coupon' => $coupon_code,
                        );
                        WC()->cart->add_to_cart( $free_product_id, $quantity, $variation_id, $variation,$cart_item_data  );                     
                    }
                } 
            }   
        }

        /**
         *  Removes any free products from the cart if their related coupon is not present in the cart
         *  @since 1.3.1
         */
        public function check_any_free_products_without_coupon()
        {
            $cart = ((is_object(WC()) && isset(WC()->cart)) ? WC()->cart : null);
            if(is_object( $cart ) && is_callable(array($cart, 'is_empty')) && ! $cart->is_empty()) 
            {
                $coupons=$cart->get_applied_coupons();
                $cart_items = $cart->get_cart();
                $cart_items =((isset($cart_items) && is_array($cart_items)) ? $cart_items : array());            
                foreach($cart_items as $cart_item_key => $values )
                {                  
                    if(isset($values['free_product']) && $values['free_product']=="wt_give_away_product" && isset($values['free_gift_coupon']))
                    {
                        if(!in_array($values['free_gift_coupon'], $coupons)) /* coupon not found in the applied coupon list */
                        {
                            $cart->remove_cart_item($cart_item_key); /* remove the free item */
                        }
                    }
                }
            }                
        }

        /**
         *  Remove free products if non free products are not in the cart
         *  @since 1.3.1
         */
        public function woocommerce_cart_item_removed($cart_item_key, $cart)
        {
            $cart_items = $cart->get_cart();
            $cart_items = ( isset( $cart_items ) && is_array( $cart_items ) ) ? $cart_items : array();
            $free_products=array();
            foreach($cart_items as $cart_item_key => $values )
            {
                if(isset($values['free_product']) && $values['free_product'] == "wt_give_away_product")  /* product added as give away product */
                {
                    $free_products[]=$cart_item_key; 
                }
            }
            if(count($free_products)==count($cart_items)) /* only free products. So remove it */
            {
                foreach($free_products as $cart_item_key)
                {
                    $cart->remove_cart_item( $cart_item_key );
                }
            }
        }
    
        
        /**
         * Remove Free Product from cart.
         */    
        public function remove_free_product_into_cart( $coupon_code ) {
            
            global $woocommerce;
            $applied_coupons  = $woocommerce->cart->applied_coupons;
            if(isset($coupon_code) && !empty($coupon_code))
            {
                if(!in_array($coupon_code,$applied_coupons))
                {
                    $free_products = $this->get_free_product_for_a_coupon( $coupon_code );
                    if( empty( $free_products ) )  return;
                    $free_product_id = $free_products[0];
                    $cart_items = WC()->cart->get_cart();
                    $cart_items = ( isset( $cart_items ) && is_array( $cart_items ) ) ? $cart_items : array();
                    foreach ( $cart_items as $cart_item_key => $values ) {
    
                        $_product = $values['data'];
                        if ( $_product->get_id() == $free_product_id && isset( $values['free_product'] ) && $values['free_product'] == "wt_give_away_product" ){
                            WC()->cart->remove_cart_item( $cart_item_key );
                        }
                    }
                }
            }
        }
    
        /**
         * Check whether cart contains any give away products
         * @since 1.0.0
         */
        public function is_cart_contains_free_products() {
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
            $products_in_cart = array();
            foreach( $items as $item ) {
                if( $this->is_a_free_gift_item( $item ) ) {
                    return $item;
                }
            }
            return false;
        }
    
        // public function is_order_contains_free_products( $order ) {
        //     $order_items = $order->get_items();
        // }
    
        /**
         * Function for getting give away products based on coupon applied.
         * @since 1.0.0
         */
        public function get_free_products() {
            global $woocommerce;
            $applied_coupons  = $woocommerce->cart->applied_coupons;
            $applied_coupons = ( isset( $applied_coupons ) && is_array( $applied_coupons ) ) ? $applied_coupons : array();
            if( empty($applied_coupons)){
                return false;
            }
            
            $free_products =  array();
    
            foreach( $applied_coupons as $coupon ) {
               $coupon_id =  wc_get_coupon_id_by_code( $coupon) ;
               $products = get_post_meta( $coupon_id, '_wt_free_product_ids', true );
               if( ! is_array( $products ) ) {
                    $products = explode(',',$products);
                }
               
               $free_products  = array_merge($free_products, $products );
    
            }
            return $free_products ;
        }
    
        /**
         * function for checking a cart item is giveaway.
         * @since 1.0.0
         */
        public function is_a_free_gift_item( $cart_item ) {
            
            if( !empty( $this->get_free_products() ) && isset( $cart_item['free_product']) && $cart_item['free_product']=='wt_give_away_product') {
    
                return true;
            }
            return false;
        }
    
    
        /**
         * filter function for updating cart item price ( Displaying cart item price in cart and checkout page )
         * @param $price Price html.
         * @param $cart_item Cart item object
         * @since 1.0.0
         */
        public function add_custom_cart_item_total( $price,$cart_item ) {
     
            global $woocommerce;
            $free_products = $this->get_free_products();
            
            if ( empty($free_products) || ! in_array( $cart_item['product_id'],$free_products ) || ! ( $this->is_a_free_gift_item( $cart_item ) ) ) {
                return $price;
            }
            
            $_product = wc_get_product( $cart_item['product_id'] );
            $product_price = (float) $_product->get_price();
    
            $custom_price = $product_price * ( $cart_item['quantity'] - 1 );
    
            $return = '<del><span>'.Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $product_price * $cart_item['quantity'],2,'.','' ) ) ) .'</span></del> <span>'.'<span>'.Wt_Smart_Coupon_Admin::get_formatted_price( number_format((float) $custom_price,2,'.','' ) ).'</span>' ;
            return $return;
    
    
        }
    
       
        /**
         * Action function for displaying description for give away product on cart page
         * @Since 1.0.0
         */
        public function display_give_away_product_description( $cart_item ) {
            if( $this->is_a_free_gift_item( $cart_item ) ) {
    
                $free_gift_text = __('It\'s a free gift for you','wt-smart-coupons-for-woocommerce');
                echo '<p style="color:green;clear:both">'.$free_gift_text.'</p>';
            }
        }
    
        
    
        /**
         *  Calculate the Cart Total after reducing the free product price.
         * @since 1.0.0.
        */
    
         public function discounted_calculated_total( $total, $cart_object ){
            
            
            $gift_item = $this->is_cart_contains_free_products();
    
            if( $gift_item ) {
                $_product = wc_get_product( $gift_item['product_id'] );
                
                $discount = (float) $_product->get_price();
                $new_total = $total - $discount;
                return round( $new_total, $cart_object->dp );
            }
            return $total;
          
          }
    
          function woocommerce_calculate_totals( $cart ) {
    
            $gift_item = $this->is_cart_contains_free_products();
    
            if( $gift_item ) {
                $_product = wc_get_product( $gift_item['product_id'] );
                
                $discount = (float) $_product->get_price();
                $taxes = $cart->get_tax_totals(); 
    
                $rates = WC_Tax::get_rates();
                
                $tax_discount  = WC_Tax::calc_tax( $discount, $rates );
                $tax_discount = ( isset( $tax_discount ) && is_array( $tax_discount ) ) ? $tax_discount : array();
                foreach( $tax_discount as $tax_desc ) $discount+= $tax_desc;
    
            
                $subtotal = $cart->get_totals();
                $discount_total = $cart->get_discount_total( );
    
                $get_discount_tax =  $cart->get_discount_tax( );
    
                $cart->set_discount_total( $discount_total+$discount+10 );
    
                $cart->set_total_tax( $tax_discount );
            }
            
             return $cart;
        
        }
    
        /**
         * Add Free gift item price details into cart and checkout.
         * @since 1.0.0
         */
        public function add_give_away_product_discount() {
            $gift_item = $this->is_cart_contains_free_products();
    
            if( $gift_item) {
                $_product = wc_get_product( $gift_item['product_id'] );
    
                $discount = (float) $_product->get_price();
                ?>
                <tr class="woocommerce-give_away_product wt_give_away_product">
                    <th><?php _e( 'Free Gift Item', 'wt-smart-coupons-for-woocommerce' ); ?></th>
                    <td>-<?php echo Wt_Smart_Coupon_Admin::get_formatted_price( number_format((float) $discount,2,'.','' ) ) ?></td>
            
                </tr>
            
               <?php
            }
    
           
         }
    
        
        /**
         * Add Free Prodcut details on cart item list.
         * @since 1.0.0
        */
        function add_free_product_details_into_order( $item, $cart_item_key, $values, $order ) {
            if ( empty( $values['free_product'] ) ) {
                return;
            }
    
            $item->add_meta_data( __( 'free_product', 'wt-smart-coupons-for-woocommerce' ), '<p style="color:green">Its a free Product</p> ' );
        }
    
        /**
         * Display free product Discount Detail on order details.
         * @since 1.0.0
         */
        function woocommerce_get_order_item_totals( $total_rows, $tax_display  ) {
            $order = $tax_display;
            $order_items = $order->get_items();
            $order_items = ( isset( $order_items ) && is_array( $order_items ) ) ? $order_items : array();
            foreach( $order_items as $id => $order_item ) {
                $free_item = wc_get_order_item_meta($id,'free_product',true);
                if( !empty($free_item) ) {
                    $product = $order_item['product_id'];
                    
                    $_product  = wc_get_product( $order_item['product_id'] ) ;
                    $product_price = (float) $_product->get_price();
                    $quantity = $order_item['quantity'];
                    
                    $custom_price = $product_price * ( $quantity - 1 );
                    
                    $value = '<del><span>'.Wt_Smart_Coupon_Admin::get_formatted_price( number_format((float) $product_price,2,'.','' ) ).'</span></del> <span>'.Wt_Smart_Coupon_Admin::get_formatted_price ( number_format((float) $custom_price,2,'.','' ) ).'</span>' ;
    
                    $key = 'shipping';
                    $offset = array_search($key, array_keys($total_rows));
    
                    $total_rows = array_merge
                            (
                                array_slice($total_rows, 0, $offset),
                                array(
                                    'free_product' => array(
                                        'label' => __( 'Free Product:', 'wt-smart-coupons-for-woocommerce' ),
                                        'value' => $value
                                    )
                                ),
                                array_slice($total_rows, $offset, null)
                            );
                }
    
            }
            return $total_rows;
        }
    
        /**
         * Manage Item Meta on order page
         * @since 1.0.0
         */
        
        function  unset_free_product_order_item_meta_data( $formatted_meta, $item ) {
            
            $formatted_meta = ( isset( $formatted_meta ) && is_array( $formatted_meta ) ) ? $formatted_meta : array();
            foreach( $formatted_meta as $key => $meta ) {
                if( in_array( $meta->key, array('free_product') ) ) {
                    unset($formatted_meta[$key]);
                }
            }
            return $formatted_meta;
        }
    
        /**
         * Update cart item session fo giveaway product
         * @since 1.1.8
         */
        function update_cart_item_in_session( $session_data = array(), $values = array(), $key = '' ) {
            if( isset( $session_data['free_product'] ) &&  $session_data['free_product'] == 'wt_give_away_product'  ) {       
                $session_data    = $this->wt_smartcoupon_modify_free_product_price( $session_data);
            }
            return $session_data;
        }
    
        /**
         * Get formatted Meta values of a coupon.
         * @since 1.0.0
         */
        public static function get_coupon_meta_data( $coupon ) {
    
            $discount_types = wc_get_coupon_types();
            $coupon_data = array();
            switch( $coupon->get_discount_type() ) {
                case 'fixed_cart':
                    $coupon_data['coupon_type']     = __( 'Cart Discount', 'wt-smart-coupons-for-woocommerce' );
                    $coupon_data['coupon_amount']   = Wt_Smart_Coupon_Admin::get_formatted_price ( $coupon->get_amount() );
                    break;
    
                case 'fixed_product':
                    $coupon_data['coupon_type']     = __( 'Product Discount', 'wt-smart-coupons-for-woocommerce' );
                    $coupon_data['coupon_amount']   = Wt_Smart_Coupon_Admin::get_formatted_price ( $coupon->get_amount() );
                    break;
    
                case 'percent_product':
                    $coupon_data['coupon_type']     = __( 'Product Discount', 'wt-smart-coupons-for-woocommerce' );
                    $coupon_data['coupon_amount']   = $coupon->get_amount() . '%';
                    break;
    
                case 'percent':
                    $coupon_data['coupon_type'] = __( 'Cart Discount', 'wt-smart-coupons-for-woocommerce' );
                    $coupon_data['coupon_amount'] = $coupon->get_amount() . '%';
                    break;
    
                default:
                    $coupon_data['coupon_type'] = $discount_types[ $coupon->get_discount_type() ];
                    $coupon_data['coupon_amount'] = $coupon->get_amount();
                    break;
    
            }
            $coupon_data['coupon_expires']   = $coupon->get_date_expires();
            $coupon_data['start_date']   = self::get_coupon_start_date( $coupon->get_id(), true );
            
            return apply_filters('wt_smart_coupon_meta_data',$coupon_data );
        }
        /**
         * Get formattd Expiration date of a coupon.
         * @since 1.2.5 
         */
        public static function get_coupon_start_date( $coupon_id ,$timestamp = false ) {
            if( $timestamp === true  ) {
                return get_post_meta( $coupon_id, '_wt_coupon_start_date_timestamp', true );
            }
            return get_post_meta( $coupon_id, '_wt_coupon_start_date', true );
             
        }

        /**
         *  Get formatted Start/Expiry date of a coupon.
         *  @since 1.3.7
         */
        public static function get_coupon_start_expiry_date_texts($date, $type="start_date")
        {
            $days_diff= (($date - time())/(24*60*60));
            if($days_diff<0)
            {
                $date_text=($type=="start_date" ? '' : 'expired');
            }else
            {
                $date_text=($type=="start_date" ? __('Starts on ', 'wt-smart-coupons-for-woocommerce') : __('Expires on ', 'wt-smart-coupons-for-woocommerce')). esc_html(date_i18n(get_option('date_format', 'F j, Y'), $date)); 
                $date_text=apply_filters('wt_sc_alter_coupon_start_expiry_date_text', $date_text, $date, $type);
            }
            
            return $date_text;
        }

        /**
         * Get all coupons used by a customer in previous orders.
         * @since 1.0.0
         */
        public static function get_coupon_used_by_a_customer( $user,$coupon_code = '', $return = 'COUPONS' ) {
            global $current_user,$woocommerce,$wpdb;
    
            if( !$user ) {
                $user = wp_get_current_user();
            }
            $coupon_used = array();
            $customer_id = $user->ID;
            $order_types = wc_get_order_types();
            $order_statuses = wc_get_order_statuses();
            if( isset( $order_statuses['wc-cancelled'] ) ) {
                unset( $order_statuses['wc-cancelled'] );
            }
            $args = array(
                'numberposts' => -1,
                'meta_key' => '_customer_user',
                'meta_value'	=> $customer_id,
                'post_type' => $order_types,
                'post_status' => array_keys( $order_statuses )
            );
            $customer_orders = get_posts($args);
            $customer_orders = ( isset( $customer_orders ) && is_array( $customer_orders ) ) ? $customer_orders : array();
            if ($customer_orders) :
                foreach ($customer_orders as $customer_order) :
                    $order = wc_get_order( $customer_order->ID );
                    if( Wt_Smart_Coupon::wt_cli_is_woocommerce_prior_to( '3.7' ) ) {
                        $coupons  = $order->get_used_coupons();
                    } else {
                        $coupons  = $order->get_coupon_codes();
                    }
                    if( $coupons ) {
                        $coupon_used = array_merge( $coupon_used, $coupons );
                    }
                endforeach;
    
                if( $return =='NO_OF_TIMES' && $coupon_code != '' ) {
                    $count_of_used = array_count_values($coupon_used);
                    
                    return isset( $count_of_used[ $coupon_code ] )? $count_of_used[ $coupon_code ] : 0 ;
    
                }
                return array_unique( $coupon_used );
    
            else :
                return false;
            endif;
        }

        /**
         * Remove Quantity field editable for give away products
         */
        public function update_cart_item_quantity_field( $product_quantity = '', $cart_item_key = '', $cart_item = array() ) {

            if( $this->is_a_free_gift_item( $cart_item ) ) {
            
                $product_quantity = sprintf( '%s <input type="hidden" name="cart[%s][qty]" value="%s" />', $cart_item['quantity'], $cart_item_key, $cart_item['quantity'] );
            }
            return $product_quantity;
        }
        
        public function set_coupon_validity_for_free_products($valid, $product, $coupon, $values ){

            $disabled_products = $this->get_free_product_for_a_coupon( $coupon );
            
            if( is_array( $disabled_products ) ) {
                if( in_array( $product->get_id(), $disabled_products ) )
                $valid = false;
            }
            return $valid;
        }
        public function wt_smartcoupon_modify_free_product_price( $cart_item_data ) {
 
            if( isset( $cart_item_data['free_product'] ) &&  $cart_item_data['free_product'] == 'wt_give_away_product' ) {
                $qty = ( isset( $cart_item_data['quantity'] ))?  $cart_item_data['quantity'] :  1 ;
                $product_id     = ( isset( $cart_item_data['product_id'] ) ? $cart_item_data['product_id'] : '' ); ;
                $variation_id   = ( isset( $cart_item_data['variation_id'] ) ? $cart_item_data['variation_id'] : '' );
                if( !empty( $product_id ) || !empty( $variation_id)) {

                    if ( ! empty( $variation_id ) ) {
                        $product = wc_get_product( $variation_id );
        
                    } else {
                        $product = wc_get_product( $product_id );
                    }
                    $product_price  = (float) $product->get_price(); 
                    $discount = $product_price/$qty;
                    $discounted_price = ( $product_price - $discount );
                    $cart_item_data['data']->set_price( $discounted_price );
                    $cart_item_data['data']->set_regular_price( $product_price );
                    $cart_item_data['data']->set_sale_price( $discounted_price );
                }
                
            }
            return $cart_item_data;
        }
        public function wt_smartcoupon_recalculate_price( $cart_item_data ) {

            return $this->wt_smartcoupon_modify_free_product_price( $cart_item_data );
           
        }
        /**
        * Check if coupon applicable to specific user roles
        *
        * @since  1.2.6
        * @access public
        * @return bool
        */
        public static function _wt_sc_check_valid_user_roles( $coupon_id ){
            $_wt_sc_user_roles         = get_post_meta($coupon_id, '_wt_sc_user_roles',true);
            if( isset( $_wt_sc_user_roles ) ) {
                if( ''!= $_wt_sc_user_roles && ! is_array( $_wt_sc_user_roles ) ) {
                    $_wt_sc_user_roles = explode(',',$_wt_sc_user_roles);
                } 
                $user = wp_get_current_user();
                if( isset( $user )) {
                    $user_roles = ( isset( $user->roles ) && is_array( $user->roles ) ) ? $user->roles : array();
                    if( !empty( $_wt_sc_user_roles )){
                        $roles = array_intersect($user_roles, $_wt_sc_user_roles);
                        if(empty($roles)){
                            return false;
                        }
                    }
                }
            }
            return true;
        }


        /**
         * Get available coupons
         * @since 1.3.7
         */
        public static function get_available_coupons_for_user($user = '', $section = 'my_account')
        {
            global $wpdb;
            if(!$user){
                $user= wp_get_current_user();
            }
            if($user)
            {
                $user_id = $user->ID; 
                $email = $user->user_email;
            }


            $coupons_available_in_any_page = $wpdb->get_results("SELECT meta.`post_id`,meta.`meta_value` FROM `" . $wpdb->postmeta . "` meta WHERE  ( meta.`meta_key` =  '_wc_make_coupon_available' AND meta.`meta_value`!= '' )");
            $available_coupon_ids= array();
            if(!empty($coupons_available_in_any_page))
            {
                foreach($coupons_available_in_any_page as $coupons)
                {
                    if(in_array($section, explode(',', $coupons->meta_value)))
                    {
                        $available_coupon_ids[] = $coupons->post_id;
                    }
                }
            }
           
            $coupons = array();
            if(!empty($available_coupon_ids))
            {
                $coupon_args = array(
                    'post_type' => 'shop_coupon',
                    'post__in' => $available_coupon_ids,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'posts_per_page' => '-1'
                );
                $coupons = get_posts($coupon_args);
            }
            return apply_filters('wt_available_coupons_for_user', $coupons, $user, $section);
        }

        public static function coupon_is_valid_for_displaying($coupon, $email, $user_id, $display_invalid_coupons, &$expired_coupon, &$expire_text)
        {
            $coupon_obj = new WC_Coupon( $coupon->ID );
            self::$coupon_obj=$coupon_obj;
            $start_text = '';
            $email_restriction = $coupon_obj->get_email_restrictions();

            // Check is coupon restricted for other Email.
            if(!empty($email_restriction) && !in_array($email, $email_restriction))
            {
                return false;
            }

            // Check is coupon restricted for the user roles.
            $coupon_id    = $coupon_obj->get_id();
            if(self::_wt_sc_check_valid_user_roles( $coupon_id ) === false )
            {
                return false;
            }

            // Check is Coupon Expired.
            $coupon_data  = self::get_coupon_meta_data( $coupon_obj );
            if( $coupon_data['coupon_expires'] ) {

                $exp_date =  $coupon_data['coupon_expires']->getOffsetTimestamp();
                $expire_text = self::get_coupon_start_expiry_date_texts($exp_date, "expiry_date");
                if($expire_text == 'expired')
                {
                    array_push($expired_coupon, $coupon_obj->get_code());
                    return false;
                }
            }else
            {
                $expire_text = '';
            }

            // Check is usage limit per user is exeeded.               
            if($coupon_obj->get_usage_limit() > 0 && $coupon_obj->get_usage_count() >= $coupon_obj->get_usage_limit())
            {
                array_push($expired_coupon, $coupon_obj->get_code());
                return false;         
            }

            if($coupon_obj && $user_id && $coupon_obj->get_usage_limit_per_user()>0 && $coupon_obj->get_id() && $coupon_obj->get_data_store())
            {
                $data_store  = $coupon_obj->get_data_store();
                $usage_count = $data_store->get_usage_by_user_id( $coupon_obj, $user_id );
                if ( $usage_count >= $coupon_obj->get_usage_limit_per_user())
                {
                    array_push($expired_coupon, $coupon_obj->get_code() );
                    return false;
                }
            }

            if($display_invalid_coupons===false && $coupon_obj->is_valid()===false)
            {
                return false;
            }
            
            return true;
        }

        public static function get_coupon_html($coupon, $coupon_data, $expire_text='')
        {

            $coupon_obj =(is_null(self::$coupon_obj) ? new WC_Coupon($coupon->ID) : self::$coupon_obj);
            if(isset($coupon_data['start_date']))
            {
                $start_date =  $coupon_data['start_date'];
                $start_text = self::get_coupon_start_expiry_date_texts((int) $start_date);                   
            }

            if($expire_text=='')
            {
                if($coupon_data['coupon_expires'])
                {
                    $exp_date =  $coupon_data['coupon_expires']->getOffsetTimestamp();
                    $expire_text = self::get_coupon_start_expiry_date_texts($exp_date, "expiry_date");
                    if($expire_text == 'expired')
                    {
                        return false;
                    }
                }else
                {
                    $expire_text = '';
                }
            }
            ?>
            <div class="wt-single-coupon">
                <div class="wt-coupon-content">
                    <div class="wt-coupon-amount">
                        <span class="amount"> <?php echo $coupon_data['coupon_amount'].'</span><span> '.$coupon_data['coupon_type'] ; ?></span>
                    </div>  
                    <div class="wt-coupon-code"> <code> <?php echo $coupon->post_title; ?></code></div>
                    <?php if(  '' != $start_text ) { ?>
                        <div class="wt-coupon-start"><?php echo $start_text; ?></div>
                    <?php  } ?>
                    <?php if(  '' != $expire_text ) { ?>
                        <div class="wt-coupon-expiry"><?php echo $expire_text; ?></div>
                    <?php  } ?>
                   
                    <?php $coupon_desc = $coupon_obj->get_description(); 
                        if( '' != $coupon_desc ) {
                    ?>
                        <div class="coupon-desc-wrapper">
                            <i class="info"> i </i>
                            <div class="coupon-desc"> <?php echo $coupon_desc; ?> </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }

        /**
         * Print available coupons
         * @since 1.3.7
         */
        public static function print_available_coupon_for_user($coupons, &$printed_coupons, &$expired_coupon, $section = 'my_account')
        {
            $display_invalid_coupons=apply_filters('wt_smart_coupon_display_invalid_coupons', true);

            $current_user = wp_get_current_user(); 
            $user_id = ( isset( $current_user->ID ) ? (int) $current_user->ID : 0 );
            $email = $current_user->user_email;

            foreach($coupons as $coupon)
            {
                $expire_text='';
                
                if(!self::coupon_is_valid_for_displaying($coupon, $email, $user_id, $display_invalid_coupons, $expired_coupon, $expire_text))
                {
                    continue;
                }
                do_action('wt_smart_coupon_before_'.$section.'_single_coupon', self::$coupon_obj);

                $coupon_data  = self::get_coupon_meta_data(self::$coupon_obj);

                self::get_coupon_html($coupon, $coupon_data, $expire_text);

                $printed_coupons['available_coupons'][]=self::$coupon_obj;
            }
        }

        /**
         * Print coupon CSS
         * @since 1.3.7
         */
        public static function print_coupon_css()
        {
            $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();
            ?>
            <style type="text/css">
               .wt-single-coupon{
                    background-color: <?php echo $smart_coupon_options['wt_active_coupon_bg_color']; ?> ;
                    border: 2px dashed <?php echo $smart_coupon_options['wt_active_coupon_border_color']; ?>;
                    color: <?php echo $smart_coupon_options['wt_active_coupon_border_color']; ?>;
                    box-shadow: 0 0 0 4px <?php echo $smart_coupon_options['wt_active_coupon_bg_color']; ?>, 2px 1px 6px 4px rgba(10, 10, 0, 0.5);
                    text-shadow: -1px -1px <?php echo $smart_coupon_options['wt_active_coupon_bg_color']; ?>;
                }

                .wt-single-coupon.used-coupon {
                    background-color: <?php echo $smart_coupon_options['wt_used_coupon_bg_color']; ?> ;
                    border: 2px dashed <?php echo $smart_coupon_options['wt_used_coupon_border_color']; ?>;
                    color: <?php echo $smart_coupon_options['wt_used_coupon_border_color']; ?>;
                    box-shadow: 0 0 0 4px <?php echo $smart_coupon_options['wt_used_coupon_bg_color']; ?>, 2px 1px 6px 4px rgba(10, 10, 0, 0.5);
                    text-shadow: -1px -1px <?php echo $smart_coupon_options['wt_used_coupon_bg_color']; ?>;
                }
                .wt-single-coupon.used-coupon.expired {
                    background-color: <?php echo $smart_coupon_options['wt_expired_coupon_bg_color']; ?> ;
                    border: 2px dashed <?php echo $smart_coupon_options['wt_expired_coupon_border_color']; ?>;
                    color: <?php echo $smart_coupon_options['wt_expired_coupon_border_color']; ?>;
                    box-shadow: 0 0 0 4px <?php echo $smart_coupon_options['wt_expired_coupon_bg_color']; ?>, 2px 1px 6px 4px rgba(10, 10, 0, 0.5);
                    text-shadow: -1px -1px <?php echo $smart_coupon_options['wt_expired_coupon_bg_color']; ?>;
                }

            </style>
            <?php
        }

        /**
         * Display available coupons in checkout
         * @since 1.3.7
         */
        public function display_available_coupon_in_checkout()
        {
            $current_user = wp_get_current_user();
            $coupons = self::get_available_coupons_for_user($current_user, 'checkout');
            if(!empty($coupons))
            {
                $expired_coupon = array();
                $printed_coupons=array(
                    'available_coupons'=>array()
                );

                do_action('wt_smart_coupon_before_checkout_coupons');

                self::print_coupon_css(); /* print coupon CSS */

                echo '<div class="wt_coupon_wrapper">';
                    Wt_Smart_Coupon_Public::print_available_coupon_for_user($coupons, $printed_coupons, $expired_coupon, 'checkout');
                echo '</div>';

                do_action('wt_smart_coupon_after_checkout_coupons', $printed_coupons);
            }
        }
    }
}