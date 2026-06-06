<?php
/**
 * Apex27 Listings native Divi 5 module.
 *
 * @package WooDiviExtended\Divi5
 */

namespace WooDiviExtended\Divi5;

use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use WooDiviExtended\Api\Apex27Client;
use WP_Block_Type_Registry;

/**
 * Native Divi 5 Apex27 Listings module.
 */
class Apex27ListingsModule {

	/**
	 * Whether the module has already been registered.
	 *
	 * @var bool
	 */
	private static $registered = false;

	/**
	 * Register the module.
	 *
	 * @return void
	 */
	public static function load() {
		if ( self::$registered ) {
			return;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Apex27 Listings: load() called from hook: ' . current_filter() );
		}

		$module_path = WOO_DIVI_EXTENDED_PATH . 'modules/apex27-listings';
		$config      = array(
			'render_callback' => array( self::class, 'render_callback' ),
		);

		if ( class_exists( ModuleRegistration::class ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Listings: Registering via ModuleRegistration class.' );
			}
			ModuleRegistration::register_module( $module_path, $config );
			self::$registered = true;
		} elseif ( function_exists( 'divi_module_library_register_module' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Listings: Registering via divi_module_library_register_module function.' );
			}
			divi_module_library_register_module( $module_path, $config );
			self::$registered = true;
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Listings: No Divi 5 registration API found yet.' );
			}
		}
	}

	/**
	 * Add module classnames.
	 *
	 * @param array $args Classname arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ) {
		$classnames_instance = $args['classnamesInstance'];
		$attrs               = $args['attrs'];

		$classnames_instance->add( 'woodivi-apex27-listings-module' );
		$classnames_instance->add(
			ElementClassnames::classnames(
				array(
					'attrs' => $attrs['module']['decoration'] ?? array(),
				)
			)
		);
	}

	/**
	 * Render callback.
	 *
	 * @param array    $attrs   Module attributes.
	 * @param string   $content Child content.
	 * @param \WP_Block $block  Block object.
	 * @param object   $elements Module elements instance.
	 *
	 * @return string
	 */
	public static function render_callback( $attrs, $content, $block, $elements ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Apex27 Listings: render_callback called.' );
		}
		$output = self::render_listings( $attrs );

		return Module::render(
			array(
				'attrs'              => $attrs,
				'elements'           => $elements,
				'id'                 => $block->parsed_block['id'] ?? '',
				'name'               => $block->block_type->name,
				'moduleCategory'     => $block->block_type->category,
				'classnamesFunction' => array( self::class, 'module_classnames' ),
				'orderIndex'         => $block->parsed_block['orderIndex'] ?? null,
				'storeInstance'      => $block->parsed_block['storeInstance'] ?? null,
				'children'           => $elements->style_components(
					array(
						'attrName' => 'module',
					)
				) . $output . $content,
			)
		);
	}

	/**
	 * Render listings output.
	 *
	 * @param array $attrs Module attributes.
	 *
	 * @return string
	 */
	private static function render_listings( array $attrs ) {
		$client = new Apex27Client();

		if ( ! $client->is_configured() ) {
			return self::render_notice( __( 'Apex27 API settings are not configured yet.', 'woodivi-extend' ) );
		}

		$query = array_merge( self::get_static_query( $attrs ), self::get_search_query() );
		$limit = max( 1, absint( $attrs['apex27']['advanced']['limit']['desktop']['value'] ?? 9 ) );
		$type  = $attrs['apex27']['advanced']['itemType']['desktop']['value'] ?? 'listings';
		$custom_endpoint = $attrs['apex27']['advanced']['customEndpoint']['desktop']['value'] ?? '';

		$items = array();

		if ( 'custom' === $type ) {
			$custom_items = $client->get_endpoint_items( $custom_endpoint, $query );

			if ( is_wp_error( $custom_items ) ) {
				return self::render_notice( $custom_items->get_error_message() );
			}

			$items = $custom_items;
		}

		if ( 'sales' === $type || 'both' === $type ) {
			$sales = $client->get_sales( $query );
			if ( is_wp_error( $sales ) ) {
				return self::render_notice( $sales->get_error_message() );
			}
			$items = array_merge( $items, $sales );
		}

		if ( 'listings' === $type || 'both' === $type ) {
			$listings = $client->get_listings( $query );
			if ( is_wp_error( $listings ) ) {
				return self::render_notice( $listings->get_error_message() );
			}
			$items = array_merge( $items, $listings );
		}

		$items = array_slice( $items, 0, $limit );

		if ( empty( $items ) ) {
			$empty_text = $attrs['apex27']['advanced']['emptyText']['desktop']['value'] ?? __( 'No Apex27 properties found.', 'woodivi-extend' );
			return self::render_notice( $empty_text );
		}

		$columns = (string) ( $attrs['apex27']['advanced']['columns']['desktop']['value'] ?? '3' );
		$columns = in_array( $columns, array( '2', '3', '4' ), true ) ? $columns : '3';
		$output  = sprintf( '<div class="woodivi-apex27-listings woodivi-apex27-listings--columns-%s">', esc_attr( $columns ) );

		foreach ( $items as $item ) {
			$output .= self::render_card( is_array( $item ) ? $item : array() );
		}

		return $output . '</div>';
	}

	/**
	 * Get search query values.
	 *
	 * @return array
	 */
	private static function get_search_query() {
		$allowed = array(
			// Keep frontend behavior aligned with Divi 4 while preserving common Apex27 aliases.
			'apex27_query'     => array( 'query', 'city' ),
			'apex27_status'    => array( 'status' ),
			'apex27_type'      => array( 'type', 'propertyType' ),
			'apex27_bedrooms'  => array( 'bedrooms', 'minBeds' ),
			'apex27_min_price' => array( 'min_price', 'minPrice' ),
			'apex27_max_price' => array( 'max_price', 'maxPrice' ),
		);

		$query = array();

		foreach ( $allowed as $request_key => $api_keys ) {
			if ( isset( $_GET[ $request_key ] ) && ! is_array( $_GET[ $request_key ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_GET[ $request_key ] ) );

				foreach ( $api_keys as $api_key ) {
					$query[ $api_key ] = $value;
				}
			}
		}

		return $query;
	}

	/**
	 * Get static query values from module settings.
	 *
	 * @param array $attrs Module attributes.
	 *
	 * @return array
	 */
	private static function get_static_query( array $attrs ) {
		$raw_query = $attrs['apex27']['advanced']['staticQuery']['desktop']['value'] ?? '';
		$raw_query = is_scalar( $raw_query ) ? (string) $raw_query : '';

		if ( '' === trim( $raw_query ) ) {
			return array();
		}

		parse_str( ltrim( $raw_query, '?' ), $pairs );

		if ( ! is_array( $pairs ) ) {
			return array();
		}

		$query = array();
		foreach ( $pairs as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			$key   = sanitize_key( (string) $key );
			$value = sanitize_text_field( (string) $value );

			if ( '' !== $key && '' !== $value ) {
				$query[ $key ] = $value;
			}
		}

		return $query;
	}

	/**
	 * Render a listing card.
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 */
	private static function render_card( array $item ) {
		$title   = self::string_value( self::first_value( $item, array( 'title', 'displayAddress', 'display_address', 'address', 'property_address', 'address1', 'name' ) ) );
		$price   = self::string_value( self::first_value( $item, array( 'price', 'asking_price', 'guide_price', 'sale_price', 'rent' ) ) );
		$status  = self::string_value( self::first_value( $item, array( 'status', 'availability', 'sale_status' ) ) );
		$type    = self::string_value( self::first_value( $item, array( 'displayPropertyType', 'propertyType', 'type', 'property_type', 'department' ) ) );
		$summary = self::string_value( self::first_value( $item, array( 'summary', 'description', 'printSummary', 'short_description', 'strapline' ) ) );
		$image   = self::resolve_image( $item );
		$url     = self::string_value( self::first_value( $item, array( 'url', 'link', 'details_url', 'public_url' ) ) );

		if ( is_array( $image ) ) {
			$image = self::first_value( $image, array( 'url', 'src', 'large', 'medium' ) );
		}

		$image = self::string_value( $image );
		$title = $title ? $title : __( 'Apex27 Property', 'woodivi-extend' );

		$output = '<article class="woodivi-apex27-card">';

		if ( $image ) {
			$output .= sprintf(
				'<div class="woodivi-apex27-card__media"><img src="%s" alt="%s" loading="lazy" /></div>',
				esc_url( $image ),
				esc_attr( wp_strip_all_tags( $title ) )
			);
		}

		$output .= '<div class="woodivi-apex27-card__body">';
		$output .= sprintf( '<h3 class="woodivi-apex27-card__title">%s</h3>', esc_html( $title ) );

		if ( $price || $status || $type ) {
			$output .= '<div class="woodivi-apex27-card__meta">';
			$output .= $price ? sprintf( '<span>%s</span>', esc_html( $price ) ) : '';
			$output .= $status ? sprintf( '<span>%s</span>', esc_html( $status ) ) : '';
			$output .= $type ? sprintf( '<span>%s</span>', esc_html( $type ) ) : '';
			$output .= '</div>';
		}

		if ( $summary ) {
			$output .= sprintf( '<p class="woodivi-apex27-card__summary">%s</p>', esc_html( wp_trim_words( wp_strip_all_tags( $summary ), 28 ) ) );
		}

		if ( $url ) {
			$output .= sprintf( '<a class="woodivi-apex27-card__link" href="%s">%s</a>', esc_url( $url ), esc_html__( 'View details', 'woodivi-extend' ) );
		}

		return $output . '</div></article>';
	}

	/**
	 * Return the first available value.
	 *
	 * @param array $item Item data.
	 * @param array $keys Candidate keys.
	 *
	 * @return mixed
	 */
	private static function first_value( array $item, array $keys ) {
		foreach ( $keys as $key ) {
			if ( isset( $item[ $key ] ) && '' !== $item[ $key ] ) {
				return $item[ $key ];
			}
		}

		return '';
	}

	/**
	 * Resolve listing image URL from common Apex27 response fields.
	 *
	 * @param array $item Listing item.
	 *
	 * @return string
	 */
	private static function resolve_image( array $item ) {
		$image = self::first_value( $item, array( 'image', 'image_url', 'main_image', 'thumbnail', 'photo' ) );

		if ( is_array( $image ) ) {
			$image = self::first_value( $image, array( 'url', 'src', 'large', 'medium' ) );
		}

		if ( '' !== self::string_value( $image ) ) {
			return self::string_value( $image );
		}

		$images = self::first_value( $item, array( 'images', 'gallery' ) );
		if ( is_array( $images ) && isset( $images[0] ) && is_array( $images[0] ) ) {
			return self::string_value( self::first_value( $images[0], array( 'url', 'thumbnailUrl', 'src' ) ) );
		}

		return '';
	}

	/**
	 * Convert scalar values to strings.
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	private static function string_value( $value ) {
		return is_scalar( $value ) ? (string) $value : '';
	}

	/**
	 * Render a frontend notice.
	 *
	 * @param string $message Notice message.
	 *
	 * @return string
	 */
	private static function render_notice( $message ) {
		return sprintf( '<div class="woodivi-apex27-notice">%s</div>', esc_html( $message ) );
	}

}
