<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

final class AmzFulfillment_Template {
	public static function load($name, $controller = NULL) {
		$templateFile = sprintf('%s/AmzFulfillment/Template/%s.php', AMZFULFILLMENT_PLUGIN_DIR, $name);
		if(!is_readable($templateFile)) {
			AmzFulfillment_Logger::error('Template file not found: ' . $templateFile);
			return;
		}
		try {
			include $templateFile;
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf("Uncaught template exception in %s:%d : %s", $e->getFile(), $e->getLine(), $e->getMessage()));
		}
	}
}
