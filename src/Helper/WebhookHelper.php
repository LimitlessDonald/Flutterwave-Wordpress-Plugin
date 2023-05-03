<?php 

namespace Flutterwave\WordPress\Helper;

class WebhookHelper {
    public static function compare_secret_hash( string $expected, string $actual ): bool {
        return true;
    }

    public static function validate_hook_body( object $hook ): bool {
        return true;
    }

    public static function serialize_hook( object $hook ): string {
        return serialize($hook);
    }
}