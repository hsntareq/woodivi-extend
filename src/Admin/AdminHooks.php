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

		register_setting(
			'woodivi_extend_apex27',
			'woodivi_extend_apex27',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_apex27_settings' ),
				'default'           => $this->get_default_apex27_settings(),
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
	 * Get default Apex27 settings.
	 *
	 * @return array
	 */
	private function get_default_apex27_settings() {
		return array(
			'api_key'           => '',
			'base_url'          => 'https://api.apex27.co.uk',
			'listings_endpoint' => '/listings',
			'sales_endpoint'    => '/offers',
			'api_key_header'    => 'x-api-key',
			'auth_scheme'       => 'none',
			'api_key_param'     => '',
			'timeout'           => 20,
			'cache_minutes'     => 15,
		);
	}

	/**
	 * Sanitize Apex27 settings.
	 *
	 * @param array $settings Raw settings.
	 *
	 * @return array
	 */
	public function sanitize_apex27_settings( $settings ) {
		$defaults = $this->get_default_apex27_settings();
		$settings = is_array( $settings ) ? $settings : array();

		$base_url = isset( $settings['base_url'] ) ? esc_url_raw( trim( $settings['base_url'] ) ) : $defaults['base_url'];
		$base_url = untrailingslashit( $base_url );

		$listings_endpoint = isset( $settings['listings_endpoint'] ) ? sanitize_text_field( $settings['listings_endpoint'] ) : $defaults['listings_endpoint'];
		$sales_endpoint    = isset( $settings['sales_endpoint'] ) ? sanitize_text_field( $settings['sales_endpoint'] ) : $defaults['sales_endpoint'];
		$api_key_header    = isset( $settings['api_key_header'] ) ? sanitize_text_field( $settings['api_key_header'] ) : $defaults['api_key_header'];
		$auth_scheme       = isset( $settings['auth_scheme'] ) ? sanitize_key( $settings['auth_scheme'] ) : $defaults['auth_scheme'];
		$api_key_param     = isset( $settings['api_key_param'] ) ? sanitize_key( $settings['api_key_param'] ) : '';
		$timeout           = max( 5, absint( $settings['timeout'] ?? $defaults['timeout'] ) );

		if ( ! in_array( $auth_scheme, array( 'none', 'bearer', 'token' ), true ) ) {
			$auth_scheme = $defaults['auth_scheme'];
		}

		return array(
			'api_key'           => isset( $settings['api_key'] ) ? sanitize_text_field( $settings['api_key'] ) : '',
			'base_url'          => $base_url ? $base_url : $defaults['base_url'],
			'listings_endpoint' => '/' . ltrim( $listings_endpoint, '/' ),
			'sales_endpoint'    => '/' . ltrim( $sales_endpoint, '/' ),
			'api_key_header'    => preg_replace( '/[^A-Za-z0-9\-]/', '', $api_key_header ),
			'auth_scheme'       => $auth_scheme,
			'api_key_param'     => $api_key_param,
			'timeout'           => $timeout,
			'cache_minutes'     => max( 0, absint( $settings['cache_minutes'] ?? $defaults['cache_minutes'] ) ),
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
			'apex27-settings' => array(
				'label'       => __( 'Apex27 Settings', 'woodivi-extend' ),
				'title'       => __( 'Apex27 Settings', 'woodivi-extend' ),
				'description' => __( 'Connect Apex27 CRM so Divi modules can display property listings, sales items, and search filters.', 'woodivi-extend' ),
				'view'        => WOO_DIVI_EXTENDED_PATH . 'views/admin/modules/apex27-settings.php',
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
