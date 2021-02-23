=== Rave Payment Forms ===
Contributors: flutterwave
Tags: rave, payment form, payment gateway, bank account, credit card, debit card, nigeria, kenya, international, mastercard, visa
Donate link: http://rave.flutterwave.com/
Requires at least: 4.4
Tested up to: 5.4.0
Requires PHP: 5.4
Stable tag: 1.0.3
License: MIT
License URI: https://github.com/Flutterwave/rave-payment-forms/blob/master/LICENSE

Accept Credit card, Debit card and Bank account payment directly on your WordPress site with the Rave payment gateway.




== Description ==

Signup for a live rave account at https://rave.flutterwave.com and a sandbox account on https://ravesandbox.flutterwave.com

Please see our Terms of service here: http://bit.ly/2GH9oTy

For more information on the data we collect please view our privacy policy here: http://bit.ly/2HcQUv7

Rave is available in:

* Nigeria
* Ghana
* Kenya
* South Africa
* North America and Europe (USD, GBP, EUR)

= Configuration options = 

* Pay Button Public Key (live/Test) - Enter your public key which can be retrieved from Settings > API on your Rave account dashboard.
* Pay Button Secret Key (live/Test) - Enter your secret key which can be retrieved from Settings > API on your Rave account dashboard.
* Go Live - Tick that section to turn your rave plugin live.
* Modal Title - (Optional) customize the title of the Pay Modal. Default is FLW PAY.
* Modal Description - (Optional) customize the description on the Pay Modal. Default is FLW PAY MODAL.
* Modal Logo - (Optional) customize the logo on the Pay Modal. Enter a full url (with \'http\'). Default is Rave logo.
* Success Redirect URL - (Optional) The URL the user should be redirected to after a successful payment. Enter a full url (with 'http:\\'). Default: '\'.
* Failed Redirect URL - (Optional) The URL the user should be redirected to after a failed payment. Enter a full url (with 'http:\\'). Default: '\'.
* Pay Button Text - (Optional) The text to display on the button. Default: "PAY NOW".
* Charge Currency - (Optional) The currency the user is charged. Default: "NGN".
* Charge Country - (Optional) The country the merchant is serving. Default: "NG: Nigeria".
* Form Style - (Optional) Disable form default style and use the activated theme style instead.
* Click Save Changes to save your changes.

= Styling =

You can enable default theme's style to override default form style from the Settings page. Or you can override the formclass .flw-simple-pay-now-form from your stylesheet.

= Usage =

* a. Shortcode
Insert the shortcode anywhere on your page or post that you want the form to be displayed to the user.
Basic: requires the user to enter amount and email to complete payment
[flw-pay-button]


With button text:
[flw-pay-button]Button Text[/flw-pay-button]


With attributes: email or use_current_user_email with value "yes", amount
[flw-pay-button amount="1290" email="customer@email.com" ]

or

[flw-pay-button amount="1290" use_current_user_email="yes" ]


With attributes and button text: email, amount
[flw-pay-button amount="1290" email="customer@email.com" ]Button Text[/flw-pay-button]



With currency

[flw-pay-button custom_currency="NGN,GBP,USD"]

With attributes: email or use_current_user_email with value "yes", amount and currency
[flw-pay-button amount="1290" email="customer@email.com" custom_currency= "NGN, GBP, USD" ]

or

[flw-pay-button amount="1290" use_current_user_email="yes" custom_currency= "NGN, GBP, USD" ]

With currency:
[flw-pay-button custom_currency="NGN,GBP,USD"]

With attributes: email or use_current_user_email with value "yes", amount and currency
[flw-pay-button amount="1290" email="customer@email.com" custom_currency= "NGN, GBP, USD" ]

or

[flw-pay-button amount="1290" use_current_user_email="yes" custom_currency= "NGN, GBP, USD" ]

or

Donation page shortcode:

[flw-donation-page]

* b. Visual Composer
The shortcode can be added via Visual Composer elements.
On Visual Composer Add Element dialog, click on "Rave Forms" and select the type of form you want to include on your page. 


On the "Form Settings" dialog, fill in the form attributes and click "Save Changes".

Payment Form successfully added to the page. 


= Transaction List =
All the payments made through the forms to Rave can be accessed on Rave > Transactions page.


= TODO =
* Add advanced forms to include customization where user can choose what fields to add to the form.
* Multiple Pay Button integrations.

= Suggestions / Contributions =
For issues, suggestions and feature request, click here. To contribute, fork the repo, add your changes and modifications, then create a pull request.



== Installation ==

Automatic Installation

* Login to your WordPress Dashboard.
* Click on "Plugins > Add New" from the left menu.
* In the search box type Rave Payment Forms.
* Click on Install Now on Rave Payment Forms to install the plugin on your site.
* Confirm the installation.
* Activate the plugin.
* Go to "Rave > Settings" from the left menu to configure the plugin.


Manual Installation

* Download the plugin zip file.
* Login to your WordPress Admin. Click on \"Plugins > Add New\" from the left menu.
* Click on the \"Upload\" option, then click \"Choose File\" to select the zip file you downloaded. Click \"OK\" and \"Install Now\" to complete the installation.
* Activate the plugin.
* Go to \"Rave > Settings\" from the left menu to configure the plugin.
* For FTP manual installation, check here.

= Configure the plugin =
To configure the plugin, go to Rave > Settings from the left menu.

== Frequently Asked Questions ==
Q: How do I get my Test public and secret keys ?
A: To get your test public and secret key visit this page to see how: https://flutterwavedevelopers.readme.io/v2.0/docs/api-keys

Q: How do I move from test to production on the plugin ?
A: You need to toggle the go live check box by clicking on it, you also need to make sure your live keys have been added to the rave configuration page on wordpress.

Q: How do I charge my customers in multiple currencies ?
A: We allow you use shortcodes to append multiple currencies to the form shown to your customers simple embed with the currency shortcode style above.

== Screenshots ==

1. To configure the plugin, go to Rave > Settings from the left menu.
2. On Visual Composer Add Element dialog, click on "Rave Forms" and select the type of form you want to include on your page.
3. On the "Form Settings" dialog, fill in the form attributes and click "Save Changes". 
4. Payment Form successfully added to the page.
5. All the payments made through the forms to Rave can be accessed on Rave > Transactions page.



== Changelog ==
v 1.0.1
* Recurring payments now enabled.

v 1.0.0

== Upgrade Notice ==
v1.0.1 - 12-02-2018
* This version doesn't redirect after failure, it allows the customer try payment again.
* This version allows you use multiple currencies on the Wordpress payment form.
* This version now has recurring payments.
