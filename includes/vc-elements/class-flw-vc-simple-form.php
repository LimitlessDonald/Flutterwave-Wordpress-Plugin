<?php
/**
 * Visual Composer element for a simple PAY NOW form
 *
 * @package Flutterwave_Payments
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple PAY NOW form Class
 */
class FLW_VC_Simple_Form {


	/**
	 * Class Constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'flw_simple_form_mapping' ) );
	}

	/**
	 * Visual Composer Form elements mapping
	 *
	 * @return void
	 */
	public function flw_simple_form_mapping() {
		// Stop all if VC is not enabled
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		// Map the block with vc_map()
		vc_map(
			array(
				'name'        => __( 'Flutterwave Simple Form', 'flutterwave-payments' ),
				'base'        => 'flw-pay-button',
				'description' => __( 'Flutterwave Simple Pay Now Form', 'flutterwave-payments' ),
				'category'    => __( 'Flutterwave Forms', 'flutterwave-payments' ),
				'icon'        => FLW_DIR_URL . 'assets/images/rave-icon.png',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'class'       => 'title-class',
						'holder'      => 'p',
						'heading'     => __( 'Amount', 'flutterwave-payments' ),
						'param_name'  => 'amount',
						'value' => __('', 'flutterwave-payments'), //phpcs:ignore.
						'description' => __( 'If left blank, user will be asked to enter the amount to complete the payment.', 'flutterwave-payments' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => 'Form Attributes',
					),

					array(
						'type'        => 'checkbox',
						'heading'     => __( "Use logged-in user's email?", 'flutterwave-payments' ),
						'description' => __( "Check this if you want the logged-in user's email to be used. If unchecked or user is not logged in, they will be asked to fill in their email address to complete payment.", 'flutterwave-payments' ),
						'param_name'  => 'use_current_user_email',
						'std'         => '',
						'value'       => array(
							__( 'Yes', 'flutterwave-payments' ) => 'yes',
						),
						'group'       => 'Form Attributes',
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Button Text', 'flutterwave-payments' ),
						'param_name'  => 'content',
						'value' => __('', 'flutterwave-payments'), //phpcs:ignore.
						'description' => __( '(Optional) The text on the PAY NOW button. Default: "PAY NOW"', 'flutterwave-payments' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => 'Form Attributes',
					),

				),
			)
		);
	}
}

// Element Class Init
new FLW_VC_Simple_Form();
