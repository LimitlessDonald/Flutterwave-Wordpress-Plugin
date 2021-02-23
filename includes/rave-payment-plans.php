<?php
  /**
   * Rave Payment Forms
   */
  if ( ! defined( 'ABSPATH' ) ) { exit; }

  require_once( FLW_DIR_PATH . 'includes/Flutterwave-PHP-SDK/paymentPlan.php' );

  class FLW_Rave_Payment_Plan
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

        $this->flutterwave_api = new FLW_Rave_Api();
        add_action( 'admin_menu', array( $this, 'add_to_menu' ) );
        //call register settings function
        add_action( 'admin_init', array($this, 'register_payment_form_settings') );
 

        $this->init_settings();
        $this->_include_files();
        $this->existing_payment_plans = (array_key_exists('data', $this->flutterwave_api->get_existing_payment_plans()))?$this->flutterwave_api->get_existing_payment_plans()['data']['paymentplans']:$this->flutterwave_api->get_existing_payment_plans()['message'] ;

    
        //$this->existing_payment_plans = [];
        
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

        if ( false == get_option( 'new_plan_options' ) ) {
          update_option( 'new_plan_options', array() );
        }

      }

      private function _include_files(){

        
      }


       
     
      function get_api_base_url() {

        return $this->flutterwave_api->api_base_url();

      }

      


      public function add_to_menu() {

        add_submenu_page(
          'rave-payment-forms',
          __( 'Rave Payment Forms', 'rave-pay' ),
          __( 'Payment plans', 'rave-pay' ),
          'manage_options',
          'rave-payment-plan-form',
          array( $this, 'flw_rave_admin_payment_plan_page' )
        );

        

      }

      /**
       * Admin page content
       * @return void
       */
      public function flw_rave_admin_payment_plan_page() {

        
        include_once( FLW_DIR_PATH . 'views/admin-payment-plan.php' );
        // add_action('admin_post_create_new_plan_hook', 'create_new_plan');
      }


       /**
       * Fetches admin option settings from the db
       *
       * @param  string $setting The option to fetch
       *
       * @return mixed           The value of the option fetched
       */
      public function get_option_value( $attr ) {

        $options = get_option( 'new_plan_options' );

        if ( array_key_exists($attr, $options) ) {

          return $options[$attr];

        }

        return '';

      }


      public function register_payment_form_settings() {
        //register our settings
        register_setting( 'paymentplan-settings-group', 'new_plan_options' );

      }

      public function get_secret_key(){
          return $this->flutterwave_api->get_secret_key();
      }

      public function get_payment_plan_name($id){
        $data = $this->flutterwave_api->get_existing_payment_plan($id)['data']['paymentplans'][0]['name'];
        return $data;
      }


      // public function create_new_plan(){

      //   if($_POST['createPlan-btn']){
      //       $this->flutterwave_api->create_payment_plan(5000, "testing2", "monthly", 5);
      //       $amount = $_POST['plan-amount'];
      //       $plan_name = $_POST['plan-name'];
      //       $interval = $_POST['plan-duration'];
      //       $duration = $_POST['plan-interval'];

      //       $entries = compact('amount', 'plan_name', 'interval', 'duration');
      //       $print_r($entries);
           
      //       $response = $this->flutterwave_api->create_payment_plan((int)$amount, $plan_name, $interval, (int)$duration);
           
      //   }


      // }




  }
  

