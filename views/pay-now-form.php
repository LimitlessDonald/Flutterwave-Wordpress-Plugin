<?php
/**
 * Flutterwave Payments Form Page
 *
 * @package Flutterwave\Payments\Views
 * @version 1.0.6
 */

defined( 'ABSPATH' ) || exit;

$form_id = Flutterwave_Payments::gen_rand_string();

if ( ! empty( $atts['custom_currency'] ) ) {
	if ( preg_match( '/^[a-z\d]* [a-z\d]*$/', $atts['custom_currency'] ) ) {
		$currencies = explode( ', ', $atts['custom_currency'] );
	} else {
		$currencies = explode( ',', $atts['custom_currency'] );
	}
}

?>

<div class="flutterwave-payment-form">
	<span class="flw-error"></span>
	<form id="<?php echo esc_attr( $form_id ); ?>" class="flw-simple-pay-now-form" <?php echo esc_attr( $data_attr ); ?>>
		<div id="notice"></div>
		<?php echo wp_kses( $input_fields_html, $allowed_html_elements ); ?>
		<?php wp_nonce_field( 'flw-rave-pay-nonce', 'flw_sec_code' ); ?>
		<button value="submit" id="flw-pay-now-button" class='flw-pay-now-button'>
			<?php
			echo esc_attr( $btn_text );
			?>
		</button>
	</form>
</div>
<div id="flutterwave-overlay" style="display:none">
	<div id="flw-overlay-text">You would be redirected to the payment page soon. please do not close this page.</div>
</div>
