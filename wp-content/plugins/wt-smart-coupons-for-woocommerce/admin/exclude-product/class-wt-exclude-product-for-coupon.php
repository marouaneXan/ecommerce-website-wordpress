<?php

if (!defined('WPINC')) {
    die;
}

/**
 * Option for excluding a product from applying coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/exclude-product
 */

if( ! class_exists ( 'Wt_smart_coupon_exclude_product_from_coupon' ) ) {

    
    class Wt_smart_coupon_exclude_product_from_coupon {
        protected $option;

        public function __construct() {

            add_action( 'woocommerce_product_options_general_product_data', array($this,'add_exclude_product_check_box' ) );
        
            add_action( 'woocommerce_process_product_meta', array($this,'save_exclude_product_data'), 10, 1 );
            add_filter( 'woocommerce_coupon_is_valid', array($this,'set_coupon_validity_for_excluded_products'), 10, 2);
           
        }
        
        /**
         * function for getting all disabled product
         * @since 1.1.1
         */
        function get_disabled_product( ) {
            if( empty( $this->option )) {
                $this->option = get_option('wt_disabled_product_for_coupons');
            }
            return $this->option;
        }

        /**
         * function for update all disabled product
         * @since 1.1.1
         */
        function set_disabled_products( $products ) {
           
            update_option( 'wt_disabled_product_for_coupons',$products);
            $this->option = $products;
         
        }

        /**
         * Add disabled product checkbox
         * @since 1.1.1
         */
        function add_exclude_product_check_box(){
            global $post;

            echo '<div class="wt-exclude-product-from-coupon">';
            woocommerce_wp_checkbox( array(
                'id'        => '_wt_disabled_for_coupons',
                'label'     => __('Exclude from coupons', 'wt-smart-coupons-for-woocommerce'),
                'description' => __('Exclude this product from coupon discounts', 'wt-smart-coupons-for-woocommerce'),
                'desc_tip'  => 'true',
            ) );
        
            echo '</div>';
        }
        
        /**
         * Save Disabled Product meta
         * @since 1.1.1
         */
        function save_exclude_product_data( $post_id ){
            
            $meta_disabled = get_post_meta( $post_id, '_wt_disabled_for_coupons',true);
            $current_disabled = isset( $_POST['_wt_disabled_for_coupons'] ) ? 'yes' : 'no';
            if( empty( $meta_disabled ) && $current_disabled == "no" ) {
                return;
            }
            $disabled_products = $this->get_disabled_product();
            if( empty($disabled_products) ) {
                if( $current_disabled == 'yes' )
                    $disabled_products = array( $post_id );
            } else {
                if( $current_disabled == 'yes' ) {
                    $disabled_products[] = $post_id;
                    $disabled_products = array_unique( $disabled_products );
                } else {
                    if ( ( $key = array_search( $post_id, $disabled_products ) ) !== false )
                        unset( $disabled_products[$key] );
                }
            }
        
            update_post_meta( $post_id, '_wt_disabled_for_coupons', $current_disabled );
            $this->set_disabled_products( $disabled_products );
        }
        
        /**
         * Disable the coupon if cart contains any exluded product.
         * @since 1.1.1
         */
        function set_coupon_validity_for_excluded_products($valid, $coupon ) {
            
            if( $valid == false ) {
                return $valid;
            }
            $disabled_products = $this->get_disabled_product();

            if( ! is_array( $disabled_products ) ||  ! count( $disabled_products ) > 0 ) return $valid;

            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            if( empty( $items ) ) {
                return $valid;
            }
            $items = ( isset( $items ) && is_array( $items ) ) ? $items : array();
            $items_to_check = array();
            foreach( $items as $item ) {
                array_push($items_to_check,$item['product_id']);
            }
        
            if( ! empty( $disabled_products ) ) {
                foreach( $disabled_products as $disabled_product  ) {
                    if ( in_array( $disabled_product, $items_to_check ) ) {
                        $valid = false;
                        break;
                    }
                }
            }
            

            if ( ! $valid ) {
                throw new Exception( __( 'Sorry, this coupon is not applicable for selected products.', 'wt-smart-coupons-for-woocommerce' ), 109 );
            }
        
            return $valid;
        }
        
       
    }
}

$exclude_prod = new Wt_smart_coupon_exclude_product_from_coupon();