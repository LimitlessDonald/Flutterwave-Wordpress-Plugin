<?php
  /**
   * Rave Payment Forms
   */
  if ( ! defined( 'ABSPATH' ) ) { exit; }

  class FLW_Rave_Payment_Form
  {
       /**
       * Class instance
       * @var $instance
       */
      public static $instance = null;

      /**
       * Admin options array
       *
       * @var array
       */
      protected $options;

      function __construct(){
        
        add_action( 'admin_menu', array( $this, 'add_to_menu' ) );
        //call register settings function
        add_action( 'admin_init', array($this, 'register_form_settings') );
        $this->init_settings();

      }

            /**
       * Get the instance of the class
       *
       * @return object   An instance of this class
       */
      public static function get_instance() {

        if ( null == self::$instance ) {

          self::$instance = new self;

        }

        return self::$instance;

      }

      private function init_settings() {

        if ( false == get_option( 'extra_form_options' ) ) {
          update_option( 'extra_form_options', array() );
        }

      }


      public function add_to_menu() {

        add_submenu_page(
          'rave-payment-forms',
          __( 'Rave Payment Forms', 'rave-pay' ),
          __( 'Forms', 'rave-pay' ),
          'manage_options',
          'rave-payment-mad-form',
          array( $this, 'flw_rave_admin_form_page' )
        );

        

      }

      /**
       * Admin page content
       * @return void
       */
      public function flw_rave_admin_form_page() {

        include_once( FLW_DIR_PATH . 'views/admin-forms-page.php' );

      }


       /**
       * Fetches admin option settings from the db
       *
       * @param  string $setting The option to fetch
       *
       * @return mixed           The value of the option fetched
       */
      public function get_option_value( $attr ) {

        $options = get_option( 'extra_form_options' );

        if ( array_key_exists($attr, $options) ) {

          return $options[$attr];

        }

        return '';

      }


      public function register_form_settings() {
        //register our settings
        register_setting( 'form-settings-group', 'extra_form_options' );

      }







  }
  

