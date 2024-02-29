=== Qvickly Order Management for WooCommerce ===
Contributors: Billmate, Krokedil, NiklasHogefjord
Tags: woocommerce, billmate, ecommerce, e-commerce, checkout, swish, invoice, part-payment, partpayment, card, mastercard, visa, trustly
Requires at least: 5.0
Tested up to: 6.2
Requires PHP: 7.0
WC requires at least: 5.0.0
WC tested up to: 7.6.1
Stable tag: __STABLE_TAG__
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== DESCRIPTION ==
Provides post-purchase order management for Qvickly Checkout for WooCommerce payment gateway.
Handle returns, activations and cancellations automatically without having to login to the Qvickly merchant portal, Qvickly Online.
This way you can save time and don’t have to work in both systems simultaneously.

=== Privacy ===
This plugin is relying upon the payment provider Qvickly. The payment data will be sent to them as a 3rd party service through the Qvickly API.
* Qvickly website: https://qvickly.io/
* Qvickly API documentation: https://billmate.github.io/api-docs/
* Qvickly terms and privacy policies: https://qvickly.io/villkor/

== Installation ==
1. Upload plugin folder to to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go WooCommerce Settings –> Payment Gateways –> Qvickly Checkout and configure your Qvickly Order Management settings.
4. Read more about the configuration process in the [plugin documentation].(https://support.billmate.se/hc/sv/articles/360017264318).

== Frequently Asked Questions ==
= Which countries does this payment gateway support? =
Qvickly Checkout works for merchants in Sweden.

= Where can I find Qvickly Order Management for WooCommerce documentation? =
For help setting up and configuring Qvickly Order Management for WooCommerce please refer to our [documentation](https://support.billmate.se/hc/sv/articles/360017264318).

= I have a suggestion for an improvement or a feature request =
We have a portal for users to provide feedback, [https://woocommerce.portal.billmate.se/](https://woocommerce.portal.billmate.se/). If you submit your idea here you will get notified with updates on your idea.

= I have found a bug, where should I report it? =
The easiest way to report a bug is to email us at [support@billmate.se](mailto:support@billmate.se). If you however are a developer you can feel free to raise an issue on GitHub, [https://github.com/Billmate/billmate-order-mangement-for-woocommerce](https://github.com/Billmate/billmate-order-mangement-for-woocommerce).

== Changelog ==
= 2024.02.28    - version 1.3.2 =
* Tweak         - Updated client identifier.

= 2023.05.04    - version 1.3.1 =
* Tweak         - Adds Qvickly icon.svg to WordPress.org assets.

= 2023.05.03    - version 1.3.0 =
* Tweak         - Change name from Billmate to Qvickly.

= 2022.11.28    - version 1.2.3 =
* Tweak         - Confirm compatibility with PHP 8.1, WP 6.x & WC 7.x.

= 2022.02.11    - version 1.2.2 =
* Tweak         - Bumped supported WP & WC versions.

= 2021.12.02    - version 1.2.1 =
* Tweak         - Added factoring as supported Billmate status for refunds.

= 2021.07.05    - version 1.2.0 =
* Feature       - Add action bco_callback_denied_order. Trigger cancel order request if denied order callback is performed from Billmate.
* Fix           - PHP8 warning fix.

= 2021.04.07    - version 1.1.2 =
* Fix           - Don't try to activate orders with Swish as payment method.

= 2021.02.16    - version 1.1.1 =
* Fix           - Add support for refunding (credit) part payment orders.

= 2021.02.08    - version 1.1.0 =
* Feature       - Add support to handle Activations and Cancelations for orders created via old Billmate plugin.

= 2021.01.28    - version 1.0.2 =
* Tweak         - Add Swedish translation files

= 2021.01.27    - version 1.0.1 =
* Tweak         - Improve readme and add more description

= 2021.01.25    - version 1.0.0 =
* Release       - First release of new Billmate Order Management for WooCommerce

= 2020.12.17    - version 0.2.1 =
* Fix           - Don't make activate payment request if payment was made via a direct payment method.

= 2020.12.07    - version 0.2.0 =
* Tweak         - Added readme.txt file.

= 2020.11.05    - version 0.1.0 =
