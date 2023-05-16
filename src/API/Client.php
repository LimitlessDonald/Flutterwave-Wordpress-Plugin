<?php

namespace Flutterwave\WordPress\API;

use Flutterwave\WordPress\Exception\ApiException;

final class Client {

	const BASE_URL                   = 'https://api.flutterwave.com/';
	const VERSION                    = 'v3';
	private static ?Client $instance = null;
	private string $secret_key;
	private int $timeout;
	private array $headers;


	/**
	 * Client Header controller.
	 */
	private function __construct( string $secret_key ) {
		$this->secret_key = $secret_key;
		$this->timeout    = 60;
		$this->headers    = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->secret_key,
		);
	}

	public static function get_instance( string $secret_key ): Client {

		if ( is_null( self::$instance ) ) {
			return new self( $secret_key );
		}

		return self::$instance;
	}

	private function get_base_url(): string {
		return self::BASE_URL . self::VERSION;
	}

	/**
	 * This is the main request method for the Flutterwave WordPress client
	 */
	public function request( string $url, string $method = 'GET', array $data = array() ) {
		$_request_url       = $this->get_base_url() . $url; // url should be prefixed with a "/" .
		$wp_args['method']  = $method;
		$wp_args['timeout'] = $this->timeout;
		$wp_args['body']    = \wp_json_encode( $data, JSON_UNESCAPED_SLASHES );
		$wp_args['headers'] = $this->headers;
		if ( empty( $data ) || $method === 'GET' ) {
			unset( $wp_args['body'] );
		}

		$response = \wp_safe_remote_request( $_request_url, $wp_args );

		try {
			Handler::handle_api_errors( $response );
		} catch ( ApiException $e ) {
			return $e->getError();
		}

		return $response;
	}
}
