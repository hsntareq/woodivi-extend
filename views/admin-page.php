<?php
/**
 * Admin Page View
 *
 * @package WooDiviExtended
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'WooDivi Extend Settings', 'woodivi-extend' ); ?></h1>

	<div class="woodivi-extend-admin">
		<nav class="woodivi-extend-nav" aria-label="<?php esc_attr_e( 'WooDivi Extend modules', 'woodivi-extend' ); ?>">
			<?php foreach ( $modules as $module_slug => $module ) : ?>
				<?php
				$module_url = add_query_arg(
					array(
						'page'   => 'woodivi-extend',
						'module' => $module_slug,
					),
					admin_url( 'admin.php' )
				);
				?>
				<a
					class="woodivi-extend-nav__item <?php echo $module_slug === $current_module_slug ? 'is-active' : ''; ?>"
					href="<?php echo esc_url( $module_url ); ?>"
				>
					<?php echo esc_html( $module['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="woodivi-extend-panel">
			<header class="woodivi-extend-panel__header">
				<h2><?php echo esc_html( $current_module['title'] ); ?></h2>
				<?php if ( ! empty( $current_module['description'] ) ) : ?>
					<p><?php echo esc_html( $current_module['description'] ); ?></p>
				<?php endif; ?>
			</header>

			<div class="woodivi-extend-panel__content">
				<?php
				if ( ! empty( $current_module['view'] ) && file_exists( $current_module['view'] ) ) {
					include $current_module['view'];
				} else {
					?>
					<div class="notice notice-warning inline">
						<p><?php esc_html_e( 'This module does not have a settings view yet.', 'woodivi-extend' ); ?></p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
