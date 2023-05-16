<?php
/*
 * Exchange Rate API Service.
 *
 * @package Flutterwave_Payments
 * @version 1.0.6
 */

namespace Flutterwave\WordPress\Integration\ApiLayer;

use Flutterwave\WordPress\Integration\AbstractService;

final class ExchangeRateService extends AbstractService {
    public function __construct( string $key = '' ) {
        parent::__construct( $key );

        $this->owner = 'apilayer';
        $this->name  = 'exchange_rate';
    }

    public function _init( string $key ): void {
        $this->set_key( $key );
    }

    protected function get_headers(): array {
        return [
            'Content-Type' => 'text/plain',
            'apikey' => $this->get_key(),
        ];
    }

    public function get_assets(): array
    {
        return [
            'logo' => 'https://assets.apilayer.com/logo/logo.png'
        ];
    }

    public function get_info(): array {
        return [
            'owner' => $this->owner,
            'name'  => $this->name,
        ];
    }

    public function get_features(): array
    {
        return [
            'exchange rate'
        ];
    }
}