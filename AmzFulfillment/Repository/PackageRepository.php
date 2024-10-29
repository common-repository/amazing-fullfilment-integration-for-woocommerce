<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Repository_PackageRepository extends AmzFulfillment_Repository_Repository {
	const TABLE = 'amzFulfillment_package';

	public function __construct() {
		$create = "
				CREATE TABLE _TBL_ (
					`packageNumber`        INT          NOT NULL UNIQUE PRIMARY KEY,
					`orderId`              INT          NOT NULL,
					`trackingNumber`       VARCHAR(64)  NOT NULL DEFAULT '',
					`carrierCode`          VARCHAR(64)  NOT NULL DEFAULT '',
					`shipTime`             DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
					`estimatedArrivalTime` DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
					`address`              VARCHAR(255) NOT NULL DEFAULT '',
					`status`               VARCHAR(32)  NOT NULL DEFAULT '',
					`trackingEvents`       TEXT         NOT NULL DEFAULT '',
					`trackingError`        VARCHAR(255) NOT NULL DEFAULT '')";
		parent::__construct(SELF::TABLE, $create, TRUE);
	}

	/**
	 * @param AmzFulfillment_Entity_Package $package
	 */
	public function set($package) {
		$this->setQuery(sprintf("DELETE FROM _TBL_ WHERE `packageNumber` = %d", $package->getPackageNumber()));
		$query = "INSERT INTO _TBL_ (packageNumber,orderId,trackingNumber,carrierCode,shipTime,estimatedArrivalTime,address,status,trackingEvents,trackingError)"
				. " VALUES(%d,%d,'%s','%s','%s','%s','%s','%s','%s','%s')";
		$query = sprintf($query,
				$package->getPackageNumber(),
				$package->getOrderId(),
				esc_sql($package->getTrackingNumber()),
				esc_sql($package->getCarrierCode()),
				esc_sql($package->getShipTime()),
				esc_sql($package->getEstimatedArrivalTime()),
				esc_sql($package->getAddress()),
				esc_sql($package->getStatus()),
				esc_sql(json_encode($package->getTrackingEvents())),
				esc_sql($package->getTrackingError())
		);
		$this->setQuery($query);
	}

	/**
	 * @param integer $packageNumber
	 * @return AmzFulfillment_Entity_Package|NULL
	 */
	public function get($packageNumber) {
		$results = $this->getQuery(sprintf("SELECT * FROM _TBL_ WHERE `packageNumber`=%d", $packageNumber));
		return isset($results[0]) ? AmzFulfillment_Entity_Package::createInstance($results[0]) : NULL;
	}

	/**
	 * @param integer $orderId
	 * @return AmzFulfillment_Entity_Package[]
	 */
	public function getByOrder($orderId) {
		$packages = array();
		$results = $this->getQuery(sprintf("SELECT * FROM _TBL_ WHERE `orderId`=%d ORDER BY `packageNumber` DESC", $orderId));
		foreach($results as $result) {
			$packages[] = AmzFulfillment_Entity_Package::createInstance($result);
		}
		return $packages;
	}

	/**
	 * @return AmzFulfillment_Entity_Package[]
	 */
	public function getOpen() {
		$packages = array();
		$results = $this->getQuery("SELECT * FROM _TBL_ WHERE `status` <> 'DELIVERED' ORDER BY `packageNumber` DESC");
		foreach($results as $result) {
			$packages[] = AmzFulfillment_Entity_Package::createInstance($result);
		}
		return $packages;
	}

	/**
	 * @param integer $packageNumber
	 * @return boolean
	 */
	public function exist($packageNumber) {
		return $this->get($packageNumber) != NULL;
	}
}
