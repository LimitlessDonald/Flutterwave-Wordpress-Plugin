<?php
/**
 * Donation Form shortcode
 *
 * @package Flutterwave\Payments\Shortcodes
 * @version 1.0.6
 */

/**
 * Flutterwave Donation Shortcode Class.
 */
final class FLW_Shortcode_Donation_Form extends Abstract_FLW_Shortcode {
	/**
	 * Button Text.
	 *
	 * @var string
	 */
	protected string $button_text = 'DONATE ONCE';
	/**
	 * Initialize shortcode.
	 *
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type Shortcode type.
	 *
	 * @since 1.0.6
	 */
	public function __construct( array $attributes = array(), string $type = 'flw-donation-form' ) { // phpcs:ignore
		parent::__construct( $attributes, $type );
	}

	/**
	 * Get Attributes.
	 *
	 * @return array
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * Parse shortcode attributes.
	 *
	 * @param array $attributes Shortcode attributes.
	 *
	 * @return array
	 * @since  1.0.6
	 */
	protected function parse_attributes( array $attributes = array() ): array {
		$email                = self::use_current_user_email( $attributes ) ? wp_get_current_user()->user_email : '';
		$admin_payment_method = $this->settings->get_option_value( 'method' );
		$payment_method       = self::get_payment_options()[ $admin_payment_method ] ?? self::get_payment_options()['all'];

		$custom_currency = 'USD,KES,ZAR,GHS,TZS,EUR,NGN,GBP,UGX,RWF,ZMW';
		return shortcode_atts(
			array(
				'amount'              => 0,
				'mobile_orientation'  => 'portrait',
				'desktop_orientation' => 'landscape',
				'custom_currency'     => $custom_currency,
				'country'             => $this->settings->get_option_value( 'country' ),
				'payment_method'      => $payment_method,
				'email'               => $email,
			),
			$attributes,
			$this->type
		);
	}

	/**
	 * Parse Query Args.
	 *
	 * @return array
	 */
	protected function parse_query_args(): array {
		return array();
	}

	/**
	 * Render Shortcode Form.
	 *
	 * @return void
	 */
	public function render(): void {
		$atts      = $this->get_attributes();
		$btn_text  = $this->button_text;
		$data_attr = '';
		foreach ( $atts as $att_key => $att_value ) {

			if ( ! is_array( $att_value ) ) {
				$data_attr .= ' data-' . $att_key . '="' . $att_value . '"';
			}
		}
		include FLW_DIR_PATH . 'views/donation-payment.php';
	}

	/**
	 * Include Javascript File.
	 *
	 * @return void
	 */
	public function load_scripts(): void {
		$settings             = $this->settings;
		$admin_payment_method = $settings->get_option_value( 'method' );
		$payment_method       = self::get_payment_options()[ $admin_payment_method ] ?? self::get_payment_options()['all'];

		$args = array(
			'cb_url'     => admin_url( 'admin-ajax.php' ),
			'country'    => $settings->get_option_value( 'country' ),
			'currency'   => $settings->get_option_value( 'currency' ),
			'desc'       => $settings->get_option_value( 'modal_desc' ),
			'logo'       => $settings->get_option_value( 'modal_logo' ),
			'method'     => $payment_method,
			'public_key' => $settings->get_option_value( 'public_key' ),
			'title'      => $settings->get_option_value( 'modal_title' ),
			'countries'  => self::get_supported_country(),
		);

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'flw_donation_js', FLW_DIR_URL . 'assets/js/flw-donation.min.js', array( 'jquery', 'jquery-effects-shake' ), FLW_PAY_VERSION, true );

		wp_localize_script( 'flw_donation_js', 'flw_pay_options', $args );
	}
}
