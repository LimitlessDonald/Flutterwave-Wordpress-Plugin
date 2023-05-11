<?php
/**
 * Flutterwave base class
 *
 * @package Flutterwave_Payments
 */

use Flutterwave\WordPress\API\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin Class
 */
final class Flutterwave_Payments {

  /**
   * Plugin name
   * Plugin name
   * @var string $plugin_name
   */
  private string $plugin_name = 'flutterwave-payments';
  /**
   * Plugin version
   * @var string $plugin_version
   */
  private string $plugin_version = '1.0.6';

  /**
   * Instance variable
   * @var Flutterwave_Payments|null $instance
   */
  protected static ?Flutterwave_Payments $instance = null;

  /**
   * API Client
   * @var Client $api_client
   */
  protected Client $api_client;
  private ?object $_settings;

  /**
   * Class constructor
   */
  public function __construct() {
    $this->define_constants();
    $this->_include_files();
    $this->_init();

    add_action( 'admin_notices', array( $this, 'admin_notices' ) );

    add_action( 'wp_ajax_process_payment', array( $this, 'process_payment' ) );
    add_action( 'wp_ajax_nopriv_process_payment', array( $this, 'process_payment' ) );

  }

  /**
   * Define constant if not already set.
   *
   * @param string      $name  Constant name.
   * @param string|bool $value Constant value.
   */
  private function define( string $name, $value ) {
    if ( ! defined( $name ) ) {
      define( $name, $value );
    }
  }

  private function define_constants() {
    $this->define( 'FLW_PAY_VERSION', $this->plugin_version );
    $this->define( 'FLW_DIR_PATH', plugin_dir_path( FLW_PAY_PLUGIN_FILE ) );
    $this->define( 'FLW_DIR_URL', plugin_dir_url( FLW_PAY_PLUGIN_FILE ) );
  }

  /**
   * Includes all required files
   *
   * @return void
   */
  private function _include_files() {
    require_once( FLW_DIR_PATH . 'includes/class-flw-admin-settings.php' );
    require_once( FLW_DIR_PATH . 'includes/class-flw-payment-list.php' );
    require_once( FLW_DIR_PATH . 'includes/vc-elements/class-flw-vc-simple-form.php' );
    require_once( FLW_DIR_PATH . 'src/API/Client.php' );
    require_once( FLW_DIR_PATH . 'includes/class-flw-shortcodes.php' );

    if ( is_admin() ) {
      require_once( FLW_DIR_PATH . 'includes/rave-tinymce-plugin-class.php' );
    }
  }

  /**
   * Initialize all the included classe
   *
   * @return void
   */
  private function _init() {

    if ( ! shortcode_exists('flw-pay-button') && ! shortcode_exists('flw-donation-page') ) {
        //include shortcodes.
        require_once( FLW_DIR_PATH . 'includes/shortcodes/class-abstract-flw-shortcode.php' );
        require_once( FLW_DIR_PATH . 'includes/shortcodes/class-flw-shortcode-donation-form.php' );
        require_once( FLW_DIR_PATH . 'includes/shortcodes/class-flw-shortcode-payment-form.php' );

        //initialize shortcodes.
        FLW_Shortcodes::get_instance();
    }

    $this->_settings = FLW_Admin_Settings::get_instance();
    $this->api_client = Client::get_instance( $this->get_option( 'secret_key' ) );

    if ( is_admin() ) {
      // TODO: Introduce Advanced TinyMCE Plugin.
      FLW_Tinymce_Plugin::get_instance();
    }
  }

  private function get_option( string $name ) {
    return $this->_settings->get_option_value( $name );
  }

