<?php
/**
 * Frontend Hooks
 *
 * @package WooDiviExtended\Frontend
 */

namespace WooDiviExtended\Frontend;

use WooDiviExtended\Loader;

/**
 * Frontend Hooks
 *
 * Handles all frontend-related hooks
 */
class FrontendHooks {

	/**
	 * Loader instance
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Constructor
	 *
	 * @param Loader $loader The loader instance.
	 */
	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register() {
		// Frontend enqueue scripts and styles.
		$this->loader->add_action(
			'wp_enqueue_scripts',
			$this,
			'enqueue_frontend_assets'
		);

		if ( ! is_admin() ) {
			$this->loader->add_filter(
				'the_content',
				$this,
				'modify_content',
				20,
				1
			);
		}

		// Divi 5 Visual Builder module setting.
		$this->loader->add_action(
			'divi_visual_builder_assets_before_enqueue_scripts',
			$this,
			'enqueue_divi_visual_builder_assets'
		);

		// Divi 5 frontend render attributes.
		$this->loader->add_filter(
			'divi_module_library_register_module_attrs',
			$this,
			'add_filterable_portfolio_render_attrs',
			10,
			2
		);

		$this->loader->add_filter(
			'divi_module_library_module_default_attributes_divi/filterable-portfolio',
			$this,
			'add_filterable_portfolio_default_attrs',
			10,
			2
		);

		// Modify module attributes metadata so includeCategories lists parents and
		// provide a separate subCategories field for child terms.
		$this->loader->add_filter(
			'divi_module_library_register_module_attrs',
			$this,
			'filter_module_attrs_for_subcategories',
			15,
			2
		);
	}

	/**
	 * Filter module attributes to split parent and child categories into separate fields.
	 *
	 * @param array $module_attrs Module attributes metadata.
	 * @param array $filter_args  Filter context (contains module name).
	 *
	 * @return array
	 */
	public function filter_module_attrs_for_subcategories( $module_attrs, $filter_args ) {
		$module_name = isset( $filter_args['name'] ) ? $filter_args['name'] : '';

		if ( 'divi/filterable-portfolio' !== $module_name ) {
			return $module_attrs;
		}

		// Support multiple potential locations where Divi may place the includedCategories
		$locations = array(
			array( 'portfolio', 'content', 'includedCategories' ),
			array( 'portfolio', 'innerContent', 'includedCategories' ),
			array( 'portfolio', 'settings', 'content', 'includedCategories' ),
		);

		foreach ( $locations as $loc ) {
			$node = $module_attrs;
			$exists = true;
			foreach ( $loc as $key ) {
				if ( ! isset( $node[ $key ] ) ) {
					$exists = false;
					break;
				}
				$node = $node[ $key ];
			}

			if ( ! $exists ) {
				continue;
			}

			$options = $node['item']['component']['props']['options'] ?? null;
			if ( empty( $options ) || ! is_array( $options ) ) {
				continue;
			}

			$parents = array();
			$children = array();

			foreach ( $options as $opt ) {
				if ( isset( $opt['parent'] ) ) {
					if ( (int) $opt['parent'] === 0 ) {
						$parents[] = $opt;
					} else {
						$children[] = $opt;
					}
				} else {
					$parents[] = $opt;
				}
			}

			// Set parents back into module_attrs at the same location.
			$ref = &$module_attrs;
			while ( count( $loc ) > 1 ) {
				$key = array_shift( $loc );
				$ref = &$ref[ $key ];
			}
			$last = array_shift( $loc );
			$ref[ $last ]['item']['component']['props']['options'] = array_values( $parents );

			// If children exist, add subCategories next to the same parent location.
			if ( ! empty( $children ) ) {
				$subField = $ref[ $last ];
				$subField['item']['attrName'] = str_replace( 'includedCategories', 'subCategories', $subField['item']['attrName'] ?? 'portfolio.content.subCategories' );
				$subField['item']['label'] = __( 'Sub Categories', 'woodivi-extend' );
				$subField['item']['description'] = __( 'Select which sub-categories to include.', 'woodivi-extend' );
				$subField['item']['component']['props']['options'] = array_values( $children );

				// Place subCategories under the same containing array (e.g., portfolio.content.subCategories)
				$container = &$module_attrs['portfolio'];
				if ( isset( $container['content'] ) ) {
					$container['content']['subCategories'] = $subField;
				} elseif ( isset( $container['innerContent'] ) ) {
					$container['innerContent']['subCategories'] = $subField;
				} elseif ( isset( $container['settings']['content'] ) ) {
					$container['settings']['content']['subCategories'] = $subField;
				}
			}
		}

		return $module_attrs;
	}

