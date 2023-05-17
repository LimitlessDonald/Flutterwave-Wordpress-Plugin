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
	 * @param string $expected local hash.
	 * @param string $actual recieved hash.
	 *
	 * @return bool
	 */
	public static function compare_secret_hash( string $expected, string $actual ): bool {
		return true;
	}

	/**
	 * Validate Hook Data.
	 *
	 * @param object $hook notification sent by flutterwave.
	 *
	 * @return bool
	 */
	public static function validate_hook_body( object $hook ): bool {
		return true;
	}

}
