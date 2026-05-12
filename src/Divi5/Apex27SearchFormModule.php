<?php
/**
 * Apex27 Search Form native Divi 5 module.
 *
 * @package WooDiviExtended\Divi5
 */

namespace WooDiviExtended\Divi5;

use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use WP_Block_Type_Registry;

/**
 * Native Divi 5 Apex27 Search Form module.
 */
class Apex27SearchFormModule {

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
			error_log( 'Apex27 Search Form: load() called from hook: ' . current_filter() );
		}

		$module_path = WOO_DIVI_EXTENDED_PATH . 'modules/apex27-search-form';
		$config      = array(
			'render_callback' => array( self::class, 'render_callback' ),
		);

		if ( class_exists( ModuleRegistration::class ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Search Form: Registering via ModuleRegistration class.' );
			}
			ModuleRegistration::register_module( $module_path, $config );
			self::$registered = true;
		} elseif ( function_exists( 'divi_module_library_register_module' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Search Form: Registering via divi_module_library_register_module function.' );
			}
			divi_module_library_register_module( $module_path, $config );
			self::$registered = true;
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Apex27 Search Form: No Divi 5 registration API found yet.' );
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

		$classnames_instance->add( 'woodivi-apex27-search-module' );
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
			error_log( 'Apex27 Search Form: render_callback called.' );
		}
		$output = self::render_search_form( $attrs );

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
	 * Render search form.
	 *
	 * @param array $attrs Module attributes.
	 *
	 * @return string
	 */
	private static function render_search_form( array $attrs ) {
		$options = $attrs['apex27']['advanced'] ?? array();
		$values  = array(
			'apex27_query'     => self::request_value( 'apex27_query' ),
			'apex27_status'    => self::request_value( 'apex27_status' ),
			'apex27_type'      => self::request_value( 'apex27_type' ),
			'apex27_bedrooms'  => self::request_value( 'apex27_bedrooms' ),
			'apex27_min_price' => self::request_value( 'apex27_min_price' ),
			'apex27_max_price' => self::request_value( 'apex27_max_price' ),
		);

		$output  = '<form class="woodivi-apex27-search" method="get">';
		$output .= self::render_existing_query_inputs( array_keys( $values ) );
		$output .= '<div class="woodivi-apex27-search__grid">';
		$output .= self::render_text_field( 'apex27_query', __( 'Location or keyword', 'woodivi-extend' ), $values['apex27_query'] );
		$output .= self::render_text_field( 'apex27_type', __( 'Property type', 'woodivi-extend' ), $values['apex27_type'] );

		if ( 'off' !== ( $options['showStatus']['desktop']['value'] ?? 'on' ) ) {
			$output .= self::render_select_field(
				'apex27_status',
				__( 'Status', 'woodivi-extend' ),
				$values['apex27_status'],
				array(
					''            => __( 'Any status', 'woodivi-extend' ),
					'available'   => __( 'Available', 'woodivi-extend' ),
					'under_offer' => __( 'Under offer', 'woodivi-extend' ),
					'sold'        => __( 'Sold', 'woodivi-extend' ),
					'let_agreed'  => __( 'Let agreed', 'woodivi-extend' ),
				)
			);
		}

		if ( 'off' !== ( $options['showBedrooms']['desktop']['value'] ?? 'on' ) ) {
			$output .= self::render_number_field( 'apex27_bedrooms', __( 'Bedrooms', 'woodivi-extend' ), $values['apex27_bedrooms'] );
		}

		if ( 'off' !== ( $options['showPrices']['desktop']['value'] ?? 'on' ) ) {
			$output .= self::render_number_field( 'apex27_min_price', __( 'Min price', 'woodivi-extend' ), $values['apex27_min_price'] );
			$output .= self::render_number_field( 'apex27_max_price', __( 'Max price', 'woodivi-extend' ), $values['apex27_max_price'] );
		}

		$button_text = $options['buttonText']['desktop']['value'] ?? __( 'Search', 'woodivi-extend' );
		$output     .= sprintf( '<button class="woodivi-apex27-search__button" type="submit">%s</button>', esc_html( $button_text ) );

		return $output . '</div></form>';
	}

	/**
	 * Render hidden inputs for non-Apex27 query vars.
	 *
	 * @param array $handled_keys Keys controlled by this form.
	 *
	 * @return string
	 */
	private static function render_existing_query_inputs( array $handled_keys ) {
		$output = '';

		foreach ( $_GET as $key => $value ) {
			$key = sanitize_key( wp_unslash( $key ) );

			if ( in_array( $key, $handled_keys, true ) || is_array( $value ) ) {
				continue;
			}

			$output .= sprintf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $key ),
				esc_attr( sanitize_text_field( wp_unslash( $value ) ) )
			);
		}

		return $output;
	}

	/**
	 * Render text input.
	 *
	 * @param string $name  Field name.
	 * @param string $label Field label.
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	private static function render_text_field( $name, $label, $value ) {
		return sprintf(
			'<label class="woodivi-apex27-search__field"><span>%s</span><input type="text" name="%s" value="%s" /></label>',
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value )
		);
	}

	/**
	 * Render number input.
	 *
	 * @param string $name  Field name.
	 * @param string $label Field label.
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	private static function render_number_field( $name, $label, $value ) {
		return sprintf(
			'<label class="woodivi-apex27-search__field"><span>%s</span><input type="number" min="0" step="1" name="%s" value="%s" /></label>',
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value )
		);
	}

	/**
	 * Render select input.
	 *
	 * @param string $name    Field name.
	 * @param string $label   Field label.
	 * @param string $value   Field value.
	 * @param array  $options Select options.
	 *
	 * @return string
	 */
	private static function render_select_field( $name, $label, $value, array $options ) {
		$output = sprintf(
			'<label class="woodivi-apex27-search__field"><span>%s</span><select name="%s">',
			esc_html( $label ),
			esc_attr( $name )
		);

		foreach ( $options as $option_value => $option_label ) {
			$output .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $option_value ),
				selected( $value, $option_value, false ),
				esc_html( $option_label )
			);
		}

		return $output . '</select></label>';
	}

	/**
	 * Get request value.
	 *
	 * @param string $key Request key.
	 *
	 * @return string
	 */
	private static function request_value( $key ) {
		return isset( $_GET[ $key ] ) && ! is_array( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
	}
}
