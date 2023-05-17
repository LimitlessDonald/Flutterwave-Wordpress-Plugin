<?php
/**
 * Flutterwave Transaction Route.
 *
 * @package Flutterwave Payment
 */

/**
 * FLW Transaction Rest Route.
 */
class FLW_Transaction_Rest_Route extends WP_REST_Controller {
	const PENDING = 'processing';
	const FAILED  = 'failed';
	const SUCCESS = 'successful';
	/**
	 * Payment base_url.
	 *
	 * @var string
	 */
	protected $flw_base_url = 'https://api.flutterwave.com/v3/';

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'flutterwave/v1';

	/**
	 * Endpoint path.
	 *
	 * @var string
	 */
	protected $rest_base = 'transactions';


	/**
	 * Constructure for Transaction Route class.
	 */
	public function __construct() {
		$this->f4b_options = get_option( 'flw_rave_options' );
		add_action( 'rest_api_init', array( $this, 'create_rest_routes' ) );
	}

	/**
	 * Create Rest Route.
	 *
	 * @return void
	 */
	public function create_rest_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(

				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_transactions' ),
				'permission_callback' => array( $this, 'get_transactions_permission' ),

			)
		);

		register_rest_route(
			$this->namespace,
			'/verify-transaction',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'verifyPayment' ),
				'permission_callback' => array( $this, 'free_pass' ),

			)
		);

		register_rest_route(
			$this->namespace,
			'/update-transaction',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'update_transaction' ),
				'permission_callback' => array( $this, 'free_pass' ),
			)
		);
	}


	/**
	 * Trigger a transaction Update.
	 *
	 * @param WP_REST_Request $request The request from flutterwave.
	 */
	public function update_transaction( WP_REST_Request $request ) {
		$token = $this->f4b_options['secret_key'];

		if ( ! $request->has_param( 'tx_ref' ) || ! $request->has_param( 'post_id' ) ) {
			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => get_site_url() . '/wp-admin/admin.php?page=flutterwave-payments-transactions',
					)
				)
			);
		}

		$txref   = $request->get_param( 'tx_ref' );
		$post_id = $request->get_param( 'post_id' );
		$status  = get_post_meta( $post_id, '_flw_rave_payment_status', true );

		if ( 'successful' === $status ) {
			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => get_site_url() . '/wp-admin/admin.php?page=flutterwave-payments-transactions&status=nope',
					)
				)
			);
		}

		$url = 'https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref=' . $txref;

		$response = wp_safe_remote_get(
			$url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_wp_error( $response ) ) {

			if ( isset( $response_body['status'] ) && 'error' === $response_body['status'] ) {
				return rest_ensure_response(
					new WP_REST_Response(
						null,
						302,
						array(
							'Location' => get_site_url() . '/wp-admin/admin.php?page=flutterwave-payments-transactions&transaction_status=unverifed',
						)
					)
				);
			}
		} else {

			if ( isset( $response_body['status'] ) && 'error' === $response_body['status'] ) {
				return rest_ensure_response(
					new WP_REST_Response(
						null,
						302,
						array(
							'Location' => get_site_url() . '/wp-admin/admin.php?page=flutterwave-payments-transactions&transaction_status=unverifed',
						)
					)
				);
			}

			$this->update_wordpress( $txref, $response_body );
		}

		return rest_ensure_response(
			new WP_REST_Response(
				null,
				302,
				array(
					'Location' => get_site_url() . '/wp-admin/admin.php?page=flutterwave-payments-transactions&transaction_status=successful',
				)
			)
		);
	}

	/**
	 * Retrieve settings.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_transactions( WP_REST_Request $request ): WP_REST_Response {

		$page = $request->get_param( 'page' );

		$token = $this->f4b_options['secret_key'];

		$response = wp_remote_get(
			$this->flw_base_url . "transactions/?page=$page",
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		return new WP_REST_Response( json_decode( $response['body'] ) );

	}

	/**
	 * Route Permission.
	 *
	 * @return bool
	 */
	public function get_transactions_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Open to all.
	 *
	 * @return bool permission callback.
	 */
	public function free_pass() {
		return true;
	}

	/**
	 * Open to all.
	 *
	 * @param WP_REST_Request $request The request to verify transactions.
	 */
	public function verifyPayment( WP_REST_Request $request ) {
		$token          = $this->f4b_options['secret_key'];
		$success_url    = $this->f4b_options['success_redirect_url'];
		$failer_url     = $this->f4b_options['failed_redirect_url'];
		$pending_url    = $this->f4b_options['pending_redirect_url'];
		$txref          = $request->get_param( 'tx_ref' ) ?? null;
		$transaction_id = $request->get_param( 'transaction_id' ) ?? null;
		$status         = $request->get_param( 'status' ) ?? null;

		if ( 'cancelled' === $status ) {
			$this->update_wordpress(
				$txref,
				array(
					'data' => array(
						'amount'   => 0.00,
						'customer' => array(
							'name'  => '-',
							'email' => '-',
						),
						'status'   => 'cancelled',
					),
				)
			);

			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => home_url(),
					)
				)
			);
		}

		if ( is_null( $txref ) || is_null( $transaction_id ) ) {
			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => home_url(),
					)
				)
			);
		}

		sleep( 2 );

		$url = 'https://api.flutterwave.com/v3/transactions/' . $transaction_id . '/verify';

		$response = wp_safe_remote_get(
			$url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->update_wordpress(
				$txref,
				array(
					'data' => array(
						'amount'   => 0.00,
						'customer' => array(
							'name'  => '-',
							'email' => '-',
						),
						'status'   => self::PENDING,
					),
				)
			);
			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => $pending_url . '?status=' . self::PENDING,
					)
				)
			);
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 'successful' !== $response_body['data']['status'] ) {
			$this->update_wordpress( $txref, $response_body );
			return rest_ensure_response(
				new WP_REST_Response(
					null,
					302,
					array(
						'Location' => $failer_url . '?status=' . self::PENDING,
					)
				)
			);
		}

		$payment_record_id = $response_body['data']['meta']['order_id'];

		if ( 'successful' !== get_post_meta( $payment_record_id )['_flw_rave_payment_status'] ) {
			$this->update_wordpress( $txref, $response_body );
		}

		return rest_ensure_response(
			new WP_REST_Response(
				null,
				302,
				array(
					'Location' => $success_url,
				)
			)
		);
	}

	/**
	 * Update WordPress.
	 *
	 * @param string $tx_ref The request tx_ref.
	 * @param array  $response  data from flutterwave.
	 *
	 * @return void
	 */
	private function update_wordpress( string $tx_ref, array $response ): void {
		$pending_amount    = (float) $response['data']['meta']['order_amount'];
		$pending_currency  = $response['data']['meta']['order_currency'];
		$recieved_amount   = (float) $response['data']['amount'];
		$recieved_currency = $response['data']['currency'];

		$payment_record_id = $response['data']['meta']['order_id'];

		if ( $this->has_order_property_matched( $response ) ) {
			if ( ! is_wp_error( $payment_record_id ) ) {
				$data      = $response['data'];
				$post_meta = array(
					'_flw_rave_payment_id'     => $data['id'],
					'_flw_rave_payment_status' => $data['status'],
				);
				$this->add_post_meta( $payment_record_id, $post_meta );
			}
		} else {
			if ( ! is_wp_error( $payment_record_id ) ) {

				if ( $recieved_amount < $pending_amount && $pending_currency === $recieved_currency ) {
					$post_meta = array(
						'_flw_rave_payment_status' => 'paid less - remains' . ( $pending_amount - $recieved_amount ),
					);
				}

				if ( $recieved_currency !== $pending_currency && $recieved_amount === $pending_amount ) {
					$post_meta = array(
						'_flw_rave_payment_status' => 'currency diff' . ( $pending_amount - $recieved_amount ),
					);
				}

				if ( $recieved_amount > $pending_amount && $pending_currency === $recieved_currency ) {
					$post_meta = array(
						'_flw_rave_payment_status' => 'paid more - refund' . ( $pending_amount - $recieved_amount ),
					);
				}

				$this->add_post_meta( $payment_record_id, $post_meta );
			}
		}

	}

	/**
	 * Update WordPress.
	 *
	 * @param [int]   $post_id post identifier.
	 * @param [array] $data data to add.
	 *
	 * @return void
	 */
	private function add_post_meta( $post_id, $data ): void {

		foreach ( $data as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

	}

	/**
	 * Check order mismatch.
	 *
	 * @param array $response  data from flutterwave.
	 *
	 * @return bool
	 */
	private function has_order_property_matched( $response ) {
		// check the amount against amount, currency paid.
		$pending_amount    = (float) $response['data']['meta']['order_amount'];
		$pending_currency  = $response['data']['meta']['order_currency'];
		$recieved_amount   = (float) $response['data']['amount'];
		$recieved_currency = $response['data']['currency'];

		return $pending_amount === $recieved_amount && $pending_currency === $recieved_currency;

	}
}
