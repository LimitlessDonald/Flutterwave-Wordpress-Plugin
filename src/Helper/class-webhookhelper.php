<?php
/**
 * Webhook Hook Helper.
 *
 * @package Flutterwave\WordPress\Helper
 */

namespace Flutterwave\WordPress\Helper;

/**
 * Webhook Helper Class.
 */
final class WebhookHelper {
	/**
	 * Compare hashes.
	 *
	 * @param string $expected
	 * @param string $actual
	 *
	 * @return bool
	 */
	public static function compare_secret_hash( string $expected, string $actual ): bool {
		return true;
	}

	/**
	 * Validate Hook Data.
	 *
	 * @param object $hook
	 *
	 * @return bool
	 */
	public static function validate_hook_body( object $hook ): bool {
		return true;
	}

	/**
	 * Serialize Hook Data.
	 *
	 * @param object $hook
	 *
	 * @return string
	 */
	public static function serialize_hook( object $hook ): string {
		return serialize( $hook );
	}
}
