<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_PackageController {
	private $packageRepo;
	private $mws;

	public function __construct() {
		$this->packageRepo = AmzFulfillment_Main::instance()->data()->packages();
		$this->mws = AmzFulfillment_Main::instance()->amazonMWS();
	}

	public function update($packageNumber, $orderId) {
		$package = NULL;
		if($this->packageRepo->exist($packageNumber)) {
			$package = $this->packageRepo->get($packageNumber);
		} else {
			AmzFulfillment_Logger::info(sprintf('WooCommerceOrder-%d new Amazon package-%d', $orderId, $packageNumber));
			$package = new AmzFulfillment_Entity_Package($packageNumber, $orderId);
		}
		if($package->getStatus() === "DELIVERED") {
			return false;
		}
		try {
			$change = false;
			$trackingChange = false;
			$result = $this->mws->fbaOutbound()->getPackageTrackingDetails($package->getPackageNumber());
			$trackingNumber = $result->getTrackingNumber();
			$carrierCode = $result->getCarrierCode();
			$shipTime = AmzFulfillment_Amazon_Date::toDate($result->getShipDate());
			$eta = AmzFulfillment_Amazon_Date::toDate($result->getEstimatedArrivalDate());
			$address = $this->formatAddress($result->getShipToAddress()->getCity(), $result->getShipToAddress()->getState(), $result->getShipToAddress()->getCountry());
			$status = $result->getCurrentStatus();
			$events = array();
			foreach($result->getTrackingEvents()->getmember() as $event) {
				$eventCode = $event->getEventCode();
				$eventAddress = "";
				$eventDate = AmzFulfillment_Amazon_Date::toDate($event->getEventDate());
				if(!is_null($event->getEventAddress())) {
					$eventAddress = $this->formatAddress($event->getEventAddress()->getCity(), $event->getEventAddress()->getState(), $event->getEventAddress()->getCountry());
				}
				$events[] = new AmzFulfillment_Entity_PackageTrackingEvent($eventCode, $eventDate, $eventAddress);
			}
			if($package->getStatus() !== $status) {
				$change = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d status '%s'", $orderId, $packageNumber, $status));
				$package->setStatus($status);
			}
			if($package->getTrackingNumber() !== $trackingNumber && !empty($trackingNumber)) {
				$change = true;
				$trackingChange = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d tracking number '%s'", $orderId, $packageNumber, $trackingNumber));
				$package->setTrackingNumber($trackingNumber);
			}
			if($package->getCarrierCode() !== $carrierCode) {
				$change = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d carrier '%s'", $orderId, $packageNumber, $carrierCode));
				$package->setCarrierCode($carrierCode);
			}
			if($package->getShipTime() !== $shipTime) {
				$change = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d ship time '%s'", $orderId, $packageNumber, $shipTime));
				$package->setShipTime($shipTime);
			}
			if($package->getEstimatedArrivalTime() !== $eta) {
				$change = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d estimated time '%s'", $orderId, $packageNumber, $eta));
				$package->setEstimatedArrivalTime($eta);
			}
			if($package->getAddress() !== $address && !empty($address)) {
				$change = true;
				AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d package-%d address '%s'", $orderId, $packageNumber, $address));
				$package->setAddress($address);
			}
			if(count($package->getTrackingEvents()) !== count($events)) {
				$change = true;
				$package->setTrackingEvents($events);
			}
			if(!empty($package->getTrackingError())) {
				$change = true;
				$package->setTrackingError(NULL);
			}
			if($change) {
				$this->packageRepo->set($package);
				if($trackingChange && $package->getStatus() !== "DELIVERED") {
					AmzFulfillment_Main::instance()->eventController()->amazonPackageChanged($orderId, $packageNumber, AmzFulfillment_Entity_Event::AMAZON_PACKAGE_TRACKING_NUMBER);
				}
				return true;
			}
		} catch(Exception $e) {
			$package->setTrackingError($e->getMessage());
			AmzFulfillment_Logger::warn(sprintf("WooCommerceOrder-%d package-%d %s", $orderId, $packageNumber, $e->getMessage()));
			$this->packageRepo->set($package);
		}
		return false;
	}

	/**
	 * @param integer $orderId
	 */
	public function find($orderId) {
		$count = 0;
		try {
			$result = $this->mws->fbaOutbound()->getFulfillmentOrder($orderId);
			foreach($result->getFulfillmentShipment()->getMember() as $shipment) {
				if(!$shipment->isSetFulfillmentShipmentPackage()) {
					continue;
				}
				foreach($shipment->getFulfillmentShipmentPackage()->getMember() as $shipmentPackage) {
					$packageNumber = $shipmentPackage->getPackageNumber();
					if(!empty($packageNumber) && !$this->packageRepo->exist($packageNumber)) {
						if($this->update($packageNumber, $orderId)) {
							$count++;
						}
					}
				}
			}
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d failed to get packages from Amazon: %s', $orderId, $e->getMessage()));
		}
		return $count;
	}

	public function formatAddress($city, $state, $country) {
		$address = array();
		if(!empty($city)) {
			$address[] = $city;
		}
		if(!empty($state)) {
			$address[] = $state;
		}
		if(!empty($country)) {
			$address[] = ' (' . $country . ')';
		}
		return str_replace('  ', ' ', trim(implode(' ', $address)));
	}
}
