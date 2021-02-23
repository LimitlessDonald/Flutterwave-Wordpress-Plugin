
    const planName = jQuery('#plan-name');
    const planAmount = jQuery('#plan-amount');
    const planInterval = jQuery('#plan-interval');
    const planDuration = jQuery('#plan-duration');
    const seckey = jQuery('#seckey');
    
    

    jQuery(document).ready(function(){

        jQuery('#wpfooter').hide();
          

        jQuery('#createPlan-btn').on('click', (event) => {
                event.preventDefault();
      
                jQuery.post("https://api.ravepay.co/v2/gpx/paymentplans/create",
                {
                      seckey: seckey.val(),
                      amount : planAmount.val(),
                      name : planName.val(),
                      interval : planInterval.val(),
                      duration : parseInt(planDuration.val()),
                })
                .done(function () {
                    location.reload();
 
                })

        });
        










    });






     