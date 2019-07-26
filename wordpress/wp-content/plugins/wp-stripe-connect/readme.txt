=== Woocommerce Marketplace Stripe Connect ===
Contributors: Webkul Team
Tags: form, database, db, data, value, submit
WordPress :
  * Requires at least: 4.4
  * Tested up to: 4.9.0
WooCommerce: 3.2.x
License: GNU/GPL for more info see license.txt included with plugin
License URI: http://www.gnu.org/licenseses/gpl-2.0.html

Wordpress WooCommerce Marketplace Stripe Connect works with checkout  payment gateway. This Plugin can be run in both test and live mode.
In this seller has to connect using stripe connect.
It is an ad-don of Wordpress WooCommerce Marketplace. To use this module you must have installed WooCommerce and Marketplace first.

== Description ==
	* WooCommerce Marketplace Stripe Connect Features:
		1. Download zip file from http://webkul.com.
		2. Unzip the file.
		3. Select the following files and directories
			* assets
			* includes
			* readme.txt
			* license.txt
			* stripe-connect.php
			* payment-stripe.png
			* index.htm
		4. Now make a zip file selecting all mentioned files and directories and rename the zip file as stripe-connect
		5. After making zip file you should have a zip file name **stripe-connect.zip** with all mentioned files i.e assets, includes, readme.txt, license.txt, payment-stripe.png, index.htm, and stripe-connect.php in it.
		6. Goto wordpress admin dashboard.
		7. Goto add new plugin.
		8. Goto upload plugin and browse to stripe-connect.zip file.
		9. After uplaoding it activate plugin.

== Installation ==

1. Upload `stripe-connect` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You need to have Woocommerce and Marketplace installed in order to get this plugin working.

== Frequently Asked Questions ==

For any Query please generate a ticket at https://webkul.com/ticket/

== 1.1.2 ==
Enhancement - Implemented the Direct Charge API for payment.
Enhancement - Updated the code structure.
Enhancement - Updated commission part.
Tweak - Added the translation in the plugin.

== 1.1.1 ==
* Stripe payment is done using token

== 1.1.0 ==
* Stripe connected account data issue fixed on seller profile update
* Added card type not found message checkout page if type not set in configuration
* Added settings link in plugin actions

== 1.0.1 ==
* Compatiblity issues with latest woocommerce version are fixed
* Stripe connect account form creation issue for seller end fixed
* Make compatible with woocommerce3.0

== 1.0.0 ==
* Initial verison

Current Version 1.1.2
