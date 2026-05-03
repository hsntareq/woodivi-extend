<?php
/**
 * Admin Hooks
 *
 * @package WooDiviExtended\Admin
 */

namespace WooDiviExtended\Admin;

use WooDiviExtended\Loader;

/**
 * Admin Hooks
 *
 * Handles all admin-related hooks
 */
class AdminHooks {

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
		// Admin enqueue scripts and styles
		$this->loader->add_action(
			'admin_enqueue_scripts',
			$this,
			'enqueue_admin_assets'
		);

		// Admin menu
		$this->loader->add_action(
			'admin_menu',
			$this,
			'add_admin_menu'
		);

		// Admin settings
		$this->loader->add_action(
			'admin_init',
			$this,
			'register_settings'
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only load on specific admin pages
		if ( strpos( $hook_suffix, 'woodivi-extend' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'woodivi-extend-admin',
			WOO_DIVI_EXTENDED_URL . 'assets/css/admin.css',
			array(),
			WOO_DIVI_EXTENDED_VERSION
		);

		wp_enqueue_script(
			'woodivi-extend-admin',
			WOO_DIVI_EXTENDED_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			WOO_DIVI_EXTENDED_VERSION,
			true
		);
	}

	/**
	 * Add admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'WooDivi Extend', 'woodivi-extend' ),
			__( 'WooDivi Extend', 'woodivi-extend' ),
			'manage_options',
			'woodivi-extend',
			array( $this, 'render_admin_page' ),
			'dashicons-admin-generic',
			25
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'woodivi_extend_filterable_portfolio',
			'woodivi_extend_filterable_portfolio',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_filterable_portfolio_settings' ),
				'default'           => $this->get_default_filterable_portfolio_settings(),
			)
		);
	}

	/**
	 * Get default filterable portfolio settings.
	 *
	 * @return array
	 */
	private function get_default_filterable_portfolio_settings() {
		return array(
			'enabled'        => 0,
			'show_counts'    => 1,
			'show_label'     => 1,
			'checkbox_label' => __( 'Refine by sub category', 'woodivi-extend' ),
		);
	}

	/**
	 * Sanitize filterable portfolio settings.
	 *
	 * @param array $settings Raw settings.
	 *
	 * @return array
	 */
	public function sanitize_filterable_portfolio_settings( $settings ) {
		$defaults = $this->get_default_filterable_portfolio_settings();
		$settings = is_array( $settings ) ? $settings : array();

		return array(
			'enabled'        => ! empty( $settings['enabled'] ) ? 1 : 0,
			'show_counts'    => ! empty( $settings['show_counts'] ) ? 1 : 0,
			'show_label'     => ! empty( $settings['show_label'] ) ? 1 : 0,
			'checkbox_label' => isset( $settings['checkbox_label'] ) ? sanitize_text_field( $settings['checkbox_label'] ) : $defaults['checkbox_label'],
		);
	}

	/**
	 * Get admin modules.
	 *
	 * Add new modules here, or extend this list with the
	 * woodivi_extend_admin_modules filter.
	 *
	 * @return array
	 */
	private function get_admin_modules() {
		$modules = array(
			'dashboard' => array(
				'label'       => __( 'Dashboard', 'woodivi-extend' ),
				'title'       => __( 'Dashboard', 'woodivi-extend' ),
				'description' => __( 'Overview and quick links for WooDivi Extend modules.', 'woodivi-extend' ),
				'view'        => WOO_DIVI_EXTENDED_PATH . 'views/admin/modules/dashboard.php',
			),
			'filterable-portfolio' => array(
				'label'       => __( 'Filterable Portfolio', 'woodivi-extend' ),
				'title'       => __( 'Filterable Portfolio Settings', 'woodivi-extend' ),
				'description' => __( 'Add inline child category checkboxes to Divi filterable portfolio tabs.', 'woodivi-extend' ),
				'view'        => WOO_DIVI_EXTENDED_PATH . 'views/admin/modules/filterable-portfolio.php',
			),
			'text-slider' => array(
				'label'       => __( 'Text Slider', 'woodivi-extend' ),
				'title'       => __( 'Text Slider Settings', 'woodivi-extend' ),
				'description' => __( 'Configure the Text Slider module extension settings.', 'woodivi-extend' ),
				'view'        => WOO_DIVI_EXTENDED_PATH . 'views/admin/modules/text-slider.php',
			),
		);

		return apply_filters( 'woodivi_extend_admin_modules', $modules );
	}

	/**
	 * Get current module slug from the admin URL.
	 *
	 * @param array $modules Registered modules.
	 *
	 * @return string
	 */
	private function get_current_module_slug( array $modules ) {
		$current_module = isset( $_GET['module'] ) ? sanitize_key( wp_unslash( $_GET['module'] ) ) : 'dashboard';

		if ( ! isset( $modules[ $current_module ] ) ) {
			return 'dashboard';
		}

		return $current_module;
	}

	/**
	 * Render admin page
	 *
	 * @return void
	 */
	public function render_admin_page() {
		$modules             = $this->get_admin_modules();
		$current_module_slug = $this->get_current_module_slug( $modules );
		$current_module      = $modules[ $current_module_slug ];

		$file = WOO_DIVI_EXTENDED_PATH . 'views/admin-page.php';
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}
