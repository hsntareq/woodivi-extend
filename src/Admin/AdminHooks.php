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
	 * Render admin page
	 *
	 * @return void
	 */
	public function render_admin_page() {
		$file = WOO_DIVI_EXTENDED_PATH . 'views/admin-page.php';
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}
