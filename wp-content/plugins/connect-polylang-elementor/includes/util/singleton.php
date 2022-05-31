<?php
namespace ConnectPolylangElementor\Util;

defined( 'ABSPATH' ) || exit;


trait Singleton {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Instantiates Manager.
	 *
	 * @return Manager
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
