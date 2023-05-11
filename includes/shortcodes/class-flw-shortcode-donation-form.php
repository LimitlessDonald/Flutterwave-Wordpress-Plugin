<?php

final class FLW_Shortcode_Donation_Form extends Abstract_FLW_Shortcode{
	/**
	 * Initialize shortcode.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $type Shortcode type.
	 *
	 *@since 1.0.6
	 */
	public function __construct( array $attributes = array(), string $type = 'flw-donation-page' ) {
		parent::__construct( $attributes, $type );
	}

	/**
	 * Parse shortcode attributes.
	 *
	 * @param array $attributes Shortcode attributes.
	 *
	 * @return array
	 * @since 1.0.6
	 */
	protected function parse_attributes( array $attributes = array() ): array {
		return shortcode_atts(
			array(
				'amount' => 0,
				'currency' => 'NGN',
				'country' => 'NG',
				'payment_method' => 'both',
				'customer_email' => '',
			),
			$attributes,
			$this->type
		);
	}

	protected function parse_query_args(): array {
		return array();
	}

	public function render( ): void {
		wp_enqueue_style( 'flw_donation_css');
		include( FLW_DIR_PATH . 'views/donation-payment.php' );
	}

	public function load_scripts(): void {
		wp_enqueue_script( 'flw_donation_js' );
		wp_localize_script( 'flw_donation_js', 'flw_donation_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'flw_donation_nonce' ),
		) );
	}
}