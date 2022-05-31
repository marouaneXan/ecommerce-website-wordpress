<?php
namespace ConnectPolylangElementor\Widgets;

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;


class Manager {

	use \ConnectPolylangElementor\Util\Singleton;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widget' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'register_styles' ) );

	}

	/**
	 * Register widget
	 *
	 * @since 2.0.0
	 *
	 * @access private
	 */
	public function register_widget() {

		Plugin::instance()->widgets_manager->register_widget_type( new PolylangLanguageSwitcher() );

	}

	/**
	 * Register widget styles
	 *
	 * @since  2.0.0
	 *
	 * @return void
	 */
	public function register_styles() {

		$script = '/assets/css/language-switcher.' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'css' : 'min.css' );

		wp_register_style( 'cpel-language-switcher', plugins_url( $script, CPEL_FILE ), '', CPEL_PLUGIN_VERSION );

	}

}
