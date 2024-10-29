<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Repository_FulfillmentRepository extends AmzFulfillment_Repository_Repository {
	const TABLE = 'amzFulfillment_fulfillment';

	public function __construct() {
		$create = "
				CREATE TABLE `_TBL_` (
					`orderId`           INT         NOT NULL UNIQUE PRIMARY KEY,
					`fulfillmentStatus` VARCHAR(32) NOT NULL,
					`fulfillmentTime`   DATETIME    DEFAULT NULL)";
		parent::__construct(SELF::TABLE, $create, TRUE);
	}

	/**
	 * @param integer $orderId
	 * @return NULL|AmzFulfillment_Entity_Fulfillment
	 */
	public function get($orderId) {
		$result = $this->getQuery(sprintf("SELECT * FROM _TBL_ WHERE `orderId`=%d", $orderId));
		return isset($result[0]) ? AmzFulfillment_Entity_Fulfillment::createInstance($result[0]) : NULL;
	}

	/**
	 * @param integer $startTime
	 * @return AmzFulfillment_Entity_Fulfillment[]
	 */
	public function getAllOpen($startTime) {
		$fulfillments = array();
		$results = $this->getQuery(sprintf("SELECT * FROM `_TBL_` WHERE `fulfillmentTime` >= '%s'", date('Y-m-d H:i:s', $startTime)));
		foreach($results as $result) {
			$fulfillments[] = AmzFulfillment_Entity_Fulfillment::createInstance($result);
		}
		return $fulfillments;
	}

	/**
	 * @return AmzFulfillment_Entity_Fulfillment[]
	 */
	public function getAll() {
		$fulfillments = array();
		$results = $this->getQuery("SELECT * FROM `_TBL_` ORDER BY orderId ASC");
		foreach($results as $result) {
			$fulfillments[] = AmzFulfillment_Entity_Fulfillment::createInstance($result);
		}
		return $fulfillments;
	}

	/**
	 * @param AmzFulfillment_Entity_Fulfillment $fulfillment
	 */
	public function set($fulfillment) {
		$this->setQuery(sprintf("DELETE FROM `_TBL_` WHERE orderId=%d", $fulfillment->getOrderId()));
		$this->setQuery(sprintf("INSERT INTO `_TBL_` (orderId,fulfillmentStatus,fulfillmentTime) VALUES(%d,'%s','%s')",
				$fulfillment->getOrderId(),
				esc_sql($fulfillment->getStatus()),
				date('Y-m-d H:i:s', $fulfillment->getCreateTime())));
	}

	/**
	 * @param integer $orderId
	 * @return boolean
	 */
	public function exist($orderId) {
		return $this->get($orderId) != NULL;
	}
}
