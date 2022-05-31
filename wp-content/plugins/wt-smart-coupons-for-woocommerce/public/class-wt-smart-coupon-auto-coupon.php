<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists('Wt_Smart_Coupon_Auto_Coupon') ) {
    class Wt_Smart_Coupon_Auto_Coupon {

        protected $overwrite_coupon_message, $_user_emails;

        private $coupon_is_processing = false;
        private $auto_coupon_session_hash = ''; 
        protected $autocoupons = null;

        public function __construct() {
    
            $this->overwrite_coupon_message = array();
            add_action('woocommerce_coupon_options',array( $this,'add_auto_coupon_options' ),10,2);
            add_action('woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta'), 11, 2);
            add_action( 'woocommerce_checkout_update_order_review', array( $this, 'store_billing_email_into_session' ), 10 ); 
            add_action( 'woocommerce_after_checkout_validation', array( $this, 'store_billing_email_into_session' ), 10 ); 
            add_action( 'woocommerce_check_cart_items', array( $this, 'woocommerce_check_cart_items' ), 0, 0 );
            add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'coupon_html' ), 10, 2 );
            
            // Action to auto apply coupons.
            
            add_action( 'wp_loaded', array( $this, 'auto_apply_coupons' ));
            add_action( 'woocommerce_checkout_update_order_review', array( $this, 'reset_auto_coupon_hash' ) );
            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'action_woocommerce_cart_loaded_from_session' ) );
            add_action( 'woocommerce_after_calculate_totals', array( $this, 'maybe_apply_auto_coupons' ));
            
        }
    
    
        /**
         * Add coupon meta field for setting AutoCoupon
         * @since 1.1.0
         */
        function add_auto_coupon_options( $coupon_id, $coupon ) {
            
            $_wt_make_auto_coupon = get_post_meta($coupon_id , '_wt_make_auto_coupon', true );
    
            woocommerce_wp_checkbox(
                array(
                    'id' => '_wt_make_auto_coupon',
                    'label' => __('Apply coupon automatically', 'wt-smart-coupons-for-woocommerce'),
                    'desc_tip' => true,
                    'description' => __('This coupon will be applied automatically if the specifications are met. The corresponding coupon description will be shown when the coupon is applied, only 5 coupons will be applied automatically, rest will be ignored.', 'wt-smart-coupons-for-woocommerce'),
                    'wrapper_class' => 'wt_auto_coupon',
                    'value'       =>  wc_bool_to_string( $_wt_make_auto_coupon  ),
                    )
                );
        
        }
        /**
         * Save AutoCoupon meta
         * @since 1.1.0
         */
        function process_shop_coupon_meta( $post_id, $post ) {
            if( isset( $_POST['_wt_make_auto_coupon'] ) && $_POST['_wt_make_auto_coupon']!='' ) {
                update_post_meta($post_id, '_wt_make_auto_coupon',  true );
            } else {
                update_post_meta($post_id, '_wt_make_auto_coupon', false );
            }
        }
    
        /**
         * Function to check specified coupon is autocoupon
         * @since 1.1.0
         * @since 1.3.4 Checking via postmeta
         */
        function is_auto_coupon($coupon)
        {
            if(is_object($coupon))
            {
                $coupon = $coupon->get_id();
            }
            return (get_post_meta($coupon, '_wt_make_auto_coupon', true) ? true : false);
        }
    
        /**
         * Function to retrive all auto coupons
         * @since 1.1.0
         */
        function get_all_auto_coupons() {

            if( ! is_array( $this->autocoupons ) ) {
                global $wpdb;
                $auto_coupons = array();
                $this->autocoupons = array();
                $coupon_items = $wpdb->get_results("SELECT DISTINCT `ID` as coupon_id FROM $wpdb->posts as P INNER JOIN $wpdb->postmeta AS PM1 ON (P.`ID` = PM1.`post_id`) WHERE P.`post_type` = 'shop_coupon' AND P.`post_status` = 'publish' AND PM1.`meta_key` = '_wt_make_auto_coupon' AND PM1.`meta_value` = 1  ORDER BY P.`post_date` DESC");
                $coupon_items = ( isset( $coupon_items ) && is_array( $coupon_items ) ) ? $coupon_items : array();
                foreach( $coupon_items as $coupon_item ) {

                    $this->autocoupons[] = $coupon_item->coupon_id;
                }
            } 
            return $this->autocoupons;
        }
    
        /**
         * Get all available auto coupons.
         * @since 1.1.0
         * @since 1.3.4 Maximum limit section updated
         */
        function get_available_auto_coupons( $return ="OBJECT" )
        {   
            $available_coupons = array();
            $all_auto_coupons =  $this->get_all_auto_coupons();
            $all_auto_coupons = ( isset( $all_auto_coupons ) && is_array( $all_auto_coupons ) ) ? $all_auto_coupons : array();
            $auto_coupon_limit = apply_filters('wt_smartcoupon_max_auto_coupons_limit', 5);
            $i=0;
            foreach($all_auto_coupons as $auto_coupon)
            {
                $coupon_obj = new WC_Coupon( $auto_coupon );
                if( $this->is_valid_coupon( $coupon_obj ) )
                {
                    $i++;
                    if($return == "OBJECT")
                    {
                        $available_coupons[] = $coupon_obj;
                    } else {
                        $available_coupons[] = $coupon_obj->get_code();
                    }
                    if($i==$auto_coupon_limit)
                    {
                        break;
                    }
                }
                
            }
    
            return $available_coupons;   
        }
        
        /**
         * Check is coupon can apply. 
         * @since 1.1.0
         */
        private function is_valid_coupon( $coupon ) {
    
            // echo $coupon->get_code(); echo '<br/>';
            $can_be_applied = true;
    
            $cart = WC()->cart;
    
            //Test validity
            if ( ! $coupon->is_valid() ) {
                $can_be_applied = false;
            }
    
            if ( $can_be_applied && $coupon->get_usage_limit() > 0 && $coupon->get_usage_count() >= $coupon->get_usage_limit() ) {
                $can_be_applied = false;
            }
    
            $check_emails = $this->get_user_emails();
            $check_emails = ( isset( $check_emails ) && is_array( $check_emails ) ) ? $check_emails : array();
            if ( $can_be_applied ) {
                $restrictions = $coupon->get_email_restrictions();
                if ( is_array( $restrictions ) && 0 < count( $restrictions ) && ! $this->is_coupon_emails_allowed( $check_emails, $restrictions, $cart ) ) {
                    $can_be_applied = false;
                }
            }
            
    
            if ( $can_be_applied ) {
    
                $limit_per_user = $coupon->get_usage_limit_per_user();
                if ( 0 < $limit_per_user ) {
                    $used_by         = $coupon->get_used_by();
                    $usage_count     = 0;
                    $user_id_matches = array( get_current_user_id() );
    
                    // Check usage Registered emails.
                    foreach ( $check_emails as $check_email ) {
                        $usage_count      += count( array_keys( $used_by, $check_email, true ) );
                        $user              = get_user_by( 'email', $check_email );
                        $user_id_matches[] = $user ? $user->ID : 0;
                    }
                    // Check against billing Email.
                    $users_query = new WP_User_Query(
                        array(
                            'fields'     => 'ID',
                            'meta_query' => array(
                                array(
                                    'key'     => '_billing_email',
                                    'value'   => $check_emails,
                                    'compare' => 'IN',
                                ),
                            ),
                        )
                    );
    
                    $user_id_matches = array_unique( array_filter( array_merge( $user_id_matches, $users_query->get_results() ) ) );
                    $user_id_matches = ( isset( $check_emails ) && is_array( $user_id_matches ) ) ? $check_emails : array();
                    foreach ( $user_id_matches as $user_id ) {
                        $usage_count += count( array_keys( $used_by, (string) $user_id, true ) );
                    }
    
                    if ( $usage_count >= $coupon->get_usage_limit_per_user() ) {
                        $can_be_applied = false;
                    }
                    
                    
                }
            }
    
            return apply_filters( 'wt_is_valid_coupon', $can_be_applied, $coupon );
        }
    
       
        /**
         * Store the userdata into session
         * @since 1.1.0
         */
        public function store_billing_email_into_session( $post_data ) {
            if ( ! is_array( $post_data ) ) {
                parse_str( $post_data, $posted );
            } else {
                $posted = $post_data;
            }
    
            if ( isset( $posted['billing_email'] ) ) {
                $this->set_session( 'billing_email', $posted['billing_email'] );
            }
        }
        
        /**
         * Set smartcoupon session.
         * @since 1.1.0
         */
        public function set_session( $key, $value ) {
            if ( ! isset( $this->_session_data ) ) {
                if ( ! isset( WC()->session ) ) {
                    return null;
                }
                $this->_session_data = WC()->session->get( '_wt_smart_coupon_session_data', array() );
            }
            if ( is_null( $value ) ) {
                unset( $this->_session_data[ $key ] );
            } else {
                $this->_session_data[ $key ] = $value;
            }
    
            WC()->session->set( '_wt_smart_coupon_session_data', $this->_session_data );
        }
        
    
        /**
         * Cache the session data into private variable 
         * @since 1.1.0
         */
        public function get_session( $key = null, $default = false ) {
            if ( ! isset( $this->_session_data ) ) {
                if ( ! isset( WC()->session ) ) {
                    return null;
                }
                $this->_session_data = WC()->session->get( '_wt_smart_coupon_session_data', array() );
            }
    
            if ( ! isset( $key ) ) {
                return $this->_session_data;
            }
            if ( ! isset( $this->_session_data[ $key ] ) ) {
                return $default;
            }
            return $this->_session_data[ $key ];
        }
    
    
         /**
         * Removed unmatched cart item ( Befor removing woocommmerce )
         * @since 1.1.0
         */
        function woocommerce_check_cart_items() {
            $this->remove_unmatched_autocoupons();
        }
        
     
        
        /**
         * Get user Account and billing email
         * @since 1.1.0
         */
        public function get_user_emails() {
            if ( ! $this->_user_emails  ) {
                $this->_user_emails = array();
                if ( is_user_logged_in() ) {
                    $current_user         = wp_get_current_user();
                    $this->_user_emails[] = $current_user->user_email;
                }
            }
            $user_emails = $this->_user_emails;
    
            $billing_email = $this->get_session( 'billing_email', '' );
            if ( is_email( $billing_email ) ) {
                $user_emails[] = $billing_email;
            }
    
            $user_emails = array_map( 'strtolower', $user_emails );
            $user_emails = array_map( 'sanitize_email', $user_emails );
            $user_emails = array_filter( $user_emails, 'is_email' );
            return array_unique( $user_emails );
        }
        
        /**
         * Check whether the email allowed the restricted email list.
         * @since 1.1.0
         */
        private function is_coupon_emails_allowed( $check_emails, $restrictions, $cart ) {
            if ( is_callable( array( $cart, 'is_coupon_emails_allowed' ) ) ) {
                return $cart->is_coupon_emails_allowed( $check_emails, $restrictions );
            }
    
            return sizeof( array_intersect( $check_emails, $restrictions ) ) > 0;
        }
    
        /**
         * Remove unmatched coupons silentley
         * @since 1.1.0
         */
        private function remove_unmatched_autocoupons( $valid_coupon_codes = null ) {
    
            if ( is_null( $valid_coupon_codes ) ) {
                $valid_coupon_codes = $this->get_available_auto_coupons( "CODE" );
            }
    
            //Remove invalids
            $calc_needed = false;
            $applied_coupons = WC()->cart->get_applied_coupons();
            $applied_coupons = ( isset( $applied_coupons ) && is_array( $applied_coupons ) ) ? $applied_coupons : array();
            foreach ( $applied_coupons as $coupon_code ) {
                if ( in_array( $coupon_code, $valid_coupon_codes ) ) {
                    continue;
                }
    
                $coupon = new WC_Coupon( $coupon_code );
    
                if ( ! $this->is_auto_coupon( $coupon ) && $coupon->is_valid() ) {
                    continue;
                }
                if ( ! apply_filters( 'wt_remove_invalid_coupon_automatically', $this->is_auto_coupon( $coupon ), $coupon ) ) {
                    continue;
                }
               WC()->cart->remove_coupon( $coupon_code );
                $calc_needed = true;
            }
    
            $calc_needed = false;
    
            return $calc_needed;
        }
        /**
        * Check whether to apply auto coupons
        * @since  1.2.6
        * @throws Exception Error message.
        */
        function action_woocommerce_cart_loaded_from_session() {
			$this->auto_coupon_session_hash = $this->get_session( 'wt_smart_coupon_auto_coupon_hash', '' );
		}
        public function maybe_apply_auto_coupons() {
            if( $this->cart_contains_subscription() === true ) {
               return;
            }
            if ( $this->coupon_is_processing ) {
				return;
            }
            $current_hash = $this->get_current_hash_values();
            if( $current_hash === $this->auto_coupon_session_hash ) {
                return;
            }
            $this->coupon_is_processing = true;
            $this->auto_apply_coupons();
            $this->coupon_is_processing = false;
            $this->auto_coupon_session_hash = $current_hash;
            $this->set_session( 'wt_smart_coupon_auto_coupon_hash', $current_hash );
        }
        public function reset_auto_coupon_hash() {
            $this->auto_coupon_session_hash = '';
        }
        public function get_current_hash_values() {

            $combined_hash = array(
				'cart'                        => WC()->cart->get_cart_for_session(),
                'current_coupons'             => WC()->cart->get_applied_coupons(), 
                'current_payment_method'      => isset( WC()->session->chosen_payment_method ) ? WC()->session->chosen_payment_method : array(),
				'current_shipping_method'     => isset( WC()->session->chosen_shipping_methods ) ? WC()->session->chosen_shipping_methods : array(),
				'current_date'                => current_time( 'Y-m-d' ),
			);
			$combined_hash = apply_filters( 'wt_smart_coupon_auto_coupon_triggers', $combined_hash );
			return md5( wp_json_encode( $combined_hash ) );
			
        }
        public function auto_apply_coupons() { 
            $need_calc = false;
		    $cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_object( $cart ) && is_callable( array( $cart, 'is_empty' ) ) && ! $cart->is_empty() ) {
                $available_coupons = $this->get_available_auto_coupons();
                
                $auto_coupons_codes = array();
                foreach ( $available_coupons as $coupon ) {
                    $auto_coupons_codes[] = $coupon->get_code();
                }
                $need_calc = $this->remove_unmatched_autocoupons( $auto_coupons_codes );
                
                if (!empty($available_coupons) && is_array($available_coupons)) {
                    $individual_coupon_applied = false;
                    foreach ($available_coupons as $available_coupon) {
    
                        $coupon_code = $available_coupon->get_code();
                        $cart_total = $cart->get_cart_contents_total();
                        $individual_coupon_applied = $this->wt_sc_check_individual_coupon_applied( $cart);
                        // Check if cart still requires a coupon discount and does not have coupon already applied.
                        if ($cart_total > 0 && !$cart->has_discount($coupon_code) && $individual_coupon_applied === false ) {
                            $coupon_desc = $available_coupon->get_description();
                            if ($coupon_desc) {
                                $coupon_desc = ': ' . $coupon_desc;
                            }
                            $new_message = apply_filters( 'wt_smart_coupon_auto_coupon_message', __('Coupon code applied successfully','wt-smart-coupons-for-woocommerce').' '.$coupon_desc, $available_coupon) ;
                            $this->start_overwrite_coupon_success_message($coupon_code, $new_message);
                            WC()->cart->add_discount($coupon_code);
                            $this->stop_overwrite_coupon_success_message();
                        }
                    }
                }
                
                if ($need_calc) {
                    $cart->calculate_totals();
                }
            }
            
        }
        
        public function wt_sc_check_individual_coupon_applied( $cart ) {
            $applied = false;
            $coupons = $cart->get_applied_coupons();
            $coupons = ( isset( $coupons ) && is_array( $coupons ) ) ? $coupons : array();
            foreach( $coupons as $code ) {
                $coupon = new WC_Coupon( $code );
                if( $coupon->get_individual_use() ) {
                    $applied = true;
                }
            }
            return $applied;
        }
        /**
         * Owerwrite Coupon default success message with specified message.
         * @since 1.1.0
         */
        function start_overwrite_coupon_success_message( $coupon, $new_message = "" ) {
            $this->overwrite_coupon_message[$coupon] = $new_message;
            add_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10, 3 );
        }
        /**
         * Unset owewriting coupon success message.
         * @since 1.1.0
         */
        function stop_overwrite_coupon_success_message() {
            remove_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10 );
            $this->overwrite_coupon_message = array();
        }
        /**
         * Filter function for owerwriting message.
         * @since 1.1.0
         */
        function owerwrite_coupon_code_message( $msg, $msg_code, $coupon ) {
            if ( isset( $this->overwrite_coupon_message[ $coupon->get_code() ] ) ) {
                $msg = $this->overwrite_coupon_message[ $coupon->get_code() ];
            }
            return $msg;
        }
    
    
        /**
         * Update coupon HTML on cart total
         * @since 1.1.0
         */
        function coupon_html( $originaltext, $coupon ) {
            if ( $this->is_auto_coupon( $coupon )  ) {
                $value = array();
    
                $amount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
                if ( $amount ) {
                    $discount_html = '-' . wc_price( $amount );
                } else {
                    $discount_html = '';
                }
    
                $value[] = apply_filters( 'woocommerce_coupon_discount_amount_html', $discount_html, $coupon );
    
                if ( $coupon->get_free_shipping() ) {
                    $value[] = __( 'Free shipping coupon', 'wt-smart-coupons-for-woocommerce' );
                }
    
                return implode( ', ', array_filter( $value ) );
            } else {
                return $originaltext;
            }
        }
        /**
        * Check if cart contains subscription and switch autocoupon apply method to call directly before calculate_totals hook.
        *
        * @since  1.2.7
        * @access public
        * @throws Exception Error message.
        */
        public function cart_contains_subscription() {
            $has_subscription = false;
            $cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_object( $cart ) && is_callable( array( $cart, 'is_empty' ) ) && ! $cart->is_empty() ) {
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $product = $cart_item['data'];
                    if ( is_a( $product, 'WC_Product_Subscription' ) || is_a( $product, 'WC_Product_Subscription_Variation' ) ) {
                        $has_subscription = true;
                    }
                }
            }
            return $has_subscription;
            
        }
    }
    $Wt_Smart_Coupon_Auto_Coupon = new Wt_Smart_Coupon_Auto_Coupon();
}