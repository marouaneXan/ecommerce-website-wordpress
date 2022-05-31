<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The public/admin-facing functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.3.5
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/common
 */

if( ! class_exists ( 'Wt_Smart_Coupon_Common' ) ) {
    class Wt_Smart_Coupon_Common {

        /**
         * The ID of this plugin.
         *
         * @since    1.3.5
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.3.5
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /*
         * module list, Module folder and main file must be same as that of module name
         * Please check the `register_modules` method for more details
         */
        public static $modules=array(
            'coupon_category',
            'coupon_shortcode',
        );

        public static $existing_modules=array();

        private static $instance = null;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.3.5
         * @param      string    $plugin_name       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
   
        }

        /**
         * Get Instance
         * @since 1.3.5
         */
        public static function get_instance($plugin_name, $version)
        {
            if(self::$instance==null)
            {
                self::$instance=new Wt_Smart_Coupon_Common($plugin_name, $version);
            }

            return self::$instance;
        }

        /**
         *  Registers modules    
         *  @since 1.3.5     
         */
        public function register_modules()
        {            
            Wt_Smart_Coupon::register_modules(self::$modules, 'wt_sc_common_modules', plugin_dir_path( __FILE__ ), self::$existing_modules);          
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