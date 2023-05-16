<?php
/*
 * Abstract Flutterwave Service.
 *
 * @package Flutterwave_Payments
 * @version 1.0.6
 */

namespace Flutterwave\WordPress\Integration;

abstract class AbstractService {

    protected string $base_url;

    protected string $name;

    protected string $api_key;

    protected string $owner;

    public array $error_log = [];

    const PUBLIC_KEY = 'public';

    const SECRET_KEY = 'secret';

    abstract public function _init( string $key ): void ;
    
    abstract public function get_features(): array ;

    abstract public function get_assets(): array ;

    abstract public function get_info() : array ;

    abstract protected function get_headers() : array ;

    public function __construct( string $key ) {
        $this->_init( $key );
    }

    public function set_key( string $key ):void {
        $this->api_key = $key;
    }

    public function get_key () {
        return $this->api_key;
    }

    public function get_name() {
        return $this->owner ." ".$this->name;
    }

    protected function request( string $url, string $method = 'GET', array $data = array() ): object {
        $url = $this->base_url . $url;
		$wp_args['method']  = $method;
		$wp_args['timeout'] = 60;
		$wp_args['body']    = \wp_json_encode( $data, JSON_UNESCAPED_SLASHES );
		$wp_args['headers'] = $this->get_headers();
		if ( empty( $data ) || $method === 'GET' ) {
			unset( $wp_args['body'] );
		}

		$response = \wp_safe_remote_request( $url, $wp_args );

        if ( !is_wp_error( $response ) ) {
            return json_decode( wp_remote_retrieve_body( $response ), true );
        } 
        
        return \WP_Error( 
            'flw-unavailable',
            /* translators: %s: owner's name, %s: service name */
            __( sprintf( '%s', $this->owner ) .'\'s ' . sprintf( '%s', $this->name ) . ' service is currently unavailable. please use another integration.', 'flutterwave-payments' ) 
        );
    }

}