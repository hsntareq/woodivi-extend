<?php
/**
 * Apex27 API client.
 *
 * @package WooDiviExtended\Api
 */

namespace WooDiviExtended\Api;

/**
 * Small server-side client for Apex27 property data.
 */
class Apex27Client {

	/**
	 * Settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->settings = wp_parse_args(
			get_option( 'woodivi_extend_apex27', array() ),
			array(
				'api_key'           => '',
				'base_url'          => 'https://api.apex27.co.uk',
				'listings_endpoint' => '/listings',
				'sales_endpoint'    => '/offers',
				'api_key_header'    => 'x-api-key',
				'auth_scheme'       => 'none',
				'api_key_param'     => '',
				'timeout'           => 20,
				'cache_minutes'     => 15,
			)
		);
	}

	/**
	 * Determine whether the API is configured.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->settings['api_key'] ) && ! empty( $this->settings['base_url'] );
	}

	/**
	 * Fetch listings.
	 *
	 * @param array $query Query arguments.
	 *
	 * @return array|\WP_Error
	 */
	public function get_listings( array $query = array() ) {
		return $this->request( $this->settings['listings_endpoint'], $query );
	}

	/**
	 * Fetch sales.
	 *
	 * @param array $query Query arguments.
	 *
	 * @return array|\WP_Error
	 */
	public function get_sales( array $query = array() ) {
		return $this->request( $this->settings['sales_endpoint'], $query );
	}

	/**
	 * Fetch items from a custom endpoint.
	 *
	 * @param string $endpoint Endpoint path.
	 * @param array  $query    Query arguments.
	 *
	 * @return array|\WP_Error
	 */
	public function get_endpoint_items( $endpoint, array $query = array() ) {
		$endpoint = is_string( $endpoint ) ? trim( $endpoint ) : '';

		if ( '' === $endpoint ) {
			return new \WP_Error( 'apex27_missing_endpoint', __( 'A custom endpoint is required.', 'woodivi-extend' ) );
		}

		return $this->request( $endpoint, $query );
	}

	/**
	 * Make an API request.
	 *
	 * @param string $endpoint Endpoint path.
	 * @param array  $query    Query arguments.
	 *
	 * @return array|\WP_Error
	 */
	private function request( $endpoint, array $query ) {
		if ( ! $this->is_configured() ) {
			return new \WP_Error( 'apex27_not_configured', __( 'Apex27 API settings are not configured.', 'woodivi-extend' ) );
		}

		$query = array_filter(
			$query,
			static function( $value ) {
				return '' !== $value && null !== $value;
			}
		);

		$url = trailingslashit( $this->settings['base_url'] ) . ltrim( $endpoint, '/' );

		if ( ! empty( $query ) ) {
			$url = add_query_arg( $query, $url );
		}

		$api_key_param = sanitize_key( $this->settings['api_key_param'] ?? '' );
		if ( '' !== $api_key_param ) {
			$url = add_query_arg( $api_key_param, $this->settings['api_key'], $url );
		}

		$cache_key     = 'woodivi_apex27_' . md5( $url );
		$cache_minutes = absint( $this->settings['cache_minutes'] );

		if ( $cache_minutes > 0 ) {
			$cached = get_transient( $cache_key );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		$auth_scheme = sanitize_key( $this->settings['auth_scheme'] ?? 'bearer' );
		$headers     = array(
			'Accept' => 'application/json',
		);

		if ( 'none' !== $auth_scheme ) {
			$auth_prefix = 'token' === $auth_scheme ? 'Token ' : 'Bearer ';
			$headers['Authorization'] = $auth_prefix . $this->settings['api_key'];
		}

		$header_name = preg_replace( '/[^A-Za-z0-9\-]/', '', (string) ( $this->settings['api_key_header'] ?? 'X-API-Key' ) );
		if ( '' !== $header_name ) {
			$headers[ $header_name ] = $this->settings['api_key'];
		}

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => max( 5, absint( $this->settings['timeout'] ?? 20 ) ),
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body, true );

		if ( $status_code < 200 || $status_code >= 300 ) {
			return new \WP_Error(
				'apex27_request_failed',
				sprintf(
					/* translators: %d: HTTP status code. */
					__( 'Apex27 request failed with HTTP status %d.', 'woodivi-extend' ),
					$status_code
				)
			);
		}

		if ( null === $data && JSON_ERROR_NONE !== json_last_error() ) {
			return new \WP_Error( 'apex27_invalid_json', __( 'Apex27 returned an invalid JSON response.', 'woodivi-extend' ) );
		}

		$items = $this->normalize_items( $data );

		if ( $cache_minutes > 0 ) {
			set_transient( $cache_key, $items, $cache_minutes * MINUTE_IN_SECONDS );
		}

		return $items;
	}

	/**
	 * Normalize common API response shapes to an item array.
	 *
	 * @param array $data Raw decoded response.
	 *
	 * @return array
	 */
	private function normalize_items( array $data ) {
		foreach ( array( 'data', 'items', 'results', 'listings', 'properties', 'sales' ) as $key ) {
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				return $data[ $key ];
			}
		}

		if ( isset( $data[0] ) && is_array( $data[0] ) ) {
			return $data;
		}

		return array();
	}
}
