<?php
/**
 * Apex27 settings view.
 *
 * @package WooDiviExtended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

$defaults = array(
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

$settings = wp_parse_args( get_option( 'woodivi_extend_apex27', array() ), $defaults );
?>

<form method="post" action="options.php" class="woodivi-extend-form">
	<?php settings_fields( 'woodivi_extend_apex27' ); ?>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-api-key"><?php esc_html_e( 'API Key', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="password"
						id="woodivi-extend-apex27-api-key"
						name="woodivi_extend_apex27[api_key]"
						value="<?php echo esc_attr( $settings['api_key'] ); ?>"
						class="regular-text"
						autocomplete="off"
					/>
					<p class="description"><?php esc_html_e( 'Used server-side only when fetching Apex27 property data.', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-base-url"><?php esc_html_e( 'API Base URL', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="url"
						id="woodivi-extend-apex27-base-url"
						name="woodivi_extend_apex27[base_url]"
						value="<?php echo esc_attr( $settings['base_url'] ); ?>"
						class="regular-text"
						placeholder="https://api.apex27.co.uk"
					/>
					<p class="description"><?php esc_html_e( 'Official production server is https://api.apex27.co.uk', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-listings-endpoint"><?php esc_html_e( 'Listings Endpoint', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="woodivi-extend-apex27-listings-endpoint"
						name="woodivi_extend_apex27[listings_endpoint]"
						value="<?php echo esc_attr( $settings['listings_endpoint'] ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Example: /listings', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-sales-endpoint"><?php esc_html_e( 'Sales Endpoint', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="woodivi-extend-apex27-sales-endpoint"
						name="woodivi_extend_apex27[sales_endpoint]"
						value="<?php echo esc_attr( $settings['sales_endpoint'] ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Example: /offers', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-api-key-header"><?php esc_html_e( 'API Key Header', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="woodivi-extend-apex27-api-key-header"
						name="woodivi_extend_apex27[api_key_header]"
						value="<?php echo esc_attr( $settings['api_key_header'] ); ?>"
						class="regular-text"
						placeholder="x-api-key"
					/>
					<p class="description"><?php esc_html_e( 'Header that should carry the API key. Leave default for most Apex27 installs.', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-auth-scheme"><?php esc_html_e( 'Authorization Scheme', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<select id="woodivi-extend-apex27-auth-scheme" name="woodivi_extend_apex27[auth_scheme]">
						<option value="none" <?php selected( $settings['auth_scheme'], 'none' ); ?>><?php esc_html_e( 'None', 'woodivi-extend' ); ?></option>
						<option value="bearer" <?php selected( $settings['auth_scheme'], 'bearer' ); ?>><?php esc_html_e( 'Bearer {api_key}', 'woodivi-extend' ); ?></option>
						<option value="token" <?php selected( $settings['auth_scheme'], 'token' ); ?>><?php esc_html_e( 'Token {api_key}', 'woodivi-extend' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-api-key-param"><?php esc_html_e( 'API Key Query Param (optional)', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="woodivi-extend-apex27-api-key-param"
						name="woodivi_extend_apex27[api_key_param]"
						value="<?php echo esc_attr( $settings['api_key_param'] ); ?>"
						class="regular-text"
						placeholder="api_key"
					/>
					<p class="description"><?php esc_html_e( 'If your server expects the key in the URL, enter the query parameter name.', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-timeout"><?php esc_html_e( 'Request Timeout', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="woodivi-extend-apex27-timeout"
						name="woodivi_extend_apex27[timeout]"
						value="<?php echo esc_attr( $settings['timeout'] ); ?>"
						class="small-text"
						min="5"
					/>
					<?php esc_html_e( 'seconds', 'woodivi-extend' ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="woodivi-extend-apex27-cache-minutes"><?php esc_html_e( 'Cache Duration', 'woodivi-extend' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="woodivi-extend-apex27-cache-minutes"
						name="woodivi_extend_apex27[cache_minutes]"
						value="<?php echo esc_attr( $settings['cache_minutes'] ); ?>"
						class="small-text"
						min="0"
					/>
					<?php esc_html_e( 'minutes', 'woodivi-extend' ); ?>
					<p class="description"><?php esc_html_e( 'Set to 0 while testing live API responses.', 'woodivi-extend' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Apex27 Settings', 'woodivi-extend' ) ); ?>
</form>

<div class="notice notice-info inline">
	<p><?php esc_html_e( 'After saving, add the Apex27 Listings and Apex27 Search Form modules in the Divi Builder.', 'woodivi-extend' ); ?></p>
</div>
