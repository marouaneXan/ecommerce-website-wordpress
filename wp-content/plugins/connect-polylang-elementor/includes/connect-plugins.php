<?php
namespace ConnectPolylangElementor;

use Elementor\Controls_Manager;


defined( 'ABSPATH' ) || exit;


class ConnectPlugins {

	use \ConnectPolylangElementor\Util\Singleton;

	/**
	 * Current template ID.
	 *
	 * @var int|null
	 */
	private $template_id = null;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		// Auto add post types for translation.
		add_filter( 'pll_get_post_types', array( $this, 'add_polylang_post_types' ), 10, 2 );

		// Front template loading.
		add_filter( 'elementor/theme/get_location_templates/template_id', array( $this, 'template_id_translation' ) );
		add_filter( 'elementor/theme/get_location_templates/condition_sub_id', array( $this, 'condition_sub_id_translation' ), 10, 2 );

		// Fix home_url() for site-url Dynamic Tag and Search Form widget.
		add_filter( 'pll_home_url_white_list', array( $this, 'elementor_home_url_white_list' ) );
		add_filter( 'home_url', array( $this, 'home_url_language_dir_slash' ), 11, 2 );

		// Fix search url for Search Form widget.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'add_search_form_home_url_filter' ) );
		add_action( 'elementor/frontend/widget/after_render', array( $this, 'remove_search_form_home_url_filter' ) );

