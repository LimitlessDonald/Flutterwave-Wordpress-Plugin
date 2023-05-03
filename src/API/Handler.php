<?php

namespace Flutterwave\WordPress\API;

use Flutterwave\WordPress\Exception\ApiException;
use Flutterwave\WordPress\Exception\InvalidRequestException;

final class Handler {

    public static function handle_api_errors($response) {
        $response_status_code = \wp_remote_retrieve_response_code($response);

        $error_hash_table = self::get_error_hash_table();

        if( isset( $error_hash_table[$response_status_code] ) && $error_hash_table[$response_status_code] !== 400 ) {
            throw new ApiException($error_hash_table[$response_status_code]);
        }

        if( isset( $error_hash_table[$response_status_code] ) && $error_hash_table[$response_status_code] === 400 ) {
            // TODO: Look at the response body and see if it matches 
        }
    }


    public static function get_error_hash_table() {
        return array (
            500 => new \WP_Error( 'flw-unavailable', __("This Services are Currently Unavailable.  Please contact support." , "flutterwave-payments" ) ),
            401 => new \WP_Error( 'flw-unauthorized', __("You do not have the right permission to this service. please ensure your secret_key has been supplied.", "flutterwave-payments"))
        );
    }
}