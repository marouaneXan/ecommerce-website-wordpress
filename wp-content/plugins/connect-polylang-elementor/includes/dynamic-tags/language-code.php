<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

defined( 'ABSPATH' ) || exit;


class LanguageCode extends Tag {

	use TagTrait;

	public function get_name() {
		return 'language-code';
	}

	public function get_title() {
		return _x( 'Language Code', 'Elementor Dynamic Tag title', 'connect-polylang-elementor' );
	}

	public function get_categories() {
		return array( Module::TEXT_CATEGORY );
	}

	public function render() {
		echo wp_kses_post( $this->get_language_field( 'slug' ) );
	}

}
