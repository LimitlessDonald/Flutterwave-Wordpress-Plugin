<?php
/*
 * Plugin Name: Flutterwave Payments
 * Plugin URI: http://flutterwave.com/
 * Description: Flutterwave payment gateway forms, accept local and international payments securely.
 * Version: 1.0.6
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Flutterwave Developers
 * Author URI: https://developer.flutterwave.com/
 * Copyright: Â© 2023 Flutterwave Technology Solutions
 * License: MIT License
 * Text Domain: flutterwave-payments
 * Domain Path: i18n/languages
 * Requires at least:      5.6
 * Requires PHP:           7.4
 *
 * @package Flutterwave Payments
 */

 declare(strict_types=1);

 defined( 'ABSPATH' ) || exit;

if ( ! defined( 'FLW_PAY_PLUGIN_FILE' ) ) {
  define( 'FLW_PAY_PLUGIN_FILE', __FILE__ );
}

  if( ! defined( 'FLW_PAY_VERSION' ) ) {
    define( 'FLW_PAY_VERSION', '1.0.5' );
  }

  // Plugin folder path
  if ( ! defined( 'FLW_DIR_PATH' ) ) {
    define( 'FLW_DIR_PATH', plugin_dir_path( __FILE__ ) );
  }

  //Plugin folder path
  if ( ! defined( 'FLW_DIR_URL' ) ) {
    define( 'FLW_DIR_URL', plugin_dir_url( __FILE__ ) );
  }

  require_once( FLW_DIR_PATH . 'includes/flutterwave-base-class.php' );

  $flw_pay_class = FLW_Rave_Pay::get_instance();

?>
