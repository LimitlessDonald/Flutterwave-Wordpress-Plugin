<?php
/**
 * Flutterwave Transaction Route.
 *
 * @package Flutterwave Payment
 */

/**
 * FLW Webhook Rest Route.
 */
class FLW_Webhook_Rest_Route extends WP_REST_Controller {
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
	protected $rest_base = 'webhook';

	/**
	 * Constructure for Webhook Route class.
	 */
	public function __construct() {
		$this->f4b_options = get_option( 'flw_rave_options' );
		add_action( 'rest_api_init', array( $this, 'create_rest_routes' ) );
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
	 * Create Webhook route.
	 *
	 * @return void
	 */
	public function create_rest_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'handle_hook' ),
				'permission_callback' => array( $this, 'free_pass' ),
			)
		);
	}

	/**
	 * Handle Webhooks from Flutterwave.
	 *
	 * @param WP_REST_Request $request The request to verify transactions.
	 */
	public function handle_hook( WP_REST_Request $request ) {

		sleep( 7 );

		if ( ! $request->has_param( 'event' ) ) {
			return wp_json_encode(
				array(
					'status'  => 'error',
					'message' => 'Hook sent does not contain an event parameter. Please assist.',
					'agent'   => 'Flutterwave Payments - WordPress Plugin.',
				)
			);
		}

		$token           = $this->f4b_options['secret_key'];
		$local_signature = $this->f4b_options['secret_hash'];
		$event           = $request->get_param( 'event' );

		$request->set_headers( $request->get_headers( wp_unslash( $_SERVER ) ) );
		$signature = $request->get_header( 'verif_hash' );

		if ( $signature !== $local_signature ) {
			return wp_json_encode(
				array(
					'status'  => 'error',
					'message' => 'Access Denied Hash does not match',
					'agent'   => 'Flutterwave Payments - WordPress Plugin.',
				)
			);
		}

		if ( 'charge.completed' === $event ) {

			$data = $request->get_param( 'data' );

			$txref          = $data['tx_ref'];
			$transaction_id = $data['id'];
			$status         = $data['status'];
			$customer       = $data['customer'];

			if ( 'cancelled' === $status ) {
				$this->update_wordpress(
					$txref,
					array(
						'data' => array(
							'amount'   => '00',
							'customer' => array(
								'name'  => '-',
								'email' => '-',
							),
							'status'   => 'cancelled',
						),
					)
				);

				return wp_json_encode(
					array(
						'message'  => 'Hook recieved with thanks. status: cancelled',
						'site_url' => get_site_url(),
						'agent'    => 'Flutterwave Payments - WordPress Plugin.',
					)
				);
			}

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
							'amount'   => '00',
							'customer' => array(
								'name'  => $customer['name'],
								'email' => $customer['email'],
							),
							'status'   => $status,
						),
					)
				);
				return wp_json_encode(
					array(
						'message'  => 'Hook recieved with thanks. status:error',
						'site_url' => get_site_url(),
						'agent'    => 'Flutterwave Payments - WordPress Plugin.',
					)
				);
			}

			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( 'successful' !== $response_body['data']['status'] ) {

				$this->update_wordpress( $txref, $response_body );
				return wp_json_encode(
					array(
						'message'  => 'Hook recieved with thanks. status: failed',
						'site_url' => get_site_url(),
						'agent'    => 'Flutterwave Payments - WordPress Plugin.',
					)
				);
			}

			$payment_record_id = $response_body['data']['meta']['order_id'];

			if ( 'successful' !== get_post_meta( $payment_record_id )['_flw_rave_payment_status'] ) {
				$this->update_wordpress( $txref, $response_body );
			}

			return wp_json_encode(
				array(
					'message'  => 'Hook recieved with thanks. status: success',
					'site_url' => get_site_url(),
					'agent'    => 'Flutterwave Payments - WordPress Plugin.',
				)
			);
		}
	}

	/**
	 * Update WordPress.
	 *
	 * @param string $tx_ref The request tx_ref.
	 * @param array  $response  data from flutterwave.
	 *
	 * @return void
	 */
	private function update_wordpress( $tx_ref, $response ): void {
		$pending_amount    = (float) $response['data']['meta']['order_amount'];
		$pending_currency  = $response['data']['meta']['order_currency'];
		$recieved_amount   = (float) $response['data']['amount'];
		$recieved_currency = $response['data']['currency'];

		$payment_record_id = $response['data']['meta']['order_id'];

		if ( $this->has_order_property_matched( $response ) ) {
			if ( ! is_wp_error( $payment_record_id ) ) {
				$data      = $response['data'];
				$post_meta = array(
					'_flw_rave_payment_fullname' => $data['customer']['name'],
					'_flw_rave_payment_customer' => $data['customer']['email'],
					'_flw_rave_payment_status'   => $data['status'],
					'_flw_rave_payment_tx_ref'   => $tx_ref,
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
	 * @param [int]   $post_id  The post id.
	 * @param [array] $data The data array.
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
	private function has_order_property_matched( $response ): bool {
		// check the amount against amount, currency paid.
		$pending_amount    = (float) $response['data']['meta']['order_amount'];
		$pending_currency  = $response['data']['meta']['order_currency'];
		$recieved_amount   = (float) $response['data']['amount'];
		$recieved_currency = $response['data']['currency'];

		return $pending_amount === $recieved_amount && $pending_currency === $recieved_currency;
	}
}
