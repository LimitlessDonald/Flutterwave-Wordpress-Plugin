<?php

  if ( ! defined( 'ABSPATH' ) ) { exit; }

?>
<?php global $payment_plans_settings; ?>
<?php global $admin_settings; ?>

  <div class="wrap w-full">
  <div class="mt-5">
        <h1 class="m-8 font-bold text-2xl"> Payment Plans</h1>
    </div>
    
    <form id="rave-pay" action="options.php" method="post" enctype="multipart/form-data">
    <?php settings_fields( 'form-settings-group' ); ?>
    <?php do_settings_sections( 'form-settings-group' ); ?>
    


    <table class="table-auto  border-solid border-4 border-gray-600 m-5">
        
        <?php if(!$this->existing_payment_plans){
            $this->existing_payment_plans = [];
        } ?>
        <?php 
        
        if(!is_array($this->existing_payment_plans)){

          echo '<div class="bg-red-100 border border-red-400 text-red-700 mx-4 px-4 py-3 rounded relative" role="alert">
          <strong class="font-bold">Error:</strong>
          <span class="block sm:inline">'.$this->existing_payment_plans.'</span>
          <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
          </span>
        </div>';
        }else{
        
        if(count($this->existing_payment_plans) === 0){
              echo '<button class="modal-open m-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
              Create Plan
            </button>';
            echo '<tr><div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3"  role="alert">
            <p class="font-bold text-center ">Flutterwave notifcation</p>
            <p class="text-sm text-center"> You currently do not have any Payment Plans created.</p>
          </div></tr>';
 
        
        }else{  ?>

         <?php echo '<button class="modal-open m-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
  Create Plan
</button>';echo '<button class="modal-openx m-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">
Use Plan
</button>';  ?>

        <thead>
          <tr class="border-solid border-2 border-gray-300">
            <th class="w-1/2 bg-orange-200 px-4 py-2">Id</th>
            <th class="w-1/2 bg-orange-200 px-4 py-2">Name</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2">Amount</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2">Interval</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2">Duration</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2">Status</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2 ">Currency</th>
            <th class="w-1/4 bg-yellow-200 px-4 py-2 ">Created on</th>
          </tr>
        </thead>
        <tbody class="text-center">
           
        <?php for($x=0;$x<count($this->existing_payment_plans); $x++){?>
        <tr class="hover:bg-blue-300 bg-white hover:border-gray-900">
            <?php foreach ($this->existing_payment_plans[$x] as $key => $value) {
                        if($key == 'plan_token' || $key == "paymentpage"){
                            continue;
                        }else{
            ?>
                
                <td class="border px-1 py-2"><?php echo $value; ?></td>
                        
            <?php }} ?>
        </tr>

                    <?php }}}?>

          
        </tbody>
      </table>





      <!--Modal-->
  <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
      
      <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
          <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
        </svg>
        <span class="text-sm">(Esc)</span>
      </div>

      <!-- Add margin if you want to see some of the overlay behind the modal-->
      <div class="modal-content py-4 text-left px-6">
        <!--Title-->
        <div class="flex justify-between items-center pb-3">
          <p class="text-2xl font-bold">Create Plan</p>
          <div class="modal-close cursor-pointer z-50">
            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
              <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
            </svg>
          </div>
        </div>

        <!--Body-->
        <form class="w-full max-w-lg"  id="add_new_paymentplan_form">
     <input type="hidden" id="seckey" value="<?php echo $this->get_secret_key(); ?>">
       
  <div class="flex flex-wrap -mx-3 mb-6">
    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
        Name
      </label>
      <input name="plan-name" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="plan-name" type="text" placeholder="Netflix 3900 plan">
      <p class="text-red-500 text-xs italic">Please fill out this field.</p>
    </div>
    <div class="w-full md:w-1/2 px-3">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-last-name">
        Amount
      </label>
      <input name="plan-amount" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="plan-amount" type="number" placeholder="2900">
    </div>
  </div>
  
  <div class="flex flex-wrap -mx-3 mb-2">
    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-city">
        Duration
      </label>
      <input name="plan-duration" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"  type="number" placeholder="Albuquerque" id="plan-duration">
    </div>
    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
        Interval
      </label>
      <div class="relative">
        <select id="plan-interval" name="plan-interval" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="plan-interval">
          <option value="weekly" >Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="quarterly">Quarterly</option>
          <option value="annually">Annually</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
      </div>
    </div>
    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
        Currency
      </label>
      <div class="relative">
        <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="plan-currency">
          <option value="NGN">NGN</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
      </div>
    </div>
  </div>