		if ( is_admin() ) {

			// All langs for template conditions & global widgets.
			add_action( 'parse_query', array( $this, 'query_all_languages' ), 1 );

			// Empty template conditions on translations.
			add_filter( 'get_post_metadata', array( $this, 'elementor_conditions_empty_on_translations' ), 10, 3 );
			add_filter( 'pre_update_option_elementor_pro_theme_builder_conditions', array( $this, 'theme_builder_conditions_remove_empty' ) );

			// Global widgets hide language column.
			add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'hide_language_column_pre' ), 9, 2 );
			add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'hide_language_column_pos' ), 11, 2 );

			if ( cpel_is_elementor_pro_active() ) {
				// Update template conditions on language terms change.
				add_action( 'set_object_terms', array( $this, 'update_conditions_on_term_change' ), 10, 4 );

				// Translations conditions column.
				add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'instances_column_pre' ), 9, 2 );
				add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'instances_column_pos' ), 11, 2 );
			}

			// Don't add "_elementor_css" meta.
			add_filter( 'update_post_metadata', array( $this, 'prevent_elementor_css_meta' ), 10, 3 );

		}

		// Elementor editor menu links to translations
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'elementor_editor_script' ) );

		// Elementor Site Editor template tweaks
		add_filter( 'elementor-pro/site-editor/data/template', array( $this, 'elementor_site_editor_template' ) );

	}

	/**
	 * Enable Elementor-specific post types automatically for Polylang translation
	 *
	 * @link   https://polylang.pro/doc/filter-reference/
	 *
	 * @since  2.0.0
	 *
	 * @param array $types The list of post type names for which Polylang manages language and translations
	 * @param bool  $is_settings  True when displaying the list in Polylang settings
	 * @return array The list of post type names for which Polylang manages language and translations
	 */
	function add_polylang_post_types( $types, $is_settings ) {

		$relevant_types = apply_filters(
			'cpel/filter/polylang/post_types',
			array(
				'elementor_library',   // Elementor
				'e-landing-page',      // Elementor Landing pages
				'oceanwp_library',     // OceanWP Library
				'astra-advanced-hook', // Astra Custom Layouts (Astra Pro)
				'gp_elements',         // GeneratePress Elements (GP Premium)
				'jet-theme-core',      // JetThemeCore (Kava Pro/ CrocoBlock)
				'jet-engine',          // JetEngine Listing Item (CrocoBlock)
				'customify_hook',      // Customify (Customify Pro)
				'wpbf_hooks',          // Page Builder Framework Sections (WPBF Premium)
				'ae_global_templates', // AnyWhere Elementor plugin
			)
		);

		return array_merge( $types, array_combine( $relevant_types, $relevant_types ) );

	}

	/**
	 * Query all languages if conditions meets
	 *
	 *   Note: Needs to be priority 1, since Polylang uses the action parse_query
	 *         which is fired before 'pre_get_posts'.
	 *
	 * @link  https://github.com/polylang/polylang/issues/152#issuecomment-320602328
	 * @link  https://github.com/pojome/elementor/issues/4839
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Query $query
	 */
	function query_all_languages( $query ) {

		$global_widget_meta_query = array(
			'key'   => '_elementor_template_type',
			'value' => 'widget',
		);

		$is_elementor_conditions = isset( $query->query_vars['meta_key'] )
			&& '_elementor_conditions' === $query->query_vars['meta_key'];

		$is_global_widget = isset( $query->query_vars['post_type'], $query->query_vars['meta_query'] )
			&& 'elementor_library' === $query->query_vars['post_type']
			&& in_array( $global_widget_meta_query, $query->query_vars['meta_query'] );

		if ( $is_elementor_conditions || $is_global_widget ) {
			$query->set( 'lang', '' );
		}

	}

	/**
	 * Return empty conditions on secondary translations
	 *
	 * @since  2.0.0
	 *
	 * @param  mixed  $null
	 * @param  int    $post_id
	 * @param  string $meta_key
	 * @return mixed null or empty array
	 */
	function elementor_conditions_empty_on_translations( $null, $post_id, $meta_key ) {

		return '_elementor_conditions' === $meta_key && cpel_is_translation( $post_id ) ? array( array() ) : $null;

	}

	/**
	 * Clear empty conditions before save 'elementor_pro_theme_builder_conditions' option
	 *
	 * @since  2.0.0
	 *
	 * @param  array $value array of theme builder conditions
	 * @return array  filtered array
	 */
	function theme_builder_conditions_remove_empty( $value ) {

		foreach ( $value as $location => $items ) {
			$value[ $location ] = array_filter( $items );
		}

		return array_filter( $value );

	}

	/**
	 * Change Elementor template with their translation for the current lanaguage (if exists).
	 *
	 * @link   https://github.com/pojome/elementor/issues/4839
	 *
	 * @since  2.0.0
	 *
	 * @uses   pll_get_post()
	 *
	 * @param  int $post_id ID of the current post
	 * @return string Based translation, the translation ID, or the original Post ID
	 */
	function template_id_translation( $post_id ) {

		$post_id           = pll_get_post( $post_id ) ?: $post_id;
		$this->template_id = $post_id; // Save for check sub_id

		return $post_id;

	}

	/**
	 * Filter Elementor sub_conditions system
	 *
	 * If is translated template that is based on term or post
	 *   return the translation ID of term or post.
	 *
	 * @since  2.0.0
	 *
	 * @uses   pll_get_post()
	 * @uses   pll_get_term()
	 *
	 * @param  int   $sub_id ID of the object in subcondition
	 * @param  array $parsed_condition condition parts
	 * @return int original sub ID or translated ID
	 */
	function condition_sub_id_translation( $sub_id, $parsed_condition ) {

		if ( $sub_id && cpel_is_translation( $this->template_id ) ) {

			if ( in_array( $parsed_condition['sub_name'], get_post_types() ) ) {

				$sub_id = pll_get_post( $sub_id ) ?: $sub_id;

			} else {

				$sub_id = pll_get_term( $sub_id ) ?: $sub_id;

			}
		}

		return $sub_id;

	}

	/**
	 * Update Elementor conditions
	 *
	 * On change post_translations terms on Elementor Library trigger conditions regenerate.
	 *
	 * @since  2.0.0
	 *
	 * @param  mixed $post_id
	 * @param  mixed $terms
	 * @param  mixed $tt_ids
	 * @param  mixed $taxonomy
	 * @return void
	 */
	function update_conditions_on_term_change( $post_id, $terms, $tt_ids, $taxonomy ) {

		if ( 'post_translations' === $taxonomy && 'elementor_library' === get_post_type( $post_id ) ) {

			$theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
			$theme_builder->get_conditions_manager()->get_cache()->regenerate();

		}

	}

	/**
	 * Hide language column info pre
	 *
	 * Wrap language info for Global Widgets with a hidden div (open)
	 *
	 * @since  2.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	function hide_language_column_pre( $column, $post_id ) {

		if ( false !== strpos( $column, 'language_' ) && 'widget' === get_post_meta( $post_id, '_elementor_template_type', true ) ) {
			echo '<span aria-hidden="true">—</span><div class="hidden" aria-hidden="true">';
		}

	}

	/**
	 * Hide language column info pos
	 *
	 * Wrap language info for Global Widgets with a hidden div (close)
	 *
	 * @since  2.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	function hide_language_column_pos( $column, $post_id ) {

		if ( false !== strpos( $column, 'language_' ) && 'widget' === get_post_meta( $post_id, '_elementor_template_type', true ) ) {
			echo '</div>';
		}

	}

	/**
	 * Show default language instances in translations
	 *
	 * (Also wrap "None" with a hidden div)
	 *
	 * @since  2.0.4
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	function instances_column_pre( $column, $post_id ) {

		if ( 'instances' === $column && cpel_is_translation( $post_id ) ) {

			$conditions_manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();
			$instances          = $conditions_manager->get_document_instances( pll_get_post( $post_id, pll_default_language() ) );

			echo '<span style="opacity:.4">' . esc_html( implode( ', ', $instances ) ) . '</span><div class="hidden" aria-hidden="true">';

		}

	}

	/**
	 * Show default language instances in translations (close)
	 *
	 * @since  2.0.4
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	function instances_column_pos( $column, $post_id ) {

		if ( 'instances' === $column && cpel_is_translation( $post_id ) ) {
			echo '</div>';
		}

	}

	/**
	 * Don't copy '_elementor_css' meta on Polylang add new translation
	 *
	 * Without this meta Elementor generates the css for the new post.
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed  $null
	 * @param  int    $post_id
	 * @param  string $meta_key
	 * @return mixed null or false
	 */
	public function prevent_elementor_css_meta( $null, $post_id, $meta_key ) {

		global $pagenow;

		return '_elementor_css' === $meta_key && 'post-new.php' === $pagenow
			&& isset( $_GET['from_post'], $_GET['new_lang'] ) ? false : $null;

	}

	/**
	 * Whitelist Elementor Pro home_url()
	 *
	 * Polylang add home_url() to whitelist for Elementor Pro
	 *   "Search Form" widget and "Site Url" dynamic tag.
	 *
	 * @since  2.0.0
	 *
	 * @param  array $white_list
	 * @return array
	 */
	function elementor_home_url_white_list( $white_list ) {

		$white_list[] = array( 'file' => 'site-url.php' );

		return $white_list;

	}

	/**
	 * Language subdir add trailing slash
	 *
	 * @since  2.0.0
	 *
	 * @param  string $url
	 * @param  string $path
	 * @return string
	 */
	function home_url_language_dir_slash( $url, $path ) {

		return empty( $path ) && ! is_admin() && $url !== get_option( 'home' )
			&& function_exists( 'PLL' ) && 1 === PLL()->options['force_lang'] ? trailingslashit( $url ) : $url;

	}

	/**
	 * Replace home_url with correct language search url
	 *
	 * Only for Elementor Search Form that uses home_url() in form action.
	 *
	 * @since 2.0.6
	 *
	 * @param  string $url
	 * @param  string $path
	 * @return string
	 */
	function search_form_home_url_filter( $url, $path ) {

		return function_exists( 'PLL' ) ? PLL()->curlang->search_url : $url;

	}

	/**
	 * Add home_url() filter before render Search Form
	 *
	 * @since 2.0.6
	 *
	 * @param  Element_Base $element
	 * @return void
	 */
	function add_search_form_home_url_filter( $element ) {

		if ( 'search-form' === $element->get_name() ) {
			add_filter( 'home_url', array( $this, 'search_form_home_url_filter' ), 10, 2 );
		}

	}

	/**
	 * Remove home_url() filter after render Search Form
	 *
	 * @since 2.0.6
	 *
	 * @param  Element_Base $element
	 * @return void
	 */
	function remove_search_form_home_url_filter( $element ) {

		if ( 'search-form' === $element->get_name() ) {
			remove_filter( 'home_url', array( $this, 'search_form_home_url_filter' ) );
		}

	}

	/**
	 * Elementor editor script
	 *
	 * Add script with links to translations on Elementor editor panel.
	 *
	 * @since  2.0.0
	 *
	 * @return void
	 */
	function elementor_editor_script() {

		global $typenow, $post;

		// If is post type translatable
		if ( pll_is_translated_post_type( $typenow ) ) {

			$languages    = pll_languages_list( array( 'fields' => '' ) );
			$translations = pll_get_post_translations( $post->ID );
			$current      = pll_get_post_language( $post->ID, 'name' );

			$items = array();
			foreach ( $languages as $language ) {
				if ( $language->name !== $current ) {
					if ( isset( $translations[ $language->slug ] ) ) {

						$translation_id = $translations[ $language->slug ];
						$link           = get_edit_post_link( $translation_id, 'edit' );

						if ( get_post_meta( $translation_id, '_elementor_edit_mode', true ) ) {
							$link = add_query_arg( 'action', 'elementor', $link );
						}

						$items[] = array(
							'name'  => "cpel-{$language->slug}",
							'icon'  => 'eicon-globe',
							'title' => sprintf( '%s (%s)', get_the_title( $translation_id ), $language->slug ),
							'type'  => 'link',
							'link'  => $link,
						);
					} else {

						$args = array(
							'post_type' => $typenow,
							'from_post' => $post->ID,
							'new_lang'  => $language->slug,
							'_wpnonce'  => wp_create_nonce( 'new-post-translation' ),
						);

						$link = add_query_arg( $args, admin_url( 'post-new.php' ) );

						$items[] = array(
							'name'  => "cpel-{$language->slug}",
							'icon'  => 'eicon-plus',
							'title' => sprintf( __( 'Add a translation in %s', 'polylang' ), $language->name ),
							'type'  => 'link',
							'link'  => $link,
						);
					}
				}
			}

			$group = array(
				'name'  => 'cpel',
				'title' => sprintf( __( 'This item is in %s', 'polylang' ), $current ),
				'items' => $items,
			);

			$script = 'jQuery(window).on("elementor:init", () => {
				window.elementor.on("panel:init", () => {
					setTimeout(() => { window.elementor.modules.layouts.panel.pages.menu.Menu.groups.add(' . json_encode( $group ) . '); });
				});
			});';

			// Add after Elementor editor script.
			wp_add_inline_script( 'elementor-editor', $script );

		}

	}

	/**
	 * Elementor Site Editor template changes
	 *
	 * at 2.0.0 named "elementor_theme_editor_title"
	 *
	 * @since  2.0.4
	 *
	 * @param  array $data
	 * @return array
	 */
	function elementor_site_editor_template( $data ) {

		$post_id = $data['id'];

		// Add lang info to title.
		$data['title'] = sprintf( '%s / %s', $data['title'], strtoupper( pll_get_post_language( $post_id ) ) );

		// Show default language instances in translations (and recalc isActive).
		if ( cpel_is_translation( $post_id ) ) {

			$language = pll_default_language();

			$conditions_manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();
			$instances          = $conditions_manager->get_document_instances( pll_get_post( $post_id, $language ) );

			if ( empty( $instances ) ) {
				$instances = array( 'no_instances' => esc_html__( 'No instances', 'elementor-pro' ) );
				$is_active = false;
			} else {
				$is_active = 'publish' === $data['status'];
			}

			$data['instances'] = array( 'cpel' => sprintf( esc_html__( '(from %s)', 'connect-polylang-elementor' ), strtoupper( $language ) ) ) + $instances;
			$data['isActive']  = $is_active;
		}

		return $data;

	}

}
