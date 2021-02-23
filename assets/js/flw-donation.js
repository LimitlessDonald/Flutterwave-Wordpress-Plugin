var pp;

jQuery( function( $ ) {


    var donationOnce = $('#once');
    var donationMonthly = $('#monthly');
    var donation_form = $( '.flw-donation-pay-now-form' );

    
    var donationPayBtn = $('#flw-donation-pay-button');

    donationOnce.on('click', () =>{
        donationPayBtn.text('Donate NGN 1,000 once');
        jQuery('#flw-payment-plan').val('');
    });

    donationMonthly.on('click', () =>{
        donationPayBtn.text('Donate NGN 1,000 monthly');
        jQuery('#flw-payment-plan').val(pp);
    });

    donationOnce.trigger('click');


    donationPayBtn.on('click', (evt) => {
        evt.preventDefault();

            donation_form.submit();
  
            
    });

    if ( donation_form ) {
            
        donation_form.on( 'submit', function(evt) {
              evt.preventDefault();
              var d_config = buildConfigObj( this );
              processCheckout( d_config );
        } );
    
      }

});


