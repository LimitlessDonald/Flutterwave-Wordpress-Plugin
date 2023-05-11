<?php
/**
 * Shortcode Class
 *
 * @package Flutterwave_Payments
 */

defined( 'ABSPATH' ) || exit;

class FLW_Shortcode {

  /**
   * Class instance variable
   *
   * @var ?FLW_Shortcode $instance
   */
  protected static ?FLW_Shortcode $instance = null;

  /**
   * Class constructor
   */
  public function __construct() {}

  /**
   * Get the instance of this class
   *
   * @return object the single instance of this class
   */
  public static function get_instance() {

    if ( null === self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;

  }

  /**
   * Generates Pay Now button from shortcode
   *
   * @param array $attr Array of attributes from the shortcode
   *
   * @return string      Pay Now button html content
   */
  public static function pay_button_shortcode( array $attr = [], $content="" ): string {

	  $admin_settings = FLW_Admin_Settings::get_instance();

    if ( ! $admin_settings->is_public_key_present() ) return '';

    $btn_text = empty( $content ) ? self::pay_button_text() : $content;
    $email = self::use_current_user_email( $attr ) ? wp_get_current_user()->user_email : '';
    if (!empty(self::get_logo_url($attr))) {
      $attr['logo'] = self::get_logo_url($attr);
    }

    $main_option_default = array(
      'amount'    => '',
      'custom_currency' => [],
      'email'     => $email,
      'country'   => $admin_settings->get_option_value('country'),
      'currency'  => $admin_settings->get_option_value('currency'),
    );

    $atts = shortcode_atts( $main_option_default , $attr );

    self::load_js_files();

    ob_start();
    self::render_payment_form( $atts, $btn_text );
    $form = ob_get_contents();
    ob_end_clean();
    return $form;
  }

  /**
   * Generates Donation page from shortcode
   *
   * @param  array $attr Array of attributes from the shortcode
   *
   * @return string      Pay Now button html content
   */
  public function donation_page_shortcode( $attr, $content="" ) {

	  $admin_settings = FLW_Admin_Settings::get_instance();

    wp_register_style('flw_donation_css', FLW_DIR_URL . 'assets/css/flw-donation.css');
    if ( ! $admin_settings->is_public_key_present() ) return '';

    $this->load_js_files();

    ob_start();

    $this->render_donation_page();
    $form = ob_get_contents();
    ob_end_clean();

    return $form;

  }

  public static function render_payment_form( $atts, $btn_text ) {

    $data_attr = '';
    foreach ($atts as $att_key => $att_value) {

      if(!is_array($att_value)){
        $data_attr .= ' data-' . $att_key . '="' . $att_value . '"';
      }


    }
    include( FLW_DIR_PATH . 'views/pay-now-form.php' );
  }


  public function render_donation_page(){

    wp_enqueue_style( 'flw_donation_css');
    // wp_enqueue_style( 'flw_donation_css', FLW_DIR_URL . 'assets/css/flw-donation.css', false );
    include( FLW_DIR_PATH . 'views/donation-payment.php' );
  }

  /**
   * Loads javascript files
   *
   * @return void
   */
  public static function load_js_files() {

    $settings = FLW_Admin_Settings::get_instance();

    $args = array(
      'cb_url'    => admin_url( 'admin-ajax.php' ),
      'country'   => $settings->get_option_value( 'country' ),
      'currency'  => $settings->get_option_value( 'currency' ),
      'desc'      => $settings->get_option_value( 'modal_desc' ),
      'logo'      => $settings->get_option_value( 'modal_logo' ),
      'method'    => $settings->get_option_value( 'method' ),
      'pbkey'     => $settings->get_option_value( 'public_key' ),
      'title'     => $settings->get_option_value( 'modal_title' ),
    );

    wp_enqueue_script( 'flw_checkout_js', 'https://checkout.flutterwave.com/v3.js', array(), FLW_PAY_VERSION, true );
    wp_enqueue_script( 'flw_pay_js', FLW_DIR_URL . 'assets/js/flw.js', array( 'flw_checkout_js', 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'flwdonation_js', FLW_DIR_URL . 'assets/js/flw-donation.js', array( 'jquery' ), '1.0.0', true );

    wp_localize_script( 'flw_pay_js', 'flw_pay_options', $args );

  }
  /**
   * Loads css files
   *
   * @return void
   */
  public static function load_css_files() {
	  $admin_settings = FLW_Admin_Settings::get_instance();
    if ( 'yes' !== $admin_settings->get_option_value( 'theme_style' ) ) {
      wp_enqueue_style( 'flw_css', FLW_DIR_URL . 'assets/css/flw.css', false );
    }
  }

  /**
   * Get pay now button text
   *
   * @return string Button text
   */
  private static function pay_button_text() {
	  $admin_settings = FLW_Admin_Settings::get_instance();
    $text = $admin_settings->get_option_value( 'btn_text' );
    if ( empty( $text ) ) {
      $text = 'PAY NOW';
    }

    return $text;

  }

  /**
   * Checks if the loggedin user email should be used
   *
   * @param  array $attr attributes from shortcode
   *
   * @return boolean
   */
  private static function use_current_user_email( $attr ) {

    return isset( $attr['use_current_user_email'] ) && $attr['use_current_user_email'] === 'yes';

  }

  private static function get_logo_url($attr) {
	  $admin_settings = FLW_Admin_Settings::get_instance();
    $logo = $admin_settings->get_option_value( 'modal_logo' );
    if ( ! empty( $attr['logo'] ) ) {
      $logo = strpos( $attr['logo'], 'http' ) != false ? $attr['logo'] : wp_get_attachment_url( $attr['logo'] );
    }
    return $logo;
  }

}