	/**
	 * Enqueue frontend scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		$filterable_portfolio_settings = $this->get_filterable_portfolio_settings();

		// Prefer minified assets when present (production build)
		$css_file = WOO_DIVI_EXTENDED_PATH . 'assets/css/frontend.min.css';
		$css_url = WOO_DIVI_EXTENDED_URL . 'assets/css/frontend.css';
		if ( file_exists( $css_file ) ) {
			$css_url = WOO_DIVI_EXTENDED_URL . 'assets/css/frontend.min.css';
		}

		$js_file = WOO_DIVI_EXTENDED_PATH . 'assets/js/frontend.min.js';
		$js_url = WOO_DIVI_EXTENDED_URL . 'assets/js/frontend.js';
		if ( file_exists( $js_file ) ) {
			$js_url = WOO_DIVI_EXTENDED_URL . 'assets/js/frontend.min.js';
		}

		wp_enqueue_style(
			'woodivi-extend-frontend',
			$css_url,
			array(),
			WOO_DIVI_EXTENDED_VERSION
		);

		wp_enqueue_script(
			'woodivi-extend-frontend',
			$js_url,
			array( 'jquery' ),
			WOO_DIVI_EXTENDED_VERSION,
			true
		);

		// Localize script for passing data to JavaScript
		wp_localize_script(
			'woodivi-extend-frontend',
			'wooDiviExtended',
			array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'woodivi-extend-nonce' ),
				'filterablePortfolio' => array(
					'enabled'       => (bool) $filterable_portfolio_settings['enabled'],
					'showCounts'    => (bool) $filterable_portfolio_settings['show_counts'],
					'showLabel'     => (bool) $filterable_portfolio_settings['show_label'],
					'checkboxLabel' => $filterable_portfolio_settings['checkbox_label'],
					'categories'    => $this->get_project_category_tree(),
				),
			)
		);
	}

	/**
	 * Enqueue Divi 5 Visual Builder assets.
	 *
	 * @return void
	 */
	public function enqueue_divi_visual_builder_assets() {
		if (
			! function_exists( 'et_builder_d5_enabled' )
			|| ! function_exists( 'et_core_is_fb_enabled' )
			|| ! et_builder_d5_enabled()
			|| ! et_core_is_fb_enabled()
			|| ! class_exists( '\ET\Builder\VisualBuilder\Assets\PackageBuildManager' )
		) {
			return;
		}

		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			array(
				'name'    => 'woodivi-extend-divi5-filterable-portfolio',
				'version' => WOO_DIVI_EXTENDED_VERSION,
				'script'  => array(
					'src'                => WOO_DIVI_EXTENDED_URL . 'assets/js/divi5-filterable-portfolio.js',
					'deps'               => array(
						'lodash',
						'divi-vendor-wp-hooks',
						'divi-vendor-wp-i18n',
					),
					'enqueue_top_window' => true,
					'enqueue_app_window' => true,
					'args'               => array(
						'in_footer' => false,
					),
				),
				'style'   => array(
					'enqueue_top_window' => false,
					'enqueue_app_window' => false,
				),
			)
		);
	}

	/**
	 * Add default Divi 5 render attributes for the custom module setting.
	 *
	 * @param array $default_attrs Default attributes.
	 * @param array $metadata      Module metadata.
	 *
	 * @return array
	 */
	public function add_filterable_portfolio_default_attrs( $default_attrs, $metadata ) {
		$default_attrs['portfolio']['advanced']['showSubcategories'] = array(
			'desktop' => array(
				'value' => 'off',
			),
		);

		return $default_attrs;
	}

	/**
	 * Add frontend classes/data attributes when the module-level setting is enabled.
	 *
	 * @param array $module_attrs Module render attributes.
	 * @param array $filter_args  Filter context.
	 *
	 * @return array
	 */
	public function add_filterable_portfolio_render_attrs( $module_attrs, $filter_args ) {
		$module_name = isset( $filter_args['name'] ) ? $filter_args['name'] : '';

		if ( 'divi/filterable-portfolio' !== $module_name ) {
			return $module_attrs;
		}

		$show_subcategories = $module_attrs['portfolio']['advanced']['showSubcategories']['desktop']['value'] ?? 'off';

		// Module-level option to hide child categories from the Included Categories list.
		$hide_child = $module_attrs['portfolio']['content']['hideChildCategories']['desktop']['value'] ?? 'off';

		if ( 'on' !== $show_subcategories ) {
			return $module_attrs;
		}

		$existing_class = $module_attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '';
		$classes        = array_filter( preg_split( '/\s+/', $existing_class ) );
		$classes[]      = 'woodivi-filterable-portfolio-subcategories';

		if ( 'on' === $hide_child ) {
			$classes[] = 'woodivi-hide-child-categories';
			$module_attrs['module']['advanced']['htmlAttributes']['desktop']['value']['data-hide-child-categories'] = '1';
		}

		$module_attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] = implode( ' ', array_unique( $classes ) );

		return $module_attrs;
	}

	/**
	 * Get filterable portfolio settings.
	 *
	 * @return array
	 */
	private function get_filterable_portfolio_settings() {
		$defaults = array(
			'enabled'        => 0,
			'show_counts'    => 1,
			'show_label'     => 1,
			'checkbox_label' => __( 'Refine by sub category', 'woodivi-extend' ),
		);

		return wp_parse_args( get_option( 'woodivi_extend_filterable_portfolio', array() ), $defaults );
	}

	/**
	 * Get project category hierarchy for Divi portfolio filters.
	 *
	 * @return array
	 */
	private function get_project_category_tree() {
		if ( ! taxonomy_exists( 'project_category' ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'project_category',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$parents = array();

		foreach ( $terms as $term ) {
			if ( 0 === (int) $term->parent ) {
				$parents[ $term->term_id ] = array(
					'id'       => (int) $term->term_id,
					'name'     => $term->name,
					'slug'     => $term->slug,
					'children' => array(),
				);
			}
		}

		foreach ( $terms as $term ) {
			if ( 0 === (int) $term->parent || ! isset( $parents[ $term->parent ] ) ) {
				continue;
			}

			$parents[ $term->parent ]['children'][] = array(
				'id'    => (int) $term->term_id,
				'name'  => $term->name,
				'slug'  => $term->slug,
				'count' => (int) $term->count,
			);
		}

		return array_values( $parents );
	}

	/**
	 * Modify content
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public function modify_content( $content ) {
		// Add your content modifications here
		return $content;
	}
}
