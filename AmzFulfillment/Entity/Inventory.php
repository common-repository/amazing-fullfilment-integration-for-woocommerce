<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Inventory {
	private $sku;
	private $amazonStock;
	private $updatedTime;

	/**
	 * @param string $sku
	 * @param integer $amazonStock
	 * @param string $updatedTime
	 */
	public function __construct($sku, $amazonStock, $updatedTime) {
		$this->sku = $sku;
		$this->amazonStock = $amazonStock;
		$this->updatedTime = $updatedTime;
	}

	/**
	 * @return string
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * @return integer
	 */
	public function getAmazonStock() {
		return $this->amazonStock;
	}

	/**
	 * @return string
	 */
	public function getUpdatedTime() {
		return $this->updatedTime;
	}
}
