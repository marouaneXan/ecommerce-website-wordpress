<?php
/**
 * @package           ConnectPolylangElementor
 * @license           GPL-2.0-or-later
 * @link              https://wordpress.org/plugins/connect-polylang-elementor/
 *
 * @wordpress-plugin
 * Plugin Name:       Polylang Connect for Elementor
 * Plugin URI:        https://github.com/creame/connect-polylang-elementor
 * Description:       Connect Polylang with Elementor. Display templates in the correct language, language switcher widget, language visibility conditions and dynamic tags.
 * Version:           2.0.8
 * Author:            Creame
 * Author URI:        https://crea.me/
 * License:           GPL-2.0-or-later
 * License URI:       https://opensource.org/licenses/GPL-2.0
 * Text Domain:       connect-polylang-elementor
 * Domain Path:       /languages/
 * Requires WP:       5.4
 * Requires PHP:      5.6
 *
 * Copyright (c) 2021 Paco Toledo - CREAME
 * Copyright (c) 2018-2021 David Decker - DECKERWEB
 */
namespace ConnectPolylangElementor;

defined( 'ABSPATH' ) || exit;


/**
 * Setting constants.
 *
 * @since 2.0.0
 */
define( 'CPEL_PLUGIN_VERSION', '2.0.8' );
define( 'CPEL_FILE', __FILE__ );
define( 'CPEL_DIR', plugin_dir_path( CPEL_FILE ) );
define( 'CPEL_BASENAME', plugin_basename( CPEL_FILE ) );


/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the plugin.
 *
 * @since 2.0.0
 */
spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__; // project-specific namespace prefix
		$base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix

		// Does the class use the namespace prefix? No, move to the next registered autoloader
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class_name = substr( $class, $len );

		// Replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name,
		// append with .php and transform CamelCase to lower-dashed
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class_name );
		$file = strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $file ) );
		$path = $base_dir . $file . '.php';

		if ( file_exists( $path ) ) {
			require $path;
		}
	}
);


// Initialize plugin
add_action( 'plugins_loaded', 'ConnectPolylangElementor\\setup', 20 );
add_action( 'init', 'ConnectPolylangElementor\\load_textdomain' );


/**
 * Plugin setup.
 *
 * @since 2.0.0
 *
 * @return void
 */
function setup() {

	require CPEL_DIR . 'includes/functions.php';

	if ( cpel_is_polylang_api_active() && cpel_is_elementor_active() ) {

		ConnectPlugins::instance();
		LanguageVisibility::instance();
		DynamicTags\Manager::instance();
		Finder\Manager::instance();
		Widgets\Manager::instance();

	}

	if ( is_admin() || is_network_admin() ) {

		AdminExtras::instance();

	}
}

/**
 * Load textdomain.
 *
 * @since 2.0.0
 *
 * @return void
 */
function load_textdomain() {

	load_plugin_textdomain( 'connect-polylang-elementor', false, dirname( CPEL_BASENAME ) . '/languages' );

}

