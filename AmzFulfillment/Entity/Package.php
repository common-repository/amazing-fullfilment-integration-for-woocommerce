<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Package {
	private $packageNumber = NULL;
	private $orderId = NULL;
	private $trackingNumber = NULL;
	private $carrierCode = NULL;
	private $shipTime = NULL;
	private $estimatedArrivalTime = NULL;
	private $address = NULL;
	private $status = NULL;
	private $trackingEvents = array();
	private $trackingError = NULL;

	public function __construct($packageNumber = NULL, $orderId = NULL) {
		$this->packageNumber = $packageNumber;
		$this->orderId = $orderId;
	}

	/**
	 * @return integer
	 */
	public function getPackageNumber() {
		return $this->packageNumber;
	}

	/**
	 * @param integer $packageNumber
	 */
	public function setPackageNumber($packageNumber) {
		$this->packageNumber = $packageNumber;
	}

	/**
	 * @return NULL|integer
	 */
	public function getOrderId() {
		return $this->orderId;
	}

	/**
	 * @param integer $orderId
	 */
	public function setOrderId($orderId) {
		$this->orderId = $orderId;
	}

	/**
	 * @return string
	 */
	public function getTrackingNumber() {
		return $this->trackingNumber;
	}

	/**
	 * @param string $trackingNumber
	 */
	public function setTrackingNumber($trackingNumber) {
		$this->trackingNumber = $trackingNumber;
	}

	/**
	 * @return string
	 */
	public function getCarrierCode() {
		return $this->carrierCode;
	}

	/**
	 * @param string $carrierCode
	 */
	public function setCarrierCode($carrierCode) {
		$this->carrierCode = $carrierCode;
	}

	public function getShipTime() {
		return $this->shipTime;
	}

	public function setShipTime($time) {
		$this->shipTime = $time;
	}

	public function getEstimatedArrivalTime() {
		return $this->estimatedArrivalTime;
	}

	public function setEstimatedArrivalTime($time) {
		$this->estimatedArrivalTime = $time;
	}

	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getStatusText() {
		return isset(AmzFulfillment_Amazon_Package::$status[$this->status]) ? AmzFulfillment_Amazon_Package::$status[$this->status] : "";
	}

	public function getTrackingEvents() {
		return $this->trackingEvents;
	}

	public function setTrackingEvents($trackingEvents) {
		if(is_array($trackingEvents)) {
			$this->trackingEvents = $trackingEvents;
		} else {
			$this->trackingEvents = array();
		}
	}

	public function addTrackingEvent(AmzFulfillment_Entity_PackageTrackingEvent $trackingEvent) {
		$this->trackingEvents[] = $trackingEvent;
	}

	public function getTrackingError() {
		return $this->trackingError;
	}

	public function setTrackingError($trackingError) {
		$this->trackingError = $trackingError;
	}

	public function isTrackingError() {
		return !empty($this->trackingError);
	}

	public function isDelivered() {
		return $this->status == 'DELIVERED';
	}

	public function equals($other) {
		if(!is_a($other, 'AmzFulfillment_Entity_Package')) {
			return false;
		} elseif($this->getAddress() === $other->getAddress() &&
				$this->getTrackingNumber() === $other->getTrackingNumber() &&
				$this->getTrackingEvents() === $other->getTrackingEvents() &&
				$this->getTrackingError() === $other->getTrackingError() &&
				$this->getStatus() === $other->getStatus() &&
				$this->getShipTime() === $other->getShipTime() &&
				$this->getPackageNumber() === $other->getPackageNumber() &&
				$this->getOrderId() === $other->getOrderId() &&
				$this->getEstimatedArrivalTime() === $other->getEstimatedArrivalTime() &&
				$this->getCarrierCode() === $other->getCarrierCode()) {
			return true;
		} else {
			return false;
		}
	}

	public static function createInstance($obj) {
		if(!is_object($obj)) {
			return NULL;
		}
		$instance = new self($obj->packageNumber, $obj->orderId);
		if(isset($obj->packageNumber)) {
			$instance->setPackageNumber($obj->packageNumber);
		}
		if(isset($obj->orderId)) {
			$instance->setOrderId($obj->orderId);
		}
		if(isset($obj->trackingNumber)) {
			$instance->setTrackingNumber($obj->trackingNumber);
		}
		if(isset($obj->carrierCode)) {
			$instance->setCarrierCode($obj->carrierCode);
		}
		if(isset($obj->shipTime)) {
			$instance->setShipTime($obj->shipTime);
		}
		if(isset($obj->estimatedArrivalTime)) {
			$instance->setEstimatedArrivalTime($obj->estimatedArrivalTime);
		}
		if(isset($obj->address)) {
			$instance->setAddress($obj->address);
		}
		if(isset($obj->status)) {
			$instance->setStatus($obj->status);
		}
		if(isset($obj->trackingEvents)) {
			$events = array();
			foreach(json_decode($obj->trackingEvents) as $event) {
				$events[] = new AmzFulfillment_Entity_PackageTrackingEvent($event->code, $event->time, $event->address);
			}
			$instance->setTrackingEvents($events);
		}
		if(isset($obj->trackingError)) {
			$instance->setTrackingError($obj->trackingError);
		}
		return $instance;
	}
}
