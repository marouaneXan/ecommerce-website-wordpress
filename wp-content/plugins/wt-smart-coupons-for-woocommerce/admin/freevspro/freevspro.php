<?php
/**
 * Free VS Pro Comparison
 *
 * @link       
 * @since 2.7.4    
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wt_Smart_Coupon_Freevspro
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='freevspro';
	public function __construct()
	{
		$this->module_id=$this->module_base;
		self::$module_id_static=$this->module_id;

		/*
		*	Init
		*/
		add_action('admin_init', array($this, 'init'));
	}

	/**
	*	To show WT other free plugins
	*/
	public function wt_other_pluigns()
	{

		$other_plugins_arr=array(
		    array(
		    	'key'=>'cookie-law-info',
		    	'title'=>__('GDPR Cookie Consent (CCPA Ready)', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('This plugin will assist you in making your website GDPR (RGPD, DSVGO) compliant.', 'wt-smart-coupons-for-woocommerce'),
		    	'icon'=>'icon-256x256.png',
		    ),
		    array(
		    	'key'=>'users-customers-import-export-for-wp-woocommerce',
		    	'title'=>__('Import Export WordPress Users and WooCommerce Customers', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('This plugin allows you to import and export WordPress users and WooCommerce customers quickly and easily.', 'wt-smart-coupons-for-woocommerce'),
		    ),
		    array(
		    	'key'=>'order-xml-file-export-import-for-woocommerce',
		    	'title'=>__('Order XML File Export Import for WooCommerce', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('The Order XML File Export Import Plugin for WooCommerce will export your WooCommerce orders in XML format.', 'wt-smart-coupons-for-woocommerce'),
		    ),
		    array(
		    	'key'=>'wt-woocommerce-sequential-order-numbers',
		    	'title'=>__('Sequential Order Number for WooCommerce', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('Using this plugin, you will always get sequential order number for woocommerce.', 'wt-smart-coupons-for-woocommerce'),
		    	'file'=>'wt-advanced-order-number.php'
		    ),
		    array(
		    	'key'=>'wp-migration-duplicator',
		    	'title'=>__('WordPress Migration & Duplicator', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('This plugin exports your WordPress website media files, plugins and themes including the database with a single click.', 'wt-smart-coupons-for-woocommerce'),
		    	'icon'=>'icon-128x128.jpg',
		    ),
		    array(
		    	'key'=>'express-checkout-paypal-payment-gateway-for-woocommerce',
		    	'title'=>__('PayPal Express Checkout Payment Gateway for WooCommerce', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('With this plugin, your customer can use their credit cards or PayPal Money to make order from cart page itself.', 'wt-smart-coupons-for-woocommerce'),
		    	'icon'=>'icon-128x128.jpg',
		    ),
		);

		shuffle($other_plugins_arr);

		$must_plugins_arr=array(
			array(
		    	'key'=>'wt-woocommerce-related-products',
		    	'title'=>__('Related Products for WooCommerce', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('This plugin allows you to choose related products for a particular product.', 'wt-smart-coupons-for-woocommerce'),
		    	'file'=>'custom-related-products.php',
		    	'icon'=>'icon-256x256.png',
			),
			array(
		    	'key'=>'decorator-woocommerce-email-customizer',
		    	'title'=>__('Decorator – WooCommerce Email Customizer', 'wt-smart-coupons-for-woocommerce'),
		    	'description'=>__('Customize your WooCommerce emails now and stand out from the crowd!', 'wt-smart-coupons-for-woocommerce'),
		    	'file'=>'decorator.php'
		    )
		);

		/* must plugins as first items */
		$other_plugins_arr=array_merge($must_plugins_arr, $other_plugins_arr);
		
		$plugin_count=0;
		ob_start();
		foreach($other_plugins_arr as $plugin_data)
		{
			if($plugin_count>=5) //maximum 3 plugins
			{
				break;
			}
			$plugin_key=$plugin_data['key'];
			$plugin_file=WP_PLUGIN_DIR.'/'.$plugin_key.'/'.(isset($plugin_data['file']) ? $plugin_data['file'] : $plugin_key.'.php');
			if(!file_exists($plugin_file)) //plugin not installed
			{
				$plugin_count++;
				$plugin_title=$plugin_data['title'];
				$plugin_icon=isset($plugin_data['icon']) ? $plugin_data['icon'] : 'icon-128x128.png';
				?>
				<div class="wt_smcpn_other_plugin_box">
		            <div class="wt_smcpn_other_plugin_hd">
		                <?php echo $plugin_title;?>
		            </div>
		            <div class="wt_smcpn_other_plugin_con">
		                <?php echo $plugin_data['description'];?>
		            </div>
		            <div class="wt_smcpn_other_plugin_foot">
		                <a href="https://wordpress.org/plugins/<?php echo $plugin_key;?>/" target="_blank" class="wt_smcpn_other_plugin_foot_install_btn"><?php _e('Download', 'wt-smart-coupons-for-woocommerce');?></a>
		            </div>
		        </div>
				<?php
			}
		}
		$html=ob_get_clean();
		if($html!="")
		{
		?>
			<div class="wt_smcpn_other_plugins_hd"><?php _e('OTHER FREE SOLUTIONS FROM WEBTOFFEE', 'wt-smart-coupons-for-woocommerce');?></div>
		<?php
			echo $html;
		}
	}


	/**
	*	Initiate module
	*/
	public function init()
	{
		/**
		*	Add settings tab
		*/
		add_filter('wt_smart_coupon_admin_tab_items', array( $this, 'settings_tabhead'), 11);
		add_action('wt_smart_coupon_tab_content_'.$this->module_base, array($this, 'out_settings_form'));
		
	}


	/**
	* 	Tab head for admin settings page
	*	
	*/
	public function settings_tabhead($arr)
	{
		$arr[$this->module_base]=__('Free vs. Pro', 'wt-smart-coupons-for-woocommerce');
		return $arr;
	}

	/**
	* 
	*	Tab content
	*/
	public function out_settings_form($args)
	{
		$pro_upgarde_features=array(
		    __('Create and offer coupons based on customers’ purchase history Eg: First-order coupons, next-order, or nth order coupons.
', 'wt-smart-coupons-for-woocommerce'),
		    __('Create and sell store credits coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Email gift cards in beautiful templates', 'wt-smart-coupons-for-woocommerce'),
		    __('Restrict coupons based on country', 'wt-smart-coupons-for-woocommerce'),
		    __('Import coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Bulk generate coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Create signup & abandoned cart coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Create combo coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Coupon style customization', 'wt-smart-coupons-for-woocommerce'),
		    __('Create and display count down discount sales banner', 'wt-smart-coupons-for-woocommerce'),
		    __('Offer give away coupons', 'wt-smart-coupons-for-woocommerce'),
		    __('Add coupon expiry in days', 'wt-smart-coupons-for-woocommerce'),
		);
		
		include plugin_dir_path( __FILE__ ).'views/goto-pro.php';
	}

}
new Wt_Smart_Coupon_Freevspro();