=== Amazing Fulfillment Integration for WooCommerce ===
Contributors: Denny1989
Tags: Amazon, WooCommerce, Fulfillment, FBA, Multichannel
Requires at least: 4.2
Tested up to: 5.2.1
Stable tag: 2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 2.4
WC tested up to: 3.6.4


An easy to use plugin that lets you send WooCommerce Orders to multichannel fulfillment by Amazon

== Description ==
The plugin is free to use and offers the following features:
* Works for all Amazon-Marketplaces
* Sync amazon stock with WooCommerce for selected products (manual or automatically)
* Send orders manually (single or bulk) to Amazon directly via the WooCommerce orders menu
* Various options on how to handle orders that can't be fulfilled (e.g. if there is no stock available)

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the WooCommerce -> Amazing Fulfillment screen to configure the plugin

== Screenshots ==
1. This screenshot showes Amazon to WooCommerce stock sync as one of many features


== Changelog ==
= 2.1 =
* Wordpress 5.2.1 and WooCommerce 3.6.4 testet and supported
= 2.0 =
* Major release with many fixes and enhancements
= 1.1.3 =
* Fix plugin activation errors affected by some PHP versions
= 1.1.2 =
* Set stock-status to in-stock, if product quantity is synced to more than 0
* Set stock-status to out-of-stock, if product quantity is synced to 0
* Add links to WooCommerce products in inventory tab
* Set hold-option default to disabled
* Add notice to inventory tab
* Add link to dejo-commerce amazon mws tutorial in settings tab
= 1.1.1 =
* Fix failed amazon api calls if a woocommerce product has an empty sku
* Change amazon mws credentials test to a more tollerant amazon api call (listAllFulfillmentOrders)
* Add first screenshot to plugin description
= 1.1.0 =
* Add package tracking
* Add reset to defaults button for automation rules
* Enhanced fulfilment overview
* Fix possible event endless loop in automation rules
* Fix no success message after submitting fulfillment order manually (order menu)
* Fix no fulfilment status updates, when automate is disabled
* Fix event action woocommerce set to completed is not working
* Fix email notification: Add order status to message
* Fix amazon order prefix to avoid orderIds with >40 chars
* Refactor worker
* Optimize text content
* Link to buy a license is missing in the license tab of the plugin
= 1.0.1 =
* Fixed issue with some amazon mws keys
* Update support tab text
* Add pro version preview images to pro tabs
* Add option to enable / disable automate rules execution globally
* Add entry to fulfillments, immediately after successful fulfilment
* Add status message after bulk action
= 1.0.0 =
* First stable version

== Upgrade Notice ==
= 2.0 =
New major release with many fixes and enhancements
= 1.1.3 =
Minor bug fix release
= 1.1.2 =
Add stock-status changes after quantity sync. Minor fixes.
= 1.1.1 =
Bug fix release
= 1.1.0 =
First major update after launch. Amazon package tracking is now implemented
= 1.0.1 =
Amazing features and fixes
= 1.0.0 =
First stable version
