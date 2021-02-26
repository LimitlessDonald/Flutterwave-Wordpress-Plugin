<?php

  /**
   * Rave API class
   */

  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }

  


  if ( ! class_exists( 'FLW_Rave_Api' ) ) {


    /**
     * Main Plugin Class
     */
    class FLW_Rave_Api {

        private $api_base_url = 'https://api.ravepay.co/';
        // private $api_base_url = 'http://flw-pms-dev.eu-west-1.elasticbeanstalk.com/';
      
        function __construct() {
          $this->_init();
          
        }

        function _init(){
            if ($this->get_option_value('go_live' ) === 'yes' ) {
                $this->api_base_url = 'https://api.ravepay.co/';
            }


        }

              /**
       * Fetches admin option settings from the db
       *
       * @param  string $setting The option to fetch
       *
       * @return mixed           The value of the option fetched
       */
      public function get_option_value( $attr ) {

        $options = get_option( 'flw_rave_options' );

        if ( array_key_exists($attr, $options) ) {

          return $options[$attr];

        }

        return '';

      }



       /**
       * Exposes the api base url
       *
       * @return string rave api base url
       */
      function get_api_base_url() {

        return $this->api_base_url;

      }

        //get CURL
      function getCURL($url) {
        $args = array(
          'header' => ['Content-Type'=>'application/json'],
        );
        $request = wp_remote_get($url, $args );

        if ( is_array( $request ) && ! is_wp_error( $request ) ) {

          $result = json_decode($request['body'], true); 
          
          return $result;
        }

        if(is_wp_error( $request )){
          $result =  ['message' => 'You need to check your Network Connection'];
          return $result;

        }

       

       



        
    }

    //post CURL
      function postCURL($url, $data) {

        $request = wp_remote_post($url, array(
            'headers' => ['Content-Type'=> 'application/json'],
            'body' => wp_json_encode($data)
        ) );
    }


       /**
       * Gets the merchants payment plans
       *
       * @return string rave list of payment plans
       */
      function get_existing_payment_plans() {

        $url = $this->api_base_url . "v2/gpx/paymentplans/query?seckey=".$this->get_option_value( 'secret_key' );

        return $this->getCURL($url);

      }


      function get_secret_key(){
        return $this->get_option_value( 'secret_key' );
      }

       /**
       * Gets the merchants a payment plan
       *
       * @return string a rave payment plans
       */
      function get_existing_payment_plan($payment_plan_id, $q = "") {

        // $payment_plan_id = 5873;
        $url = $this->api_base_url . "v2/gpx/paymentplans/query?seckey=".$this->get_option_value( 'secret_key' )."&id=".$payment_plan_id."&q=".$q;

        return $this->getCURL($url);

      }

        /**
       * Create a new payment plan
       *
       * @return string rave api payment
       */
      function create_payment_plan($amount, $plan_name, $interval, $duration) {
        $url = $this->api_base_url . "v2/gpx/paymentplans/create";
        return $this->postCURL($url,[
            "seckey" => $this->get_option_value( 'secret_key' ),
            "amount" => (int)$amount,
            "name" => $plan_name,
            "interval" => $interval,
            "duration" => (int)$duration,
            ]);
      }
      


        /**
       *cancel a payment plan
       *
       * @return string a rave cancel payment plans
       */
      function get_cancel_payment_plan() {

        $payment_plan_id = 5873;
        $url = $this->api_base_url . "v2/gpx/paymentplans/".$payment_plan_id."/cancel";
        return $this->postCURL($url,["seckey" => $this->get_option_value( 'secret_key' )]);

      }

        /**
       *cancel a payment plan
       *
       * @return string a rave edit a payment plans
       */
      function edit_payment_plan() {

        $payment_plan_id = 5873;
        $url = $this->api_base_url . "v2/gpx/paymentplans/".$payment_plan_id."/edit";
        return $this->postCURL($url,[
            "seckey" => $this->get_option_value( 'secret_key' ),
            "name" => $this->get_option_value( 'payment_plan_name' ),
            "status" => $this->get_option_value( 'payment_plan_status' ),
            ]);

      }

    



  }

}

  ?>