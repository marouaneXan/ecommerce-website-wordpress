<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Modules\DynamicTags\Module;
use Elementor\Core\DynamicTags\Data_Tag;

defined( 'ABSPATH' ) || exit;


class LanguageUrl extends Data_Tag {

	use TagTrait;

	public function get_name() {
		return 'language-url';
	}

	public function get_title() {
		return _x( 'Language URL', 'Elementor Dynamic Tag title', 'connect-polylang-elementor' );
	}

	public function get_categories() {
		return array( Module::URL_CATEGORY );
	}

	public function get_panel_template() {
		return ' ({{ url }})';
	}

	public function get_value( array $options = array() ) {
		return $this->get_language_field( 'url' );
	}

}
