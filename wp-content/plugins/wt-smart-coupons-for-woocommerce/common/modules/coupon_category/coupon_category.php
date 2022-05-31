<?php
/**
 * Coupon category admin/public
 *
 * @link      
 * @since 1.3.5     
 *
 * @package  Wt_Smart_Coupon
 */
if (!defined('ABSPATH')) {
    exit;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Category_Common' ) ){

	class Wt_Smart_Coupon_Category_Common {
		public $module_base='coupon_category';
        public $module_id='';
        public static $module_id_static='';
        private static $instance = null;

        public function __construct()
        {
            $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
            self::$module_id_static=$this->module_id;

            add_filter('init', array($this, 'register_coupon_category_taxonomy'));

            add_action('shop_coupon_cat_pre_add_form', array($this, 'taxonomy_tab_view'));

            add_action('restrict_manage_posts', array($this, 'add_coupon_category_filter'));
            add_filter('manage_edit-shop_coupon_columns', array($this, 'add_coupon_category_column'));
        	add_filter('manage_shop_coupon_posts_custom_column', array($this, 'add_coupon_category_column_content'), 10, 2);

        	add_filter('woocommerce_screen_ids', array($this, 'add_to_wc_screens'), 10, 1);

        }

        /**
         * Get Instance
         * @since 1.3.5
         */
        public static function get_instance()
        {
            if(self::$instance==null)
            {
                self::$instance=new Wt_Smart_Coupon_Category_Common();
            }
            return self::$instance;
        }

        /**
		* Coupon category column head
		* @since 1.3.5
		* @param array $columns Columns list.
        */
        public function add_coupon_category_column($columns)
	    {
	        $columns['coupon_categories']=__('Categories', 'wt-smart-coupons-for-woocommerce');
	        return $columns;
	    }

	    /**
		* Coupon category column content
		* @since 1.3.5
		* @param string 	$column    	Column name.
		* @param int    	$coupon_id 	Coupon ID.
        */
	    public function add_coupon_category_column_content($column, $coupon_id)
	    {
	        if('coupon_categories'!==$column)
	        {
	            return;
	        }

	        $categories=get_the_terms($coupon_id, 'shop_coupon_cat');

	        if(is_array($categories) && !empty($categories))
	        {
	        	$out=array();
	        	$cat_filter_link=admin_url('edit.php?post_type=shop_coupon&shop_coupon_cat=');
	            foreach($categories as $category)
	            {
	            	$out[]='<a href="'.esc_attr($cat_filter_link.$category->slug).'">'.esc_html($category->name).'</a>';
	            }
	            echo implode(', ', $out);
	        }else
	        {
	        	echo '--';
	        }
	    }

	    /**
		* Coupon category filter select box in coupon listing page
		* @since 1.3.5
		* @param string $post_type Post type.
        */
        public function add_coupon_category_filter($post_type)
        {
        	if('shop_coupon'!==$post_type)
        	{
	            return;
	        }
	        $selected_val=(isset($_GET['shop_coupon_cat']) ? Wt_Smart_Coupon_Security_Helper::sanitize_item($_GET['shop_coupon_cat']) : '');
	        $args=array(
	            'show_count'         => true,
	            'hierarchical'       => true,
	            'show_uncategorized' => true,
	            'pad_counts'         => true,
	            'hide_empty'         => false,	            
	            'selected'           => $selected_val,
	            'show_option_none'   => __('Select category', 'wt-smart-coupons-for-woocommerce'),
	            'option_none_value'  => '',
	            'value_field'        => 'slug',
	            'taxonomy'           => 'shop_coupon_cat',
	            'name'               => 'shop_coupon_cat',
	            'orderby'            => 'name',
	            'class'              => 'dropdown_shop_coupon_cat',
	        );

	        wp_dropdown_categories($args);
        }

        public function taxonomy_tab_view($taxonomy)
        {
        	do_action('smart_coupons_display_views');
        	?>
        	<script type="text/javascript">
        		jQuery(document).ready(function(){
        			jQuery('.smart-coupon-tabs').insertBefore('form.search-form');
        		});
        	</script>
        	<?php
        }


        /**
         * 	@since 1.3.5
         * 	Add coupon category to WC screen IDs
         */
        public function add_to_wc_screens($screen_ids)
        {
        	$screen_ids[]='edit-shop_coupon_cat';
        	return $screen_ids;
        }


        /**
         * 	@since 1.3.5
         * 	Register coupon category taxonomy
         */
        public function register_coupon_category_taxonomy()
        {
        	$labels = array(
	            'name'              => _x('Categories', 'Taxonomy General Name', 'wt-smart-coupons-for-woocommerce'),
	            'singular_name'     => _x('Category', 'Taxonomy Singular Name', 'wt-smart-coupons-for-woocommerce'),
	            'search_items'      => __('Search categories', 'wt-smart-coupons-for-woocommerce'),	            
	            'all_items'         => __('All categories', 'wt-smart-coupons-for-woocommerce'),
	            'parent_item'       => __('Parent category', 'wt-smart-coupons-for-woocommerce'),
	            'parent_item_colon' => __('Parent category:', 'wt-smart-coupons-for-woocommerce'),
	            'edit_item'         => __('Edit category', 'wt-smart-coupons-for-woocommerce'),
	            'update_item'       => __('Update category', 'wt-smart-coupons-for-woocommerce'),
	            'add_new_item'      => __('Add new category', 'wt-smart-coupons-for-woocommerce'),
	            'new_item_name'     => __('New category name', 'wt-smart-coupons-for-woocommerce'),
	            'menu_name'         => __('Categories', 'wt-smart-coupons-for-woocommerce'),	            
	            'view_item'         => __('View category', 'wt-smart-coupons-for-woocommerce'),
	            'popular_items'     => __('Popular categories', 'wt-smart-coupons-for-woocommerce'),	            
	            'not_found'         => __('Not found', 'wt-smart-coupons-for-woocommerce'),
	            'most_used'         => __('Most used', 'wt-smart-coupons-for-woocommerce'),
	        );

	        $args = array(
	            'labels'            => $labels,
	            'label'             => $labels['singular_name'],
	            'hierarchical'      => true,
	            'public'            => false,
	            'show_ui'           => true,
	            'show_admin_column' => true,
	            'show_in_nav_menus' => false,
	            'show_tagcloud'     => false,
	            'show_in_rest'      => true,
	            'show_in_menu'      => true,
	            'capabilities'      => array(
		            'manage_terms' 	=> 'manage_woocommerce',
		            'edit_terms'   	=> 'manage_woocommerce',
		            'delete_terms' 	=> 'manage_woocommerce',
		            'assign_terms' 	=> 'manage_woocommerce',
		        ),            
	        );

	        register_taxonomy('shop_coupon_cat', array('shop_coupon'), $args);
        }
	}
	Wt_Smart_Coupon_Category_Common::get_instance();
}