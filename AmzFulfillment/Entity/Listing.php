<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Listing {
	private $sku = NULL;
	private $asin = NULL;
	private $name = NULL;

	public function __construct($sku = NULL, $asin = NULL, $name = NULL) {
		$this->sku = $sku;
		$this->asin = $asin;
		$this->name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * @param string|null $sku
	 */
	public function setSku($sku) {
		$this->sku = $sku;
	}

	/**
	 * @return string|null
	 */
	public function getAsin() {
		return $this->asin;
	}

	/**
	 * @param string|null $asin
	 */
	public function setAsin($asin) {
		$this->asin = $asin;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string|null $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	public static function createInstance($obj) {
		if(!is_object($obj)) {
			return NULL;
		}
		return new self($obj->sku, $obj->asin, $obj->name);
	}
}
