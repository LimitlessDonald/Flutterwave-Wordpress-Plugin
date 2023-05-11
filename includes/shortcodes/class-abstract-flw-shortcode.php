<?php

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
	protected static function get_logo_url($attr) {
		$admin_settings = FLW_Admin_Settings::get_instance();
		$logo = $admin_settings->get_option_value( 'modal_logo' );
		if ( ! empty( $attr['logo'] ) ) {
			$logo = strpos( $attr['logo'], 'http' ) != false ? $attr['logo'] : wp_get_attachment_url( $attr['logo'] );
		}
		return $logo;
	}

	protected static function get_supported_country(): array {
		return array(
			'NGN' => 'NG',
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
}