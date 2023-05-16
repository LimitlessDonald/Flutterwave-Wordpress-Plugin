<?php
/*
 * Abstract Flutterwave Shortcode Class.
 *
 * @package Flutterwave-Payments
 */

defined( 'ABSPATH' ) || exit;

abstract class Abstract_FLW_Shortcode {

	/**
	 * Shortcode type.
	 *
	 * @since 1.0.6
	 * @var   string
	 */
	protected string $type = '';

	/**
	 * Attributes.
	 *
	 * @since 1.0.6
	 * @var   array
	 */
	protected array $attributes = array();

	/**
	 * Query args.
	 *
	 * @since 1.0.6
	 * @var   array
	 */
	protected array $query_args = array();

	/**
	 * Set custom visibility.
	 *
	 * @since 1.0.6
	 * @var   bool
	 */
	protected bool $custom_visibility = false;

	/**
	 * Settings.
	 *
	 * @since 1.0.6
	 * @var   FLW_Admin_Settings|null
	 */
	protected ?FLW_Admin_Settings $settings;

	abstract protected function parse_attributes( array $attributes = array() ): array;

	abstract protected function parse_query_args(): array;

	abstract public function render(): void;

	abstract public function load_scripts(): void;

	public function __construct( array $attributes, string $type ) {
		$this->type       = $type;
		$this->settings   = FLW_Admin_Settings::get_instance();
		$this->attributes = $this->parse_attributes( $attributes );
		$this->query_args = $this->parse_query_args();
	}

	/**
	 * Checks if the loggedin user email should be used
	 *
	 * @param $attr
	 *
	 * @return boolean
	 */
	protected static function use_current_user_email( $attr ): bool {

		return isset( $attr['use_current_user_email'] ) && $attr['use_current_user_email'] === 'yes';

	}

	/**
	 * Get the current user email
	 *
	 * @return string
	 */
	protected static function get_logo_url( $attr ) {
		$admin_settings = FLW_Admin_Settings::get_instance();
		$logo           = $admin_settings->get_option_value( 'modal_logo' );
		if ( ! empty( $attr['logo'] ) ) {
			$logo = strpos( $attr['logo'], 'http' ) != false ? $attr['logo'] : wp_get_attachment_url( $attr['logo'] );
		}
		return $logo;
	}

	protected static function get_supported_country(): array {
		return array(
			'NGN' => 'NG',
			'EUR' => 'NG',
			'GBP' => 'NG',
			'USD' => 'US',
			'KES' => 'KE',
			'ZAR' => 'ZA',
			'TZS' => 'TZ',
			'UGX' => 'UG',
			'GHS' => 'GH',
			'ZMW' => 'ZM',
			'RWF' => 'RW',
		);
	}

	protected static function get_payment_options(): array {
		return array(
			'both'    => 'card,account',
			'card'    => 'card',
			'account' => 'account',
			'all'     => 'card,account,ussd,qr,mpesa,banktransfer,mobilemoneyghana,mobilemoneyfranco,mobilemoneyuganda,mobilemoneyrwanda,mobilemoneyzambia,barter,credit',
		);
	}

	protected function get_field_data_type( ?string $key = null ) {

		$data = array(
			'email' => array(
				'id' => 'flw-customer-email',
				'name' => 'email',
				'class' => 'flw-form-input-text',
				'type' => 'text',
				'placeholder' => __( 'Email', 'flutterwave-payments' )
			),
			'amount' => array(
				'id' => 'flw-amount',
				'name' => 'amount',
				'class' => 'flw-form-input-text',
				'type' => 'number',
				'placeholder' => __( 'Amount', 'flutterwave-payments' )
			),
			'currency' => array(
				'id' => 'flw-currency',
				'name' => 'custom_currency',
				'class' => 'flw-form-select',
				'type' => 'select',
				'label' => __( 'Currency', 'flutterwave-payments' )
			),
			'custom_currency' => array(
				'id' => 'flw-currency',
				'name' => 'custom_currency',
				'class' => 'flw-form-select',
				'type' => 'select',
				'label' => __( 'Currency', 'flutterwave-payments' )
			),
			'fullname' => array(
				'id' => 'flw-full-name',
				'name' => 'fullname',
				'class' => 'flw-form-input-text',
				'type' => 'text',
				'placeholder' => __( 'Full Name', 'flutterwave-payments' )
			),
			'phone'    => array(
				'id' => 'flw-phone',
				'name' => 'phone',
				'class' => 'flw-form-input-text',
				'type' => 'tel',
				'placeholder' => __( 'Phone Number', 'flutterwave-payments' )
			),
			'firstname' => array(
				'id' => 'flw-first-name',
				'name' => 'firstname',
				'class' => 'flw-form-input-text',
				'type' => 'text',
				'placeholder' => __( 'First Name', 'flutterwave-payments' )
			),
			'lastname' => array(
				'id' => 'flw-last-name',
				'name' => 'lastname',
				'class' => 'flw-form-input-text',
				'type' => 'text',
				'placeholder' => __( 'Last Name', 'flutterwave-payments' )
			),
			'country' => 'text'
		);

		if( is_null( $key ) ) {
			return $data;
		}

		return $data[ $key ];
	}

	protected static function get_allowed_html() {
		return array(
			'div'   => array(), 
			'input' => array(
				'id'          => array(),
				'class'       => array(),
				'type'        => array(),
				'placeholder' => array(),
				'required' => array(),
			),
			'select' => array(
				'id'          => array(),
				'class'       => array(),
				'required' => array(),
			),
			'option' => array(
				'value' => array()
			) ,
			'label' => array(
				'class' => array()
			) 
		);	
	}
}
