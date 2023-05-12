<?php
class FLW_Transaction_Rest_Route extends WP_REST_Controller {
	/**
	 * payment base_url.
	 *
	 * @var string
	 */
	protected $flw_base_url = 'https://api.flutterwave.com/v3/';

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'flutterwave-for-business/v1';

	/**
	 * Endpoint path.
	 *
	 * @var string
	 */
	protected $rest_base = 'payments/transactions';

	public function __construct() {
		$this->f4b_options = get_option( 'flw_rave_options' );
		add_action( 'rest_api_init', [ $this, 'create_rest_routes' ] );
	}

	public function create_rest_routes() {

		register_rest_route( $this->namespace, "/". $this->rest_base, [

			'methods' => WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_transactions' ],
			'permission_callback' => [ $this, 'get_transactions_permission' ]

		] );

		register_rest_route( $this->namespace, "/verifytransaction", [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [ $this, 'verifyPayment' ],
			'permission_callback' => [ $this, 'free_pass' ]

		] );

	}

	/**
	 * Retrieve settings.
	 *
	 * @return WP_REST_Response
	 */
	public function get_transactions(WP_REST_Request $request): WP_REST_Response {

		$page = $request->get_param('page');

		$token = $this->f4b_options['secret_key'];

		$response = wp_remote_get($this->flw_base_url."transactions/?page=$page", array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer '.$token
			)
		) );

		return new WP_REST_Response( json_decode($response['body']) );

	}

	public function get_transactions_permission() {
		return current_user_can( 'manage_options' );
	}

	public function free_pass(){
		return true;
	}

	public function verifyPayment(WP_REST_Request $request)
	{
		$token = $this->f4b_options['secret_key'];
		$success_url = $this->f4b_options['success_redirect_url'];
		$failer_url = $this->f4b_options['failed_redirect_url'];
		$txref = $request->get_param('tx_ref');
		$transactionId = $request->get_param('transaction_id');

		if(is_null($txref)) {
			return rest_ensure_response(new WP_REST_Response( null,
				302,
				array(
					'Location' => $_SERVER['HTTP_REFERER']
				)
			));
		}

		$url = 'https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref='. $txref;
		$response = wp_remote_get( $url, [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $token,
			],
		]);

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $response_body['data']['status'] != 'successful' ) {
			return rest_ensure_response(new WP_REST_Response( null,
				302,
				array(
					'Location' => $failer_url // or set any other URL you wish to redirect to
				)));
		}

		$this->_update_wordpress($txref, $response_body);

		return rest_ensure_response(new WP_REST_Response( null,
			302,
			array(
				'Location' => $success_url // or set any other URL you wish to redirect to
			)
		));
	}

	private function _update_wordpress($tx_ref, $response): void {
		$args   =  array(
			'post_type'   => 'payment_list',
			'post_status' => 'publish',
			'post_title'  => $tx_ref,
		);

		$payment_record_id = wp_insert_post( $args, true );

		if ( ! is_wp_error( $payment_record_id )) {
			$data = $response['data'];
			$post_meta = array(
				'_flw_rave_payment_amount'   => $data['amount'],
				'_flw_rave_payment_fullname' => $data['customer']['name'],
				'_flw_rave_payment_customer' => $data['customer']['email'],
				'_flw_rave_payment_status'   => $data['status'],
				'_flw_rave_payment_tx_ref'   => $tx_ref,
			);
			$this->_add_post_meta( $payment_record_id, $post_meta );
		}
	}

	private function _add_post_meta( $post_id, $data ): void {

		foreach ($data as $meta_key => $meta_value) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

	}
}