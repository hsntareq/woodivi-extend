<?php
/**
 * Dashboard module view.
 *
 * @package WooDiviExtended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}
?>

<div class="woodivi-extend-card">
	<h3><?php esc_html_e( 'Getting Started', 'woodivi-extend' ); ?></h3>
	<p><?php esc_html_e( 'Use the module navigation to manage each Divi 4 and Divi 5 feature from one admin area.', 'woodivi-extend' ); ?></p>
</div>

<div class="woodivi-extend-card">
	<h3><?php esc_html_e( 'Add More Modules', 'woodivi-extend' ); ?></h3>
	<p><?php esc_html_e( 'Register another module in AdminHooks::get_admin_modules(), then create a matching view file in views/admin/modules/.', 'woodivi-extend' ); ?></p>
</div>
