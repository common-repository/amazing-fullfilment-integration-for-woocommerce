<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Log {
	private $id;
	private $time;
	private $msg;

	/**
	 * @param int $id
	 * @param string $time
	 * @param string $msg
	 */
	public function __construct($id, $time, $msg) {
		$this->id = $id;
		$this->time = $time;
		$this->msg = $msg;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @return string
	 */
	public function getMsg() {
		return $this->msg;
	}
}
