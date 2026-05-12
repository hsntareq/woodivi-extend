<?php
/**
 * Apex27 Search Form Divi module.
 *
 * @package WooDiviExtended\DiviModules
 */

namespace WooDiviExtended\DiviModules;

/**
 * Display a GET-based Apex27 search form.
 */
class Apex27SearchForm extends \ET_Builder_Module {

	/**
	 * Advanced style support.
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
		$this->name             = esc_html__( 'Apex27 Search Form', 'woodivi-extend' );
		$this->slug             = 'woodivi_apex27_search_form';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%.woodivi-apex27-search';
	}

	/**
	 * Module fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'woodivi-extend' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => esc_html__( 'Search', 'woodivi-extend' ),
			),
			'show_status' => array(
				'label'           => esc_html__( 'Show Status Field', 'woodivi-extend' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'woodivi-extend' ),
					'off' => esc_html__( 'No', 'woodivi-extend' ),
				),
				'default'         => 'on',
			),
			'show_prices' => array(
				'label'           => esc_html__( 'Show Price Fields', 'woodivi-extend' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'woodivi-extend' ),
					'off' => esc_html__( 'No', 'woodivi-extend' ),
				),
				'default'         => 'on',
			),
			'show_bedrooms' => array(
				'label'           => esc_html__( 'Show Bedrooms Field', 'woodivi-extend' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'woodivi-extend' ),
					'off' => esc_html__( 'No', 'woodivi-extend' ),
				),
				'default'         => 'on',
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
		$values = array(
			'apex27_query'     => $this->request_value( 'apex27_query' ),
			'apex27_status'    => $this->request_value( 'apex27_status' ),
			'apex27_type'      => $this->request_value( 'apex27_type' ),
			'apex27_bedrooms'  => $this->request_value( 'apex27_bedrooms' ),
			'apex27_min_price' => $this->request_value( 'apex27_min_price' ),
			'apex27_max_price' => $this->request_value( 'apex27_max_price' ),
		);

		$output  = '<form class="woodivi-apex27-search" method="get">';
		$output .= $this->render_existing_query_inputs( array_keys( $values ) );
		$output .= '<div class="woodivi-apex27-search__grid">';
		$output .= $this->render_text_field( 'apex27_query', esc_html__( 'Location or keyword', 'woodivi-extend' ), $values['apex27_query'] );
		$output .= $this->render_text_field( 'apex27_type', esc_html__( 'Property type', 'woodivi-extend' ), $values['apex27_type'] );

		if ( 'off' !== ( $this->props['show_status'] ?? 'on' ) ) {
			$output .= $this->render_select_field(
				'apex27_status',
				esc_html__( 'Status', 'woodivi-extend' ),
				$values['apex27_status'],
				array(
					''             => esc_html__( 'Any status', 'woodivi-extend' ),
					'available'    => esc_html__( 'Available', 'woodivi-extend' ),
					'under_offer'  => esc_html__( 'Under offer', 'woodivi-extend' ),
					'sold'         => esc_html__( 'Sold', 'woodivi-extend' ),
					'let_agreed'   => esc_html__( 'Let agreed', 'woodivi-extend' ),
				)
			);
		}

		if ( 'off' !== ( $this->props['show_bedrooms'] ?? 'on' ) ) {
			$output .= $this->render_number_field( 'apex27_bedrooms', esc_html__( 'Bedrooms', 'woodivi-extend' ), $values['apex27_bedrooms'] );
		}

		if ( 'off' !== ( $this->props['show_prices'] ?? 'on' ) ) {
			$output .= $this->render_number_field( 'apex27_min_price', esc_html__( 'Min price', 'woodivi-extend' ), $values['apex27_min_price'] );
			$output .= $this->render_number_field( 'apex27_max_price', esc_html__( 'Max price', 'woodivi-extend' ), $values['apex27_max_price'] );
		}

		$output .= sprintf(
			'<button class="woodivi-apex27-search__button" type="submit">%s</button>',
			esc_html( $this->props['button_text'] ?? esc_html__( 'Search', 'woodivi-extend' ) )
		);
		$output .= '</div></form>';

		return $output;
	}

	/**
	 * Render hidden inputs for non-Apex27 query vars.
	 *
	 * @param array $handled_keys Keys controlled by this form.
	 *
	 * @return string
	 */
	private function render_existing_query_inputs( array $handled_keys ) {
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
	 * Render a text input.
	 *
	 * @param string $name  Field name.
	 * @param string $label Field label.
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	private function render_text_field( $name, $label, $value ) {
		return sprintf(
			'<label class="woodivi-apex27-search__field"><span>%s</span><input type="text" name="%s" value="%s" /></label>',
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value )
		);
	}

	/**
	 * Render a number input.
	 *
	 * @param string $name  Field name.
	 * @param string $label Field label.
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	private function render_number_field( $name, $label, $value ) {
		return sprintf(
			'<label class="woodivi-apex27-search__field"><span>%s</span><input type="number" min="0" step="1" name="%s" value="%s" /></label>',
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value )
		);
	}

	/**
	 * Render a select input.
	 *
	 * @param string $name    Field name.
	 * @param string $label   Field label.
	 * @param string $value   Field value.
	 * @param array  $options Options.
	 *
	 * @return string
	 */
	private function render_select_field( $name, $label, $value, array $options ) {
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

		$output .= '</select></label>';

		return $output;
	}

	/**
	 * Get a sanitized request value.
	 *
	 * @param string $key Request key.
	 *
	 * @return string
	 */
	private function request_value( $key ) {
		return isset( $_GET[ $key ] ) && ! is_array( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
	}
}
