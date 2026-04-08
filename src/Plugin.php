<?php
/**
 * Main Plugin Class
 *
 * @package WooDiviExtended
 */

namespace WooDiviExtended;

/**
 * Main Plugin Class
 *
 * Handles plugin initialization and setup
 */
class Plugin {

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Loader instance
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Get plugin instance (Singleton)
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->loader = new Loader();
		$this->init();
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	private function init() {
		// Load textdomain for translations
		$this->load_textdomain();

		// Register hooks
		$this->register_hooks();
	}

	/**
	 * Load plugin textdomain
	 *
	 * @return void
	 */
	private function load_textdomain() {
		load_plugin_textdomain(
			'woodivi-extend',
			false,
			dirname( WOO_DIVI_EXTENDED_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register all hooks
	 *
	 * @return void
	 */
	private function register_hooks() {
		// Admin hooks
		if ( is_admin() ) {
			$admin = new Admin\AdminHooks( $this->loader );
			$admin->register();
		}

		// Frontend hooks
		if ( ! is_admin() ) {
			$frontend = new Frontend\FrontendHooks( $this->loader );
			$frontend->register();
		}

		// Execute all registered hooks
		$this->loader->run();
	}

	/**
	 * Get Loader instance
	 *
	 * @return Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Plugin activation
	 *
	 * @return void
	 */
	public static function activate() {
		// Add any activation logic here
		// e.g., create tables, set options, etc.
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Add any deactivation logic here
		// e.g., cleanup options, remove transients, etc.
		flush_rewrite_rules();
	}
}
