<?php

defined( 'ABSPATH' ) || exit;

$form_id = Flutterwave_Payments::gen_rand_string();

if (!empty($atts['custom_currency'])) {
  if (preg_match('/^[a-z\d]* [a-z\d]*$/', $atts['custom_currency'])) {
    $currencies = explode(", ", $atts['custom_currency']);
  } else{
    $currencies = explode(",", $atts['custom_currency']);
  }
}

?>

<div class="flutterwave-payment-form">
  <span class="flw-error"></span>
  <form id="<?php echo $form_id ?>" class="flw-simple-pay-now-form" <?php echo $data_attr; ?> >
    <div id="notice"></div>
    <?php if ( empty( $atts['email'] ) ) : ?>

      <label class="pay-now"><?php _e( 'Email', 'flutterwave-payments' ) ?></label>
      <input class="flw-form-input-text" id="flw-customer-email" type="email" placeholder="<?php _e( 'Email', 'flutterwave-payments' ) ?>" required /><br>

    <?php endif; ?>

    <?php if ( empty( $atts['firstname'] ) ) : ?>

      <label class="pay-now"><?php _e( 'First Name', 'flutterwave-payments' ) ?> </label>
      <input class="flw-form-input-text" id="flw-first-name" type="text" placeholder="<?php _e( 'First Name', 'flutterwave-payments' ) ?>" /><br>

    <?php endif; ?>

    <?php if ( empty( $atts['lastname'] ) ) : ?>

      <label class="pay-now"><?php _e( 'Last Name', 'flutterwave-payments' ) ?></label>
      <input class="flw-form-input-text" id="flw-last-name" type="text" placeholder="<?php _e( 'Last Name', 'flutterwave-payments' ) ?>" /><br>

    <?php endif; ?>

    <?php if ( empty( $atts['amount'] ) ) : ?>

      <label class="pay-now"><?php _e( 'Amount', 'flutterwave-payments' ); ?></label>
      <input class="flw-form-input-text" id="flw-amount" type="text" placeholder="<?php _e( 'Amount', 'flutterwave-payments' ); ?>" required /><br>

    <?php endif; ?>

    <?php if ( !empty( $atts['recurring_payment'] ) ) : ?>

      <label class="pay-now"><?php _e( 'Recurring Payment', 'flutterwave-payments' ) ?></label>
      <select class="flw-form-select" id="flw-payment-plan" required>
        <option value="">-- Select Interval --</option>
        <?php 
          foreach($atts['paymentplans'] as $key => $value){
            if ($atts['paymentplansenable'][$key] == 'yes')
              echo '<option value="'.$key.'">'.$value.'</option>';
          }
        ?>
      </select><br>

    <?php endif; ?>

    <?php if (empty($atts['currency'])) : ?>
      <label class="pay-now"><?php _e('Currency', 'flutterwave-payments'); ?></label>
      <?php if (!empty($atts['custom_currency'])){ ?>

      <select class="flw-form-select" id="flw-currency" required>
        <?php foreach ($currencies as $currency): ?>
          <option value="<?php echo $currency ?>"><?php echo $currency ?></option>
        <?php endforeach; ?>
      </select>

      <?php } else{ ?>


      <?php if ($atts['country'] == "NG") : ?>
        <select class="flw-form-select" id="flw-currency" required>
          <option value="NGN">NGN</option>
          <option value="USD">USD</option>
          <option value="KES">KES</option>
          <option value="EUR">EUR</option>
          <option value="GBP">GBP</option>
        </select>
      <?php endif; ?>

      <?php if ($atts['country'] == "KE") : ?>
        <select class="flw-form-select" id="flw-currency" required>
          <option value="KES">KES</option>
        </select>
      <?php endif; ?>

      <?php if ($atts['country'] == "GH") : ?>
        <select class="flw-form-select" id="flw-currency" required>
          <option value="GHS">GHS</option>
          <option value="USD">USD</option>
        </select>
      <?php endif; ?>

      <?php if ($atts['country'] == "ZA") : ?>
        <select class="flw-form-select" id="flw-currency" required>
          <option value="ZAR">ZAR</option>
        </select>
      <?php endif; ?>

      <?php if ($atts['country'] == "US") : ?>
        <select class="flw-form-select" id="flw-currency" required>
          <option value="NGN">NGN</option>
          <option value="USD">USD</option>
          <option value="KES">KES</option>
          <option value="GHS">GHS</option>
          <option value="EUR">EUR</option>
          <option value="ZAR">ZAR</option>
          <option value="GBP">GBP</option>
        </select>
      <?php endif; ?>

        <?php 
      } ?>

    <?php endif; ?>
    <br>

    <?php wp_nonce_field( 'flw-rave-pay-nonce', 'flw_sec_code' ); ?>
    <button value="submit" id="flw-pay-now-button" class='flw-pay-now-button' href='#'><?php _e( $btn_text, 'flutterwave-payments' ) ?></button>
  </form>
</div>
