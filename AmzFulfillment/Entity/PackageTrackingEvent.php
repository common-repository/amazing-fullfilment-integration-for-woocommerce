<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_PackageTrackingEvent {
	public $code;
	public $time;
	public $address;

	public function __construct($code, $time, $address) {
		$this->code = $code;
		$this->time = $time;
		$this->address = $address;
	}

	public function getCode() {
		return $this->code;
	}

	public function getTime() {
		return $this->time;
	}

	public function getAddress() {
		return $this->address;
	}
}
