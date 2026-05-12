<?php
/**
 * Apex27 Listings Divi module.
 *
 * @package WooDiviExtended\DiviModules
 */

namespace WooDiviExtended\DiviModules;

use WooDiviExtended\Api\Apex27Client;

/**
 * Display Apex27 listing and sales items.
 */
class Apex27Listings extends \ET_Builder_Module {

	/**
	 * Module fields have advanced style support disabled by default.
	 *
	 * @var array
	 */
	public $advanced_fields = array(
		'fonts'      => false,
		'background' => false,
		'borders'    => false,
		'box_shadow' => false,
	);

	/**
	 * Initialize module.
	 *
	 * @return void
	 */
	public function init() {
		$this->name             = esc_html__( 'Apex27 Listings', 'woodivi-extend' );
		$this->slug             = 'woodivi_apex27_listings';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%.woodivi-apex27-listings';
	}

	/**
	 * Module fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'item_type' => array(
				'label'           => esc_html__( 'Items to Display', 'woodivi-extend' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'listings' => esc_html__( 'Listings', 'woodivi-extend' ),
					'sales'    => esc_html__( 'Sales', 'woodivi-extend' ),
					'both'     => esc_html__( 'Listings and Sales', 'woodivi-extend' ),
				),
				'default'         => 'listings',
			),
			'limit'     => array(
				'label'           => esc_html__( 'Item Limit', 'woodivi-extend' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '9',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '48',
					'step' => '1',
				),
			),
			'columns'   => array(
				'label'           => esc_html__( 'Columns', 'woodivi-extend' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'2' => esc_html__( '2 Columns', 'woodivi-extend' ),
					'3' => esc_html__( '3 Columns', 'woodivi-extend' ),
					'4' => esc_html__( '4 Columns', 'woodivi-extend' ),
				),
				'default'         => '3',
			),
			'empty_text' => array(
				'label'           => esc_html__( 'Empty State Text', 'woodivi-extend' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => esc_html__( 'No Apex27 properties found.', 'woodivi-extend' ),
			),
		);
	}

	/**
	 * Render module output.
	 *
	 * @param array  $attrs       Module attributes.
	 * @param string $content     Module content.
	 * @param string $render_slug Render slug.
	 *
	 * @return string
	 */
	public function render( $attrs, $content = null, $render_slug = null ) {
		$client = new Apex27Client();

		if ( ! $client->is_configured() ) {
			return $this->render_notice( esc_html__( 'Apex27 API settings are not configured yet.', 'woodivi-extend' ) );
		}

		$query = $this->get_search_query();
		$limit = max( 1, absint( $this->props['limit'] ?? 9 ) );
		$type  = $this->props['item_type'] ?? 'listings';

		$items = array();

		if ( 'sales' === $type || 'both' === $type ) {
			$sales = $client->get_sales( $query );
			if ( is_wp_error( $sales ) ) {
				return $this->render_notice( $sales->get_error_message() );
			}
			$items = array_merge( $items, $sales );
		}

		if ( 'listings' === $type || 'both' === $type ) {
			$listings = $client->get_listings( $query );
			if ( is_wp_error( $listings ) ) {
				return $this->render_notice( $listings->get_error_message() );
			}
			$items = array_merge( $items, $listings );
		}

		$items = array_slice( $items, 0, $limit );

		if ( empty( $items ) ) {
			return $this->render_notice( $this->props['empty_text'] ?? esc_html__( 'No Apex27 properties found.', 'woodivi-extend' ) );
		}

		$columns = in_array( (string) ( $this->props['columns'] ?? '3' ), array( '2', '3', '4' ), true ) ? (string) $this->props['columns'] : '3';
		$output  = sprintf( '<div class="woodivi-apex27-listings woodivi-apex27-listings--columns-%s">', esc_attr( $columns ) );

		foreach ( $items as $item ) {
			$output .= $this->render_card( is_array( $item ) ? $item : array() );
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Get query values from the companion search form.
	 *
	 * @return array
	 */
	private function get_search_query() {
		$allowed = array(
			'apex27_query'     => 'query',
			'apex27_status'    => 'status',
			'apex27_type'      => 'type',
			'apex27_bedrooms'  => 'bedrooms',
			'apex27_min_price' => 'min_price',
			'apex27_max_price' => 'max_price',
		);

		$query = array();

		foreach ( $allowed as $request_key => $api_key ) {
			if ( isset( $_GET[ $request_key ] ) && ! is_array( $_GET[ $request_key ] ) ) {
				$query[ $api_key ] = sanitize_text_field( wp_unslash( $_GET[ $request_key ] ) );
			}
		}

		return $query;
	}

	/**
	 * Render a property card.
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 */
	private function render_card( array $item ) {
		$title   = $this->first_value( $item, array( 'title', 'address', 'display_address', 'property_address', 'name' ) );
		$price   = $this->first_value( $item, array( 'price', 'asking_price', 'guide_price', 'sale_price', 'rent' ) );
		$status  = $this->first_value( $item, array( 'status', 'availability', 'sale_status' ) );
		$type    = $this->first_value( $item, array( 'type', 'property_type', 'department' ) );
		$summary = $this->first_value( $item, array( 'summary', 'description', 'short_description', 'strapline' ) );
		$image   = $this->first_value( $item, array( 'image', 'image_url', 'main_image', 'thumbnail', 'photo' ) );
		$url     = $this->first_value( $item, array( 'url', 'link', 'details_url', 'public_url' ) );

		if ( is_array( $image ) ) {
			$image = $this->first_value( $image, array( 'url', 'src', 'large', 'medium' ) );
		}

		$title   = $this->string_value( $title );
		$price   = $this->string_value( $price );
		$status  = $this->string_value( $status );
		$type    = $this->string_value( $type );
		$summary = $this->string_value( $summary );
		$image   = $this->string_value( $image );
		$url     = $this->string_value( $url );
		$title = $title ? $title : esc_html__( 'Apex27 Property', 'woodivi-extend' );

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

		$output .= '</div></article>';

		return $output;
	}

	/**
	 * Return the first available item value.
	 *
	 * @param array $item Item data.
	 * @param array $keys Candidate keys.
	 *
	 * @return mixed
	 */
	private function first_value( array $item, array $keys ) {
		foreach ( $keys as $key ) {
			if ( isset( $item[ $key ] ) && '' !== $item[ $key ] ) {
				return $item[ $key ];
			}
		}

		return '';
	}

	/**
	 * Convert common scalar values to a display string.
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	private function string_value( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	/**
	 * Render a frontend notice.
	 *
	 * @param string $message Notice message.
	 *
	 * @return string
	 */
	private function render_notice( $message ) {
		return sprintf( '<div class="woodivi-apex27-notice">%s</div>', esc_html( $message ) );
	}
}
