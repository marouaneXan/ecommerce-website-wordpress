<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;


trait TagTrait {

	final public function get_group() {
		return Manager::TAG_GROUP;
	}

	protected function register_controls() {

		$languages = pll_the_languages( array( 'raw' => 1 ) );
		$options   = array( 'current' => __( 'Current Language', 'connect-polylang-elementor' ) );

		if ( is_array( $languages ) ) {
			foreach ( $languages as $language ) {
				$options[ $language['slug'] ] = $language['name'];
			}
		}

		$this->add_control(
			'language',
			array(
				'label'   => __( 'Language', 'polylang' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'current',
			)
		);

	}

	protected function get_language_field( $field ) {

		$settings = $this->get_settings();

		$language = $settings['language'];
		$value    = '';

		$languages = pll_the_languages( array( 'raw' => 1 ) );

		if ( is_array( $languages ) ) {
			if ( 'current' === $language ) {
				foreach ( $languages as $lang ) {
					if ( $lang['current_lang'] ) {
						$value = $lang[ $field ];
						break;
					}
				}
			} elseif ( isset( $languages[ $language ] ) ) {
				$value = $languages[ $language ][ $field ];
			}
		}

		return $value;

	}

}
