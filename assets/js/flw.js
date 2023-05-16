jQuery(function ($) {
	/**
	 * Builds config object to be sent to GetPaid
	 *
	 * @return object - The config object
	 */
	const buildConfigObj = function (form) {
		let formData = $(form).data();

		// Form Appearance settings.
		let should_split_name = formData.split_name?.replace(/"|'/g, '') == 1;

		let fullname = '';
		if (!should_split_name) {
			fullname = $(form).find('#flw-full-name').val();
		} else {
			let firstname =
				formData.firstname?.replace(/"|'/g, '') ||
				$(form).find('#flw-first-name').val();
			let lastname =
				formData.lastname?.replace(/"|'/g, '') ||
				$(form).find('#flw-last-name').val();
			fullname = firstname + ' ' + lastname;
		}

		let phone =
			formData.phone?.replace(/"|'/g, '') ||
			$(form).find('#flw-phone').val();

		let amount =
			formData.amount?.replace(/"|'/g, '') ||
			$(form).find('#flw-amount').val();
		let email =
			formData.email?.replace(/"|'/g, '') ||
			$(form).find('#flw-customer-email').val();
		let special_currency_value =
			formData.custom_currency?.replace(/"|'/g, '') ||
			$(form).find('#flw-currency').val();
		let formCurrency =
			formData.custom_currency?.replace(/"|'/g, '').length > 3
				? $(form).find('#flw-currency').val()
				: special_currency_value;

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
				phone_number: phone ?? null,
				name: fullname,
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
		};

		let dataObj = Object.assign({}, args, opts);

		// console.log(dataObj);

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
	 * Sends payment response from GetPaid to the process payment endpoint
	 *
	 * @param object Response object from GetPaid
	 *
	 * @return void
	 */
	const sendPaymentRequestResponse = function (res, form) {
		let args = {
			action: 'process_payment',
			flw_sec_code: $(form).find('#flw_sec_code').val(),
		};

		let dataObj = Object.assign({}, args, res.tx);

		$.post(flw_pay_options.cb_url, dataObj).success(function (data) {
			var response = JSON.parse(data);
			redirectUrl = response.redirect_url;

			if (redirectUrl === '') {
				var responseMsg =
					res.tx.paymentType === 'account'
						? res.tx.acctvalrespmsg
						: res.tx.vbvrespmessage;
				$(form)
					.find('#notice')
					.text(responseMsg)
					.removeClass(function () {
						return $(form).find('#notice').attr('class');
					})
					.addClass(response.status);
			} else {
				setTimeout(redirectTo, 5000, redirectUrl);
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
	$('.flw-simple-pay-now-form').each(function () {
		let form = $(this);

		form.on('submit', function (event) {
			event.preventDefault(); // Prevent the default form submission
			let btn = form.find('button');
			//gray the button.
			btn.prop('disabled', true);

			let inputs = form.find('input[type="*"]');

			let isValid = true;

			inputs.each(function () {
				let inputValue = $(this).val();
				if (
					typeof inputValue === 'string' &&
					inputValue.trim() === ''
				) {
					isValid = false;
					$(this).attr('style', 'border-color: red');
				}

				if (
					$(this).attr('type') === 'number' &&
					parseInt(inputValue) === NaN
				) {
					isValid = false;
					$(this).attr('style', 'border-color: red');
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
