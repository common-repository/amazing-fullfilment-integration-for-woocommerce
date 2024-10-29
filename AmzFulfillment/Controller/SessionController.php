<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_SessionController {
	const KEY = 'amzFulfillment';

	public function __construct() {
		if(!session_id()) {
			@session_start();
		}
		if(!isset($_SESSION[self::KEY])) {
			$_SESSION[self::KEY] = array();
		}
	}

	public function get($key) {
		return $this->exist($key) ? $_SESSION[self::KEY][$key] : NULL;
	}

	public function set($key, $value) {
		$_SESSION[self::KEY][$key] = $value;
	}

	public function add($key, $value) {
		if(!is_array($this->get($key))) {
			$arr = array();
		} else {
			$arr = $this->get($key);
		}
		$arr[] = $value;
		$this->set($key, $arr);
	}

	public function exist($key) {
		return array_key_exists($key, $_SESSION[self::KEY]);
	}
}
