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
	 * Track hooks that attempted Apex27 Divi 5 registration.
	 *
	 * @var array
	 */
	private $apex27_registration_hooks = array();

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

		$this->loader->add_action(
			'init',
			$this,
			'register_apex27_divi5_modules',
			20
		);

		$this->loader->add_action(
			'et_builder_ready',
			$this,
			'register_apex27_divi5_modules'
		);

		$this->loader->add_action(
			'admin_init',
			$this,
			'register_apex27_divi5_modules'
		);

		// Specific hook for Divi 5 Module Library registration.
		$this->loader->add_action(
			'divi_module_library_register_modules',
			$this,
			'register_apex27_divi5_modules'
		);

		$this->loader->add_action(
			'et_builder_ready',
			$this,
			'register_apex27_legacy_divi_modules'
		);

		$this->loader->add_action(
			'divi_visual_builder_assets_before_enqueue_scripts',
			$this,
			'enqueue_divi_visual_builder_assets'
		);

		$this->loader->add_action(
			'wp_enqueue_scripts',
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

		$this->loader->add_action(
			'admin_notices',
			$this,
			'maybe_render_apex27_registration_debug_notice'
		);
	}

	/**
	 * Register native Apex27 Divi 5 modules.
	 *
	 * @return void
	 */
	public function register_apex27_divi5_modules() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Apex27: register_apex27_divi5_modules called on hook: ' . current_filter() );
		}
		$this->apex27_registration_hooks[] = current_filter();

		\WooDiviExtended\Divi5\Apex27ListingsModule::load();
		\WooDiviExtended\Divi5\Apex27SearchFormModule::load();
		\WooDiviExtended\Divi5\DemoModule::load();
	}

	/**
	 * Register legacy Apex27 Divi Builder modules for Divi 4 mode.
	 *
	 * @return void
	 */
	public function register_apex27_legacy_divi_modules() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Apex27: register_apex27_legacy_divi_modules called on hook: ' . current_filter() );
		}
		if ( function_exists( 'et_builder_d5_enabled' ) && et_builder_d5_enabled() ) {
			return;
		}

		if ( ! class_exists( '\ET_Builder_Module' ) ) {
			return;
		}

		new \WooDiviExtended\DiviModules\Apex27Listings();
		new \WooDiviExtended\DiviModules\Apex27SearchForm();
	}

	/**
	 * Enqueue frontend scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		$filterable_portfolio_settings = $this->get_filterable_portfolio_settings();

		wp_enqueue_style(
			'woodivi-extend-frontend',
			WOO_DIVI_EXTENDED_URL . 'assets/css/frontend.css',
			array(),
			WOO_DIVI_EXTENDED_VERSION
		);

		wp_enqueue_script(
			'woodivi-extend-frontend',
			WOO_DIVI_EXTENDED_URL . 'assets/js/frontend.js',
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
		wp_register_script(
			'woodivi-extend-divi5-apex27',
			WOO_DIVI_EXTENDED_URL . 'assets/js/divi5-apex27-modules.js',
			array( 'lodash', 'divi-vendor-wp-hooks', 'divi-vendor-wp-i18n' ),
			WOO_DIVI_EXTENDED_VERSION,
			true
		);

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

		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			array(
				'name'    => 'woodivi-extend-divi5-apex27',
				'version' => WOO_DIVI_EXTENDED_VERSION,
				'script'  => array(
					'src'                => WOO_DIVI_EXTENDED_URL . 'assets/js/divi5-apex27-modules.js',
					'deps'               => array(
						'lodash',
						'divi-vendor-wp-hooks',
						'divi-vendor-wp-i18n',
						'divi-module-library',
						'divi-module',
						'react',
					),
					'enqueue_top_window' => false,
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

		if ( 'on' !== $show_subcategories ) {
			return $module_attrs;
		}

		$existing_class = $module_attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '';
		$classes        = array_filter( preg_split( '/\s+/', $existing_class ) );
		$classes[]      = 'woodivi-filterable-portfolio-subcategories';

		$module_attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] = implode( ' ', array_unique( $classes ) );

		return $module_attrs;
	}

	/**
	 * Render temporary admin diagnostics for Apex27 registration.
	 *
	 * @return void
	 */
	public function maybe_render_apex27_registration_debug_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! filter_input( INPUT_GET, 'woodivi_apex27_debug', FILTER_DEFAULT ) ) {
			return;
		}

		$registry = \WP_Block_Type_Registry::get_instance();
		$d5_state = 'unavailable';

		if ( function_exists( 'et_builder_d5_enabled' ) ) {
			$d5_state = et_builder_d5_enabled() ? 'on' : 'off';
		}

		$messages = array(
			'Divi 5 enabled: ' . $d5_state,
			'ModuleRegistration class exists: ' . ( class_exists( '\\ET\\Builder\\Packages\\ModuleLibrary\\ModuleRegistration' ) ? 'yes' : 'no' ),
			'divi_module_library_register_module function exists: ' . ( function_exists( 'divi_module_library_register_module' ) ? 'yes' : 'no' ),
			'Block registered (woodivi/apex27-listings): ' . ( $registry->is_registered( 'woodivi/apex27-listings' ) ? 'yes' : 'no' ),
			'Block registered (woodivi/apex27-search-form): ' . ( $registry->is_registered( 'woodivi/apex27-search-form' ) ? 'yes' : 'no' ),
			'Registration hooks fired: ' . ( empty( $this->apex27_registration_hooks ) ? 'none' : implode( ', ', array_unique( $this->apex27_registration_hooks ) ) ),
			'VB Compatibility Script enqueued: ' . ( wp_script_is( 'woodivi-extend-divi5-apex27', 'enqueued' ) ? 'yes' : 'no' ),
		);

		echo '<div class="notice notice-info"><p><strong>Apex27 Divi Registration Debug</strong></p><ul style="margin:0 0 0 18px;">';

		foreach ( $messages as $message ) {
			echo '<li>' . esc_html( $message ) . '</li>';
		}

		echo '</ul></div>';
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
		// Try a list of commonly used taxonomy names to maximize compatibility.
		$possible_taxonomies = array(
			'project_category',
			'project_cat',
			'portfolio_category',
			'portfolio_cat',
			'category',
		);

		$terms = array();
		$found_taxonomy = null;

		foreach ( $possible_taxonomies as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}

			$maybe = get_terms(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $maybe ) || empty( $maybe ) ) {
				continue;
			}

			$terms = $maybe;
			$found_taxonomy = $tax;
			break;
		}

		if ( empty( $terms ) ) {
			return array();
		}

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
