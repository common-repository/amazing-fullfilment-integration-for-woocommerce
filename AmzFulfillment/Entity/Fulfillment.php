<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Fulfillment {
	private $orderId;
	private $status;
	private $createTime;

	public function __construct($orderId = NULL, $createTime = NULL) {
		$this->setOrderId($orderId);
		if($createTime !== NULL) {
			$this->setCreateTime($createTime);
		} else {
			$this->setCreateTime(time());
		}
	}

	/**
	 * @return integer
	 */
	public function getOrderId() {
		return $this->orderId;
	}

	/**
	 * @param integer $orderId
	 */
	public function setOrderId($orderId) {
		$this->orderId = (int) $orderId;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return integer
	 */
	public function getCreateTime() {
		return $this->createTime;
	}

	/**
	 * @param mixed $createTime
	 */
	public function setCreateTime($createTime) {
		if(is_integer($createTime)) {
			$this->createTime = $createTime;
		} elseif(is_string($createTime)) {
			$this->createTime = strtotime($createTime);
		} else {
			$this->createTime = NULL;
		}
	}

	/**
	 * @param object $obj
	 * @return NULL|AmzFulfillment_Entity_Fulfillment
	 */
	public static function createInstance($obj) {
		if(!is_object($obj)) {
			return NULL;
		}
		$instance = new self();
		if(isset($obj->orderId)) {
			$instance->setOrderId($obj->orderId);
		}
		if(isset($obj->fulfillmentStatus)) {
			$instance->setStatus($obj->fulfillmentStatus);
		}
		if(isset($obj->fulfillmentTime)) {
			$instance->setCreateTime($obj->fulfillmentTime);
		}
		return $instance;
	}
}
