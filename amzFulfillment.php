<?php

/**
 * Plugin Name: Amazing Fulfillment Integration for WooCommerce
 * Plugin URI: https://www.amazing-fulfillment.com
 * Description: A plugin that lets you send WooCommerce Orders to Multichannel fulfillment by Amazon
 * Author: DEJO-Commerce
 * Author URI: https://www.amazing-fulfillment.com/imprint/
 * Version: 2.1
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.2
 * Tested up to: 5.2.1
 * Requires at least WooCommerce: 2.4
 * Tested up to WooCommerce: 3.6.4
 */

require_once ABSPATH . '/wp-admin/includes/plugin.php';
define('AMZFULFILLMENT_PLUGIN_FILE',	__FILE__);
define('AMZFULFILLMENT_PLUGIN_DIR',		dirname(__FILE__)) . DIRECTORY_SEPARATOR;
define('AMZFULFILLMENT_PLUGIN_URL',		plugin_dir_url(__FILE__));
define('AMZFULFILLMENT_PLUGIN_ID',		preg_replace("/^.*\//", '', AMZFULFILLMENT_PLUGIN_DIR));

/**
 * AMZFULFILLMENT_LOGLEVEL
 *
 * Logging configuration
 * 
 * Log entry will be written to 
 * - Database (Shown in logs tab)
 * - Admin panel message
 * - Wordpress debug log
 * 
 * For wordpress logging (wp-content/debug.log) WP_DEBUG and WP_DEBUG_LOG needs to be set to true in wp-config.php:
 *   define('WP_DEBUG', true);
 *   define('WP_DEBUG_LOG', true);
 * 
 * Possible log values:
 *     ERROR             Log only errors
 *     WARN              Log warnings and errors
 *     INFO   (default)  Log infos, warnings and errors
 *     DEBUG             Log debug informations, infos, warnings and errors
 *     TRACE             Log low level debug informations, infos, warnings and errors
 */
define('AMZFULFILLMENT_LOGLEVEL', 'INFO');

/**
 * Plugin root hooks
 */
spl_autoload_register('amzFulfillmentAutoloader');
add_action('plugins_loaded', 'amzFulfillmentInit');
register_activation_hook(AMZFULFILLMENT_PLUGIN_FILE, 'amzFulfillmentActivate');
register_deactivation_hook(AMZFULFILLMENT_PLUGIN_FILE, 'amzFulfillmentDeactivate');

/**
 * Class autoloader
 */
function amzFulfillmentAutoloader($className) {
	$loads = array(
			'AmzFulfillment',
			'FBAInventoryServiceMWS',
			'FBAOutboundServiceMWS',
			'MarketplaceWebService'
	);
	$classParts = explode('_', $className);
	if(!in_array($classParts[0], $loads)) {
		return;
	}
	$classPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
	$classFile = AMZFULFILLMENT_PLUGIN_DIR . DIRECTORY_SEPARATOR .$classPath . '.php';
	require_once($classFile);
}

/**
 * amzFulfillmentWooCommerceError
 *
 * Display error message: WooCommerce not active
 */
function amzFulfillmentWooCommerceError() {
	printf('<div class="error"><p>%s</p></div>', __('To use Amazing Fulfillment Integration for WooCommerce it is required that WooCommerce is installed and activated'));
}

/**
 * amzFulfillmentActivate
 *
 * Plugin activate event
 */
function amzFulfillmentActivate() {
	AmzFulfillment_Main::instance()->init();
	AmzFulfillment_Logger::debug(AMZFULFILLMENT_PLUGIN_ID . ' activate');
	foreach(AmzFulfillment_Main::instance()->data()->repositories() as $repository) {
		try {
			$repository->init();
		} catch(Exception $e) {
			AmzFulfillment_Logger::debug(sprintf('%s database table %s initialization failed: %s', AMZFULFILLMENT_PLUGIN_ID, $repository->getTable(), $e->getMessage()));
		}
	}
}

/**
 * amzFulfillmentDeactivate
 *
 * Plugin deactivate event
 */
function amzFulfillmentDeactivate() {
	AmzFulfillment_Logger::debug(AMZFULFILLMENT_PLUGIN_ID . ' deactivate');
	AmzFulfillment_Main::instance()->clearCrons();
	foreach(AmzFulfillment_Main::instance()->data()->repositories() as $repository) {
		try {
			$repository->clean();
		} catch(Exception $e) {
			AmzFulfillment_Logger::debug(sprintf('%s database table %s cleanup failed: %s', AMZFULFILLMENT_PLUGIN_ID, $repository->getTable(), $e->getMessage()));
		}
	}
}

/**
 * amzFulfillmentInit
 *
 * Initialize amzFulfillment plugin components
 */
function amzFulfillmentInit() {
	if(!is_plugin_active('woocommerce/woocommerce.php') && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
		add_action('admin_notices', 'amzFulfillmentWooCommerceError');
		return;
	}
	AmzFulfillment_Main::instance()->init();
}
