var pp;

jQuery(function ($) {
	/**
	 * Builds config object to be sent to GetPaid
	 *
	 * @return object - The config object
	 */
	const buildConfigObj = function (form) {
		let formData = $(form).data();
		let amount = formData.amount?.replace(/"|'/g, '') || $(form).find('#flw-amount').val();
		let email = formData.email?.replace(/"|'/g, '') || $(form).find('#flw-customer-email').val();
		let firstname =
			formData.firstname?.replace(/"|'/g, '') || $(form).find('#flw-first-name').val();
		let lastname =
			formData.lastname?.replace(/"|'/g, '') || $(form).find('#flw-last-name').val();
		let formCurrency =
			formData.currency?.replace(/"|'/g, '') || $(form).find('#flw-currency').val();
		let formId = form.attr('id');
		let txref = 'WP_' + formId.toUpperCase() + '_' + new Date().valueOf();
		let setCountry; //set country

		//switch the country with form currency provided
		setCountry = flw_pay_options.countries[formCurrency]
			? flw_pay_options.countries[formCurrency]
			: flw_pay_options.countries['NGN'];

		let redirect_url = window.location.origin;

		return {
			amount: amount,
			country: setCountry, //flw_pay_options.country,
			currency: formCurrency ?? flw_pay_options.currency,
			customer: {
				email,
				phone_number: null,
				name: firstname + ' ' + lastname,
			},
			payment_options: flw_pay_options.method,
			public_key: flw_pay_options.public_key,
			tx_ref: txref,
			customizations: {
				title: flw_pay_options.title,
				description: flw_pay_options.desc,
				logo: flw_pay_options.logo,
			},
			form_id: formId,
		};
	};

	const processCheckout = function (opts, form) {
		let args = {
			action: 'get_payment_url',
			flw_sec_code: $(form).find('#flw_sec_code').val(),
			payment_type: $(form).find('#flw-payment-type').val(),
		};

		let dataObj = Object.assign({}, args, opts);
		$.post(flw_pay_options.cb_url, dataObj).success(function (data) {
			let response = data;

			if (response.status === 'error') {
				$('.flw-error')
					.html(response.message)
					.attr('style', 'color:red');
			} else {
				let flw_overlay = $('#flutterwave-overlay');
				flw_overlay.addClass('flutterwave-overlay');
				$('#flw-overlay-text').addClass('flw-overlay-text');
				flw_overlay.show();
				redirectTo(response.url);
			}
		});
	};

	/**
	 * Redirect to set url
	 *
	 * @param string url - The link to redirect to
	 *
	 * @return void
	 */
	const redirectTo = function (url) {
		if (url) {
			location.href = url;
		}
	};

	// for each form process payments
	$('.flw-donation-form').each(function () {
		let form = $(this);

		form.find('#flw-payment-type').on('change', function () {
			let option = $(this).val();
			let btn = jQuery('.flw-donation-form').find('#flw-pay-now-button');
			let fullText = 'DONATE ' + option.toUpperCase();
			btn.text(null);
			console.log(btn.text());
			btn.text(fullText);
		});

		form.on('submit', function (event) {
			event.preventDefault(); // Prevent the default form submission
			let btn = form.find('button');
			btn.prop('disabled', true);

			let inputs = form.find('input[type="text"]');
			let isValid = true;

			inputs.each(function () {
				let inputValue = $(this).val();
				if (
					typeof inputValue === 'string' &&
					inputValue.trim() === ''
				) {
					isValid = false;
					$(this).attr('style', 'border-color: red');
				} else {
					$(this).attr('style', 'border-color: green');
				}
			});

			if (isValid) {
				let config = buildConfigObj(form);
				console.log(config);
				processCheckout(config, form);
			} else {
				//unblur button.
				btn.effect('shake', { times: 2 }, 300);
				btn.prop('disabled', false);
			}
		});
	});
});
