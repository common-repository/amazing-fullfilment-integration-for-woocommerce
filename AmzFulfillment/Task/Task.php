<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
abstract class AmzFulfillment_Task_Task {
	protected abstract function run();

	public function runTask() {
		$class = get_called_class();
		AmzFulfillment_Logger::debug($class . " run");
		try {
			$t = microtime(true);
			$this->run();
			AmzFulfillment_Logger::debug(sprintf('%s succeeded after %.0f ms', $class, (microtime(true) - $t) * 1000.0));
		} catch(Exception $e) {
			AmzFulfillment_Logger::error($class . " failed: " . $e->getMessage());
		}
	}
}
