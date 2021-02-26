<?php
  /**
   * Shortcode Class
   */

  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }

  if ( ! class_exists( 'FLW_Rave_Shortcode' ) ) {

    class FLW_Rave_Shortcode {

      /**
       * Class instance variable
       *
       * @var $instance
       */
      protected static $instance = null;

      function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'load_css_files' ) );
        add_shortcode( 'flw-pay-button', array( $this, 'pay_button_shortcode' ) );
        add_shortcode( 'flw-donation-page', array( $this, 'donation_page_shortcode' ) );

      }

      /**
       * Get the instance of this class
       *
       * @return object the single instance of this class
       */
      public static function get_instance() {

        if ( null == self::$instance ) {
          self::$instance = new self;
        }

        return self::$instance;

      }

      /**
       * Generates Pay Now button from shortcode
       *
       * @param  array $attr Array of attributes from the shortcode
       *
       * @return string      Pay Now button html content
       */
      public function pay_button_shortcode( $attr, $content="" ) {

        if(!is_array($attr)){
          $attr = [];
        }

        global $admin_settings;
        global $payment_forms;
        global $payment_plans_settings;

        if ( ! $admin_settings->is_public_key_present() ) return;

        $btn_text = empty( $content ) ? $this->pay_button_text() : $content;
        $email = $this->use_current_user_email( $attr ) ? wp_get_current_user()->user_email : '';
        if (!empty($this->get_logo_url($attr))) {
          $attr['logo'] = $this->get_logo_url($attr);
        }

        global $extra_fields;
        $extra_fields = explode(",", $payment_forms->get_option_value('extra_field_1'));
        $extra_fields_checkbox = explode(",", $payment_forms->get_option_value('extra_field_2'));

        // getting the selected payment plan name
        if($payment_plans_settings->get_option_value('recurring_payment_plan_1_enable') === 'yes' && $payment_plans_settings->get_option_value('recurring_payment_plan_1')){
           $plan_name_1 = $payment_plans_settings->get_payment_plan_name($payment_plans_settings->get_option_value('recurring_payment_plan_1'));
          }else{
            $plan_name_1 = "";
          }
        
        if($payment_plans_settings->get_option_value('recurring_payment_plan_2_enable') === 'yes' && $payment_plans_settings->get_option_value('recurring_payment_plan_2')){
          $plan_name_2 = $payment_plans_settings->get_payment_plan_name($payment_plans_settings->get_option_value('recurring_payment_plan_2'));
         }else{
           $plan_name_2 = "";
         }

         if($payment_plans_settings->get_option_value('recurring_payment_plan_3_enable') === 'yes' && $payment_plans_settings->get_option_value('recurring_payment_plan_3')){
          $plan_name_3 = $payment_plans_settings->get_payment_plan_name($payment_plans_settings->get_option_value('recurring_payment_plan_3'));
         }else{
          $plan_name_3 = "";
         }

         if($payment_plans_settings->get_option_value('recurring_payment_plan_4_enable') === 'yes' && $payment_plans_settings->get_option_value('recurring_payment_plan_4')){
          $plan_name_4 = $payment_plans_settings->get_payment_plan_name($payment_plans_settings->get_option_value('recurring_payment_plan_4'));
         }else{
          $plan_name_4 = "";
         }

        $main_option_default = array(
          'amount'    => '',
          'custom_currency' => [],
          'email'     => $email,
          'country'   => $admin_settings->get_option_value('country'),
          'currency'  => $admin_settings->get_option_value('currency'),
          'recurring_payment'  => $payment_plans_settings->get_option_value('recurring_payment'),
          'extra_count' => count($extra_fields),
          'extra_checkbox_count' => count($extra_fields_checkbox),
          'paymentplans' => [
            $payment_plans_settings->get_option_value('recurring_payment_plan_1') => $plan_name_1,
            $payment_plans_settings->get_option_value('recurring_payment_plan_2') => $plan_name_2,
            $payment_plans_settings->get_option_value('recurring_payment_plan_3') => $plan_name_3,
            $payment_plans_settings->get_option_value('recurring_payment_plan_4') => $plan_name_4,
          ],
          'paymentplansenable' => [
            $payment_plans_settings->get_option_value('recurring_payment_plan_1') => $payment_plans_settings->get_option_value('recurring_payment_plan_1_enable'),
            $payment_plans_settings->get_option_value('recurring_payment_plan_2') => $payment_plans_settings->get_option_value('recurring_payment_plan_2_enable'),
            $payment_plans_settings->get_option_value('recurring_payment_plan_3') => $payment_plans_settings->get_option_value('recurring_payment_plan_3_enable'),
            $payment_plans_settings->get_option_value('recurring_payment_plan_4') => $payment_plans_settings->get_option_value('recurring_payment_plan_4_enable'),
          ]
          );

          foreach ($extra_fields as $key => $value) {
            $attr[$value] = '';
            $main_option_default[$value] = '';
          }
          foreach ($extra_fields_checkbox as $key => $value) {
            $attr[$value] = '';
            $main_option_default[$value] = '';
          }

        $atts = shortcode_atts( $main_option_default , $attr );



        $this->load_js_files();

        ob_start();
        $this->render_payment_form( $atts, $btn_text );
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

        global $admin_settings;
        global $payment_forms;
        global $payment_plans_settings;

        wp_register_style('flw_donation_css', FLW_DIR_URL . 'assets/css/flw-donation.css');
        if ( ! $admin_settings->is_public_key_present() ) return;

        $this->load_js_files();

        ob_start();
        
        $this->render_donation_page();
        $form = ob_get_contents();
        ob_end_clean();

        return $form;

      }

      public function render_payment_form( $atts, $btn_text ) {        

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
      public function load_js_files() {

        global $admin_settings;
        global $flw_pay_class;

        $args = array(
          'cb_url'    => admin_url( 'admin-ajax.php' ),
          'country'   => $admin_settings->get_option_value( 'country' ),
          'currency'  => $admin_settings->get_option_value( 'currency' ),
          'desc'      => $admin_settings->get_option_value( 'modal_desc' ),
          'logo'      => $admin_settings->get_option_value( 'modal_logo' ),
          'method'    => $admin_settings->get_option_value( 'method' ),
          'pbkey'     => $admin_settings->get_option_value( 'public_key' ),
          'title'     => $admin_settings->get_option_value( 'modal_title' ),
        );

        wp_enqueue_script( 'flwpbf_inline_js', $flw_pay_class->get_api_base_url() . 'flwv3-pug/getpaidx/api/flwpbf-inline.js', array(), '1.0.0', true );
        wp_enqueue_script( 'flw_js', FLW_DIR_URL . 'assets/js/flw.js', array( 'flwpbf_inline_js', 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'flwdonation_js', FLW_DIR_URL . 'assets/js/flw-donation.js', array( 'jquery' ), '1.0.0', true );

        wp_localize_script( 'flw_js', 'flw_rave_options', $args );

      }
      /**
       * Loads css files
       *
       * @return void
       */
      public function load_css_files() {

        global $admin_settings;

        if ( 'yes' !== $admin_settings->get_option_value( 'theme_style' ) ) {
          wp_enqueue_style( 'flw_css', FLW_DIR_URL . 'assets/css/flw.css', false );
        }

      }

      /**
       * Get pay now button text
       *
       * @return string Button text
       */
      private function pay_button_text() {

        global $admin_settings;

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
      private function use_current_user_email( $attr ) {

        return isset( $attr['use_current_user_email'] ) && $attr['use_current_user_email'] === 'yes';

      }

      private function get_logo_url($attr) {

        global $admin_settings;

        $logo = $admin_settings->get_option_value( 'modal_logo' );
        if ( ! empty( $attr['logo'] ) ) {
          $logo = strpos( $attr['logo'], 'http' ) != false ? $attr['logo'] : wp_get_attachment_url( $attr['logo'] );
        }

        return $logo;

      }

    }

  }
?>
