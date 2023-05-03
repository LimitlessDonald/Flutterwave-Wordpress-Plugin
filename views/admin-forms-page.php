<?php

  if ( ! defined( 'ABSPATH' ) ) { exit; }

?>
<?php global $payment_forms; ?>

  <div class="wrap">

    <h1>Payment Forms</h1>
    <form id="rave-pay" action="options.php" method="post" enctype="multipart/form-data">
    <?php settings_fields( 'form-settings-group' ); ?>
    <?php do_settings_sections( 'form-settings-group' ); ?>
      <table class="form-table">
        <tbody>

        
          <!-- Form type -->
          <tr valign="top">
            <th scope="row">
              <label for="extra_form_options[payment_type]"><?php _e( 'Form Type', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <select class="regular-text code" name="extra_form_options[payment_type]">
                <?php $payment_type = esc_attr( $payment_forms->get_option_value( 'payment_type' ) ); ?>
                <option value="PAYMENT" <?php selected( $payment_type, 'PAYMENT' ) ?>>REGULAR PAYMENT</option>
                <option value="DONATION" <?php selected($payment_type, 'DONATION') ?>>DONATION</option>
              </select>
              <p class="description">(Optional) default: REGULAR PAYMENT</p>
            </td>
          </tr>
          <!-- Extra TEXT Fields -->
          <tr valign="top">
            <th scope="row">
              <label for="extra_form_options[Extra Field]"><?php _e( 'Extra Text Field', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="extra_form_options[extra_field_1]" value="<?php echo esc_attr( $payment_forms->get_option_value( 'extra_field_1' ) ); ?>" />
              <p class="description">ADD A TEXT FIELD</p>
            </td>
          </tr>
          <!-- Extra Yes/No Fields -->
          <tr valign="top">
            <th scope="row">
              <label for="extra_form_options[Extra Field]"><?php _e( 'Extra Checkbox Field', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="extra_form_options[extra_field_2]" value="<?php echo esc_attr( $payment_forms->get_option_value( 'extra_field_2' ) ); ?>" />
              <p class="description">ADD A CHECKBOX </p>
            </td>
          </tr>
          <!-- Extra SELECT/DROPDOWN Fields -->
          <tr valign="top">
            <th scope="row">
              <label for="extra_form_options[Extra Field]"><?php _e( 'Extra Checkbox Field', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="extra_form_options[extra_field_3]" value="<?php echo esc_attr( $payment_forms->get_option_value( 'extra_field_3' ) ); ?>" placeholder="Dropdown name" />
              <p class="description">ADD A DROPDOWN LIST </p>
            </td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>

  </div>
