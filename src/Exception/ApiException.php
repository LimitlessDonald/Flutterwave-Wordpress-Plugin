<?php

namespace Flutterwave\WordPress\Exception;

final class ApiException extends \Exception {

	protected \WP_Error $error;

	public function __construct( \WP_Error $error ) {
		$this->error = $error;
	}
	public function getError() {
		return $this->error;
	}
}