</form>

        <!--Footer-->
        <div class="flex justify-end pt-2">
          <button class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">Action</button>
          <button id="createPlan-btn"  name="createPlan-btn" class="px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-400">Create Plan</button>
        </div>
        
      </div>
    </div>
  </div>


            <!--Modal 2-->
            <div class="modalx opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
      
      <div class="modal-close2 absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
          <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
        </svg>
        <span class="text-sm">(Esc)</span>
      </div>

      <!-- Add margin if you want to see some of the overlay behind the modal-->
      <div class="modal-content py-4 text-left px-6">
        <!--Title-->
        <div class="flex justify-between items-center pb-3">
          <p class="text-2xl font-bold">Currently in use</p>
          <div class="modal-close2 cursor-pointer z-50">
            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
              <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
            </svg>
          </div>
        </div>

        <!--Body-->
        <form class="w-full max-w-lg"  id="rave-pay" action="options.php" method="post" enctype="multipart/form-data">
        <?php settings_fields( 'paymentplan-settings-group' ); ?>
        <?php do_settings_sections( 'paymentplan-settings-group' ); ?>
        <input type="hidden" id="seckey" value="<?php echo $this->get_secret_key(); ?>">

            <!-- Payment Plan -->
                <tr valign="top">
            <th scope="row">
              <label for="new_plan_options[modal_desc]"><?php _e( 'Enable Recurring Payment', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-checkbox">
              <fieldset>
                <?php $recurring_payment = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment' ) ); ?>
                <label>
                  <input type="checkbox" name="new_plan_options[recurring_payment]" <?php checked( $recurring_payment, 'yes' ); ?> value="yes" />
                  <?php _e( 'Enable Recurring Payment (Optional)', 'rave-pay' ); ?>
                </label>
              </fieldset>
              <table> <!-- Differrent payment plans and their ID -->
                <tr> <!-- for weekly -->
                    <br />
                  <td>
                    <input type="hidden" name="new_plan_options[recurring_payment_plan_name_1]" 
                  
                    value="<?php echo $payment_plans_settings->get_option_value( "recurring_payment_plan_name_1" )?>" >
                    <select class="regular-text code" name="new_plan_options[recurring_payment_plan_1]">
                     <?php $plan_listing = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_1' ) ); ?>
                    <?php for($x=0;$x<count($this->existing_payment_plans); $x++){  ?>


                      <option <?php selected($plan_listing, $this->existing_payment_plans[$x]['id']) ?> value="<?php echo $this->existing_payment_plans[$x]['id'] ?>"><?php echo $this->existing_payment_plans[$x]['name'] ?></option>
                        
                      <?php  }?>
                    </select>
                    <p class="description"> Choose your Payment Plan 1</p>
                  </td>
                  <td>
                    <?php $pp_1 = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_1_enable' ) ); ?>
                    <input type="checkbox" name="new_plan_options[recurring_payment_plan_1_enable]" <?php checked( $pp_1, 'yes' ); ?> value="yes" /><?php _e( '', 'rave-pay' ); ?>
                  </td>

                </tr>
                <tr> <!-- for monthly -->
                  <td>
                
                    <select class="regular-text code" name="new_plan_options[recurring_payment_plan_2]">
                     <?php $plan_listing = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_2' ) ); ?>

                    <?php for($x=0;$x<count($this->existing_payment_plans); $x++){  ?>
                  

                    <option <?php selected($plan_listing, $this->existing_payment_plans[$x]['id']) ?> value="<?php echo $this->existing_payment_plans[$x]['id'] ?>"><?php echo $this->existing_payment_plans[$x]['name'] ?></option>

                      <?php  }?>
                    </select>
                    <p class="description"> Choose your Payment Plan 2</p>
                  </td>
                  <td>
                    <?php $pp_2 = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_2_enable' ) ); ?>
                    <input type="checkbox" name="new_plan_options[recurring_payment_plan_2_enable]" <?php checked( $pp_2, 'yes' ); ?> value="yes" /><?php _e( '', 'rave-pay' ); ?>
                  </td>
                </tr>
                <tr> <!-- for quarterly -->
                <td>
                    <select class="regular-text code" name="new_plan_options[recurring_payment_plan_3]">
                     <?php $plan_listing = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_3' ) ); ?>

                    <?php for($x=0;$x<count($this->existing_payment_plans); $x++){  ?>


                    <option <?php selected($plan_listing, $this->existing_payment_plans[$x]['id']) ?> value="<?php echo $this->existing_payment_plans[$x]['id'] ?>"><?php echo $this->existing_payment_plans[$x]['name'] ?></option>

                      <?php  }?>
              </select>
              <p class="description"> Choose your Payment Plan 3</p>
              </td>
              <td>
                    <?php $pp_3 = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_3_enable' ) ); ?>
                    <input type="checkbox" name="new_plan_options[recurring_payment_plan_3_enable]" <?php checked( $pp_3, 'yes' ); ?> value="yes" /><?php _e( '', 'rave-pay' ); ?>
                  </td>
                </tr>
                <tr> <!-- for annually -->
                <td>
                <select class="regular-text code" name="new_plan_options[recurring_payment_plan_4]">
                     <?php $plan_listing = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_4' ) ); ?>

                    <?php for($x=0;$x<count($this->existing_payment_plans); $x++){  ?>

                    <option <?php selected($plan_listing, $this->existing_payment_plans[$x]['id']) ?> value="<?php echo $this->existing_payment_plans[$x]['id'] ?>"><?php echo $this->existing_payment_plans[$x]['name'] ?></option>

                      <?php  }?>
              </select>
              
              <p class="description"> Choose your Payment Plan 4</p>
              </td>
              <td>
                    <?php $pp_4 = esc_attr( $payment_plans_settings->get_option_value( 'recurring_payment_plan_4_enable' ) ); ?>
                    <input type="checkbox" name="new_plan_options[recurring_payment_plan_4_enable]" <?php checked( $pp_4, 'yes' ); ?> value="yes" /><?php _e( '', 'rave-pay' ); ?>
                  </td>
                </tr>
              </table>
              <p class="description"><b>NOTE:</b> Create your payment plans (<a href="https://ravesandbox.flutterwave.com/dashboard/payments/plans" target="_blank">Test</a> & <a href="https://rave.flutterwave.com/dashboard/payments/plans" target="_blank">Live</a>) for each intervals stated above if desired and add the payment plan ID to the fields above tied to the interval created. Click the 'checkbox' to enable it for users to see.</p>
            </td>
          </tr>
  </div>


        <!--Footer-->
        <div class="flex justify-end pt-2">

          <div class="ml-4 mr-4">
            <?php submit_button(); ?>
          </div>
        </div>

        </form>
        
      </div>
    </div>
  </div>




  <script>
    var openmodal = document.querySelectorAll('.modal-open')
    for (var i = 0; i < openmodal.length; i++) {
      openmodal[i].addEventListener('click', function(event){
    	event.preventDefault()
    	toggleModal()
      })
    }
    
    const overlay = document.querySelector('.modal-overlay')
    overlay.addEventListener('click', toggleModal)
    
    var closemodal = document.querySelectorAll('.modal-close')
    for (var i = 0; i < closemodal.length; i++) {
      closemodal[i].addEventListener('click', toggleModal)
    }
    
    document.onkeydown = function(evt) {
      evt = evt || window.event
      var isEscape = false
      if ("key" in evt) {
    	isEscape = (evt.key === "Escape" || evt.key === "Esc")
      } else {
    	isEscape = (evt.keyCode === 27)
      }
      if (isEscape && document.body.classList.contains('modal-active')) {
    	toggleModal()
      }
    };
    
    
    function toggleModal () {
      const body = document.querySelector('body')
      const modal = document.querySelector('.modal')
      modal.classList.toggle('opacity-0')
      modal.classList.toggle('pointer-events-none')
      body.classList.toggle('modal-active')
    }

    </script>



<script>
    var openmodal2 = document.querySelectorAll('.modal-openx')
    for (var i = 0; i < openmodal2.length; i++) {
      openmodal2[i].addEventListener('click', function(event){
    	event.preventDefault();
    	toggleModal2();
      })
    }
    
    overlay.addEventListener('click', toggleModal2)
    
    var closemodal2 = document.querySelectorAll('.modal-close2')
    for (var i = 0; i < closemodal2.length; i++) {
      closemodal2[i].addEventListener('click', toggleModal2)
    }
    
    document.onkeydown = function(evt) {
      evt = evt || window.event
      var isEscape = false
      if ("key" in evt) {
    	isEscape = (evt.key === "Escape" || evt.key === "Esc")
      } else {
    	isEscape = (evt.keyCode === 27)
      }
      if (isEscape && document.body.classList.contains('modal-active')) {
    	toggleModal2()
      }
    };

    function toggleModal2 () {
      const body = document.querySelector('body')
      const modal = document.querySelector('.modalx')
      modal.classList.toggle('opacity-0')
      modal.classList.toggle('pointer-events-none')
      body.classList.toggle('modal-active')
    }
    

    </script>


