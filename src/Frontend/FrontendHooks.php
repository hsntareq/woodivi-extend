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
		// Frontend enqueue scripts and styles
		$this->loader->add_action(
			'wp_enqueue_scripts',
			$this,
			'enqueue_frontend_assets'
		);

		// Example filter hook
		$this->loader->add_filter(
			'the_content',
			$this,
			'modify_content',
			20,
			1
		);
	}

	/**
	 * Enqueue frontend scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
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
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'woodivi-extend-nonce' ),
			)
		);
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