  /**
   * Adds admin settings page to the dashboard
   *
   * @return void
   */
  public function admin_notices() {
    $no_public_key = empty( $this->get_option('public_key') ?? '' );
    $no_secret_key = empty( $this->get_option('secret_key') ?? '' );

    if ( $no_secret_key || $no_public_key ) {
      echo '<div class="updated"><p>';
      echo  __( 'Flutterwave Payments is installed. - ', 'flutterwave-payments' );
      echo "<a href=" . esc_url( add_query_arg( 'page', $this->plugin_name, admin_url( 'admin.php' ) ) ) . " class='button-primary'>" . __( 'Enter your Flutterwave "Pay Checkout" Public Key and Secret Key to start accepting payments', 'flutterwave-payments' ) . "</a>";
      echo '</p></div>';
    }

  }

  /**
   * Processes payment record information
   *
   * @return void
   */
  public function process_payment() {

    global $admin_settings;

    // TODO: Payment status should be pending at the moment.
    $redirect_url_key = 'failed_redirect_url';

    check_ajax_referer( 'flw-rave-pay-nonce', 'flw_sec_code' );

    $tx_ref = sanitize_text_field($_POST['txRef']);

    $res_data = json_decode( $this->_fetchTransaction( $tx_ref ) );

    if ( is_object($res_data->data) && $this->_is_successful( $res_data->data ) ) {
      $status            =  $res_data->data->status;
      $customer_fullname = $res_data->data->customer->name;
      $customer_email    = $res_data->data->customer->email;
      $customer_id       = $res_data->data->customer->id;
      $amount            = $res_data->data->amount;

      $args   =  array(
        'post_type'   => 'payment_list',
        'post_status' => 'publish',
        'post_title'  => $tx_ref,
      );

      $payment_record_id = wp_insert_post( $args, true );

      if ( ! is_wp_error( $payment_record_id )) {

        $post_meta = array(
          '_flw_rave_payment_amount'   => $amount,
          '_flw_rave_payment_fullname' => $customer_fullname,
          '_flw_rave_payment_customer' => $customer_email,
          '_flw_rave_payment_status'   => $status,
          '_flw_rave_payment_tx_ref'   => $tx_ref,
        );
        $this->_add_post_meta( $payment_record_id, $post_meta );
      }

      if ( 'successful' === $status ) {
        $redirect_url_key = 'success_redirect_url';
      }

      echo json_encode(
          array(
              'status' => $status,
              'redirect_url' => $admin_settings->get_option_value( $redirect_url_key )
          )
      );
      die();
    }

    echo json_encode(
        array(
            'status' => $res_data->status,
            'redirect_url' => $admin_settings->get_option_value( $redirect_url_key )
        )
    );
    die();
  }

  public static function gen_rand_string( $len = 4 ) {

    if ( version_compare( PHP_VERSION, '5.3.0' ) <= 0 ) {
        return substr( md5( rand() ), 0, $len );
    }
    return bin2hex( openssl_random_pseudo_bytes( $len/2 ) );

  }

  /**
   * Fetches transaction from flutterwave endpoint
   *
   * @param $tx_ref
   *
   * @return string
   */
  private function _fetchTransaction( $tx_ref ): string {
    //https://api.flutterwave.com/v3/transactions/verify_by_reference .
    $url = '/transactions/verify_by_reference?tx_ref='. $tx_ref;
    $response = $this->api_client->request($url);

    return wp_remote_retrieve_body( $response );
  }

  /**
   * Checks if payment is successful
   *
   * @param $data object the transaction object to do the check on
   *
   * @return boolean
   */
  private function _is_successful( object $data ): bool {
    return $data->status === 'successful';
  }

  /**
   * Adds metadata to payment list post type
   *
   * @param [int]   $post_id  The ID of the post to add metadata to
   * @param [array] $data     Collection of the data to be added to the post
   */
  private function _add_post_meta( $post_id, $data ) {

    foreach ($data as $meta_key => $meta_value) {
      update_post_meta( $post_id, $meta_key, $meta_value );
    }

  }

  /**
   * Gets the instance of this class
   *
   * @return object the single instance of this class
   */
  public static function get_instance() {

    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }
}



?>
