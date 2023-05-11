<?php
/**
 * Products shortcode
 *
 * @package  Flutterwave\Payments\Shortcodes
 * @version  1.0.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pay Now button shortcode
 */
final class FLW_Shortcode_Payment_Form extends Abstract_FLW_Shortcode{

	protected string $button_text = 'Pay Now';
	/**
	 * Initialize shortcode.
	 *
	 *@since 1.0.6
	 */
	public function __construct( array $attributes = array(), string $type = 'flw-pay-button' ) {
		parent::__construct( $attributes, $type );
	}

	public function set_button_text( $content ) {
		$btn_text = $content;
		if ( empty( $btn_text ) ) {
			$admin_settings = FLW_Admin_Settings::get_instance();
			$btn_text = $admin_settings->get_option_value( 'btn_text' );
		}
		$this->button_text =  $btn_text ?? __( 'Pay Now', 'flutterwave-payments' );
	}

	public function get_attributes(): array {
		return $this->attributes;
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
		$email = self::use_current_user_email( $attributes ) ? wp_get_current_user()->user_email : '';
		$admin_payment_method = $this->settings->get_option_value( 'method' );
		$payment_method = self::get_payment_options()[ $admin_payment_method ] ?? self::get_payment_options()[ 'all' ];
		return shortcode_atts(
			array(
				'amount' => 100,
				'currency' => $this->settings->get_option_value('currency'),
				'country' => $this->settings->get_option_value('country'),
				'payment_method' => $payment_method,
				'email' => $email,
			),
			$attributes,
			$this->type
		);
	}

	protected function parse_query_args(): array {
		return array();
	}

	private static function get_payment_options(): array {
		return array(
			'both' => 'card,account',
			'card' => 'card',
			'account' => 'account',
			'all' => 'card,account,ussd,qr,mpesa,banktransfer,mobilemoneyghana,mobilemoneyfranco,mobilemoneyuganda,mobilemoneyrwanda,mobilemoneyzambia,barter,credit',
		);
	}

	public function render(): void {
		$atts = $this->get_attributes();
		$btn_text = $this->button_text;
		$data_attr = '';
		foreach ($atts as $att_key => $att_value) {

			if(!is_array($att_value)){
				$data_attr .= ' data-' . $att_key . '="' . $att_value . '"';
			}
		}
		include( FLW_DIR_PATH . 'views/pay-now-form.php' );
	}

	public function load_scripts(): void {
		$settings = $this->settings;

		$admin_payment_method = $this->settings->get_option_value( 'method' );
		$payment_method = self::get_payment_options()[ $admin_payment_method ] ?? self::get_payment_options()[ 'all' ];

		$args = array(
			'cb_url'     => admin_url( 'admin-ajax.php' ),
			'country'    => $settings->get_option_value( 'country' ),
			'currency'   => $settings->get_option_value( 'currency' ),
			'desc'       => $settings->get_option_value( 'modal_desc' ),
			'logo'       => $settings->get_option_value( 'modal_logo' ),
			'method'     => $payment_method,
			'public_key' => $settings->get_option_value( 'public_key' ),
			'title'      => $settings->get_option_value( 'modal_title' ),
			'countries' => self::get_supported_country()
		);

		wp_enqueue_script( 'flw_checkout_js', 'https://checkout.flutterwave.com/v3.js', array(), FLW_PAY_VERSION, true );
		wp_enqueue_script( 'flw_pay_js', FLW_DIR_URL . 'assets/js/flw.js', array( 'flw_checkout_js', 'jquery' ), FLW_PAY_VERSION, true );

		wp_enqueue_script( 'flwdonation_js', FLW_DIR_URL . 'assets/js/flw-donation.js', array( 'jquery' ), FLW_PAY_VERSION, true );

		wp_localize_script( 'flw_pay_js', 'flw_pay_options', $args );
	}
}