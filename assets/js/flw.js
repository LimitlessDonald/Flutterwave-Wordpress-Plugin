jQuery( function ( $ ) {
  /**
   * Builds config object to be sent to GetPaid
   *
   * @return object - The config object
   */
  const buildConfigObj = function( form ) {
      let formData = $( form ).data();
      let amount = formData.amount || $(form).find('#flw-amount').val();
      let email = formData.email || $(form).find('#flw-customer-email').val();
      let firstname = formData.firstname || $(form).find('#flw-first-name').val();
      let lastname = formData.lastname || $(form).find('#flw-last-name').val();
      let formCurrency = formData.currency || $(form).find('#flw-currency').val();
      let paymentplanID = $(form).find('#flw-payment-plan').val() ?? null;
      let txref   = 'WP_' + form.attr('id').toUpperCase() + '_' + new Date().valueOf();
      let setCountry; //set country


      if (formCurrency === '') {
        formCurrency = flw_pay_options.currency;
      }

      //switch the country with form currency provided
      setCountry = flw_pay_options.countries[formCurrency] ? flw_pay_options.countries[formCurrency]: flw_pay_options.countries['NGN'];

      let redirect_url = window.location.origin;

      return {
        amount: amount,
        country: setCountry, //flw_pay_options.country,
        currency: formCurrency,
        meta: {
          consumer_id: Math.random() + 10,
          ip_address : "127.0.0.1",
        },
        customer: {
          email,
          phone_number: null,
          name: firstname + ' ' + lastname,
        },
        payment_options: flw_pay_options.method,
        public_key: flw_pay_options.public_key,
        tx_ref: txref,
        onclose: function() {
          // TODO: handle event when modal is closed before payment is completed.
          redirectTo( redirect_url );
        },
        callback: function(res) {
          sendPaymentRequestResponse( res, form );
        },
        customizations: {
          title: flw_pay_options.title,
          description: flw_pay_options.desc,
          logo: flw_pay_options.logo,
        },
      };
  };

  const processCheckout = function(opts) {
    FlutterwaveCheckout( opts );
  };

  /**
   * Sends payment response from GetPaid to the process payment endpoint
   *
   * @param object Response object from GetPaid
   *
   * @return void
   */
  const sendPaymentRequestResponse = function( res, form ) {
    var args  = {
      action: 'process_payment',
      flw_sec_code: $( form ).find( '#flw_sec_code' ).val(),
    };

    var dataObj = Object.assign( {}, args, res.tx );

    $
      .post( flw_pay_options.cb_url, dataObj )
      .success( function(data) {
        var response  = JSON.parse( data );
        redirectUrl   = response.redirect_url;

        if ( redirectUrl === '' ) {

          var responseMsg  = ( res.tx.paymentType === 'account' ) ? res.tx.acctvalrespmsg  : res.tx.vbvrespmessage;
          $( form )
            .find( '#notice' )
            .text( responseMsg )
            .removeClass( function() {
              return $( form ).find( '#notice' ).attr( 'class' );
            } )
            .addClass( response.status );

        } else {

          setTimeout( redirectTo, 5000, redirectUrl );

        }

      } );
  };

  /**
   * Redirect to set url
   *
   * @param string url - The link to redirect to
   *
   * @return void
   */
  const redirectTo = function( url ) {

    if ( url ) {
      location.href = url;
    }

  };

  // for each form process payments
  $( '.flw-simple-pay-now-form' ).each( function() {

    let form = $( this );



    form.on('submit', function (event) {
      event.preventDefault(); // Prevent the default form submission

      let inputs = form.find('input[type="text"]');
      let isValid = true;

      inputs.each(function() {
        let inputValue = $(this).val();
        if (typeof inputValue === 'string' && inputValue.trim() === '') {
          isValid = false;
          return false; // Exit the loop if an empty value is found
        }
      });

      if (!isValid) {
        // TODO: Handle each case for each field.
        $('.flw-error').html('Please fill all the form details.').attr('style', 'color:red');
      } else {
        let config = buildConfigObj( form );
        console.log(config);
        processCheckout( config );
      }
    });
  });

} );
