<?php

/**
 * Rave API class
 */
if ( ! function_exists( 'rpf_wp_remote_put' ) ) {
	function rpf_wp_remote_put($url, $args) {
		$defaults = array('method' => 'PUT');
		$r = wp_parse_args( $args, $defaults );
		return wp_remote_request($url, $r);
	}
}

if ( ! function_exists( 'rpf_wp_remote_delete' ) ) {
	function rpf_wp_remote_delete($url, $args) {
		$defaults = array('method' => 'DELETE');
		$r = wp_parse_args( $args, $defaults );
		return wp_remote_request($url, $r);
	}
}

if ( ! class_exists( 'FLW_Rave_Api' ) ) {


	/**
	 * Main Plugin Class
	 */
	class FLW_Rave_Api {

		private string $api_base_url = 'https://api.flutterwave.com/v3/';
		private ?string $secret_key = null;

		private static int $count = 0;

		function __construct() {
			$this->_init();

		}

		function _init()
		{
			if ($this->get_option_value('go_live' ) === 'yes' ) {
				//$this->api_base_url = 'https://api.ravepay.co/';
			}
		}

		private function get_headers(): array {
			return ['Content-Type'=>'application/json',
			        'Authorization' => 'Bearer '.$this->get_option_value( 'secret_key' )
			];
		}

		/**
		 * Fetches admin option settings from the db
		 *
		 * @param $attr
		 *
		 * @return mixed           The value of the option fetched
		 */
		function get_option_value( $attr ) {

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
		function get_api_base_url(): string
		{
			return $this->api_base_url;
		}

		private function request($url, $data = [], $method = 'GET') {
			$methods = [
				'GET' => $this->getRequest($url),
				'POST' => $this->postRequest($url, $data),
				'PUT' => $this->putRequest($url, $data),
			];

			return $methods[$method];
		}

		//get CURL

		/**
		 * @param $url
		 *
		 * @return mixed|string[]|void
		 */
		function getRequest($url) {
			$request = wp_remote_get($url, [ 'headers' => $this->get_headers() ] );

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

		/**
		 * @param $url
		 * @param $data
		 *
		 * @return mixed|string[]|void
		 */
		function postRequest($url, $data) {

			$request = wp_remote_post($url, [
				'headers' => $this->get_headers(),
				'body' => wp_json_encode($data)
			]);

			if ( is_array( $request ) && ! is_wp_error( $request ) ) {

				$result = json_decode($request['body'], true);

				return $result;
			}

			if(is_wp_error( $request )){
				$result =  ['message' => 'You need to check your Network Connection'];
				return $result;

			}
		}

		//put CURL

		/**
		 * @param $url
		 * @param $data
		 *
		 * @return mixed|string[]|void
		 */
		private function putRequest( $url, $data ) {
			$request = rpf_wp_remote_put($url, []);

			if ( is_array( $request ) && ! is_wp_error( $request ) ) {

				$result = json_decode($request['body'], true);

				return $result;
			}

			if(is_wp_error( $request )){
				$result =  ['message' => 'You need to check your Network Connection'];
				return $result;
			}
		}


		/**
		 * Gets the merchants payment plans
		 *
		 * @return string rave list of payment plans
		 */
		function get_existing_payment_plans() {

			$url = $this->api_base_url . 'payment-plans';

			return $this->getRequest($url);

		}


		function get_secret_key(){
			return $this->get_option_value( 'secret_key' );
		}


		function get_existing_payment_plan($plan_id, $q = "") {
			$url = $this->api_base_url . 'payment-plans/'.$plan_id;
			return $this->getRequest($url);
		}

		function create_payment_plan($amount, $plan_name, $interval, $duration)
		{
			$url = $this->api_base_url . "payment-plans";

			if($plan_name == ""){
				self::$count++;
				$num = self::$count;
				$plan_name = "rpf_plan_{ $num }";
			}

			return $this->postRequest($url,[
				"amount" => (int)$amount,
				"name" => (string)$plan_name,
				"interval" => (string)$interval,
				"duration" => (int)$duration,
			]);
		}

		function get_cancel_payment_plan(string $plan_id): string {
			$url = $this->api_base_url . "payment-plans/".$plan_id."/cancel";
			return $this->request($url, null , 'PUT');
		}

		function edit_payment_plan(string $plan_id, float $amount, string $status): string {
			$url = $this->api_base_url . "paymentplans/".$plan_id;

			return $this->putRequest($url,[
				"amount" => $amount,
				"status" => $status,
			]);
		}

	}

}
