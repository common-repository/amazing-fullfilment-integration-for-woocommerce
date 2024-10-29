<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Repository_InventoryRepository extends AmzFulfillment_Repository_Repository {
	const TABLE = 'amzFulfillment_inventory';

	public function __construct() {
		$create = "
				CREATE TABLE _TBL_ (
					`inventoryId` INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`sku`         VARCHAR(255) NOT NULL,
					`amazonStock` INT          NOT NULL DEFAULT 0,
					`updatedTime` DATETIME     NOT NULL,
					UNIQUE KEY (sku))";
		parent::__construct(SELF::TABLE, $create, TRUE);
	}

	/**
	 * @param string $sku
	 * @param integer $amazonStock
	 */
	public function set($sku, $amazonStock) {
		$this->setQuery(sprintf("DELETE FROM _TBL_ WHERE sku='%s'", esc_sql($sku)));
		$this->setQuery(sprintf("INSERT INTO _TBL_ (sku,amazonStock,updatedTime) VALUES('%s',%d,'%s')", esc_sql($sku), $amazonStock, date('Y-m-d H:i:s')));
	}

	/**
	 * @return AmzFulfillment_Entity_Inventory[]
	 */
	public function get() {
		$items = array();
		$results = $this->getQuery("SELECT sku,amazonStock,updatedTime FROM _TBL_ ORDER BY `sku`");
		foreach($results as $item) {
			$items[$item->sku] = new AmzFulfillment_Entity_Inventory($item->sku, $item->amazonStock, $item->updatedTime);
		}
		return $items;
	}

	public function getSkus() {
		return array_keys($this->get());
	}
}
