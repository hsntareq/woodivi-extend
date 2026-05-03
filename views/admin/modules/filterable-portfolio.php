<?php
/**
 * Filterable Portfolio admin view.
 *
 * @package WooDiviExtended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

$defaults = array(
	'enabled'        => 0,
	'show_counts'    => 1,
	'show_label'     => 1,
	'checkbox_label' => __( 'Refine by sub category', 'woodivi-extend' ),
);
$settings = wp_parse_args( get_option( 'woodivi_extend_filterable_portfolio', array() ), $defaults );
?>

<form method="post" action="options.php" class="woodivi-extend-form">
	<?php settings_fields( 'woodivi_extend_filterable_portfolio' ); ?>

	<div class="woodivi-extend-card">
		<h3><?php esc_html_e( 'Sub Category Filters', 'woodivi-extend' ); ?></h3>
		<p><?php esc_html_e( 'Use this page as the global switch. Then enable "Show Sub Categories" inside each Divi Filterable Portfolio module under Content > Elements.', 'woodivi-extend' ); ?></p>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Feature Status', 'woodivi-extend' ); ?></th>
					<td>
						<label for="woodivi-filterable-portfolio-enabled">
							<input
								type="checkbox"
								id="woodivi-filterable-portfolio-enabled"
								name="woodivi_extend_filterable_portfolio[enabled]"
								value="1"
								<?php checked( 1, (int) $settings['enabled'] ); ?>
							>
							<?php esc_html_e( 'Enable sub category checkboxes for Divi Filterable Portfolio', 'woodivi-extend' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="woodivi-filterable-portfolio-label"><?php esc_html_e( 'Checkbox Group Label', 'woodivi-extend' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="woodivi-filterable-portfolio-label"
							class="regular-text"
							name="woodivi_extend_filterable_portfolio[checkbox_label]"
							value="<?php echo esc_attr( $settings['checkbox_label'] ); ?>"
						>
						<p class="description"><?php esc_html_e( 'Leave empty to show no label on the frontend.', 'woodivi-extend' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Label Visibility', 'woodivi-extend' ); ?></th>
					<td>
						<label for="woodivi-filterable-portfolio-show-label">
							<input
								type="checkbox"
								id="woodivi-filterable-portfolio-show-label"
								name="woodivi_extend_filterable_portfolio[show_label]"
								value="1"
								<?php checked( 1, (int) $settings['show_label'] ); ?>
							>
							<?php esc_html_e( 'Show checkbox group label', 'woodivi-extend' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Counts', 'woodivi-extend' ); ?></th>
					<td>
						<label for="woodivi-filterable-portfolio-counts">
							<input
								type="checkbox"
								id="woodivi-filterable-portfolio-counts"
								name="woodivi_extend_filterable_portfolio[show_counts]"
								value="1"
								<?php checked( 1, (int) $settings['show_counts'] ); ?>
							>
							<?php esc_html_e( 'Show project counts beside each child category', 'woodivi-extend' ); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button(); ?>
	</div>
</form>
