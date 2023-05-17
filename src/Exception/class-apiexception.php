<?php

namespace Flutterwave\WordPress\Exception;

final class ApiException extends \Exception {

	/**
	 * Error.
	 *
	 * @var \WP_Error
	 */
	protected \WP_Error $error;

	/**
	 * ApiException Constructor.
	 *
	 * @param \WP_Error $error the error class.
	 */
	public function __construct( \WP_Error $error ) {
		parent::__construct( $error->get_error_message() );
		$this->error = $error;
	}

	/**
	 * Get WordPress Error.
	 *
	 * @return \WP_Error
	 */
	public function getError() {
		return $this->error;
	}
}
