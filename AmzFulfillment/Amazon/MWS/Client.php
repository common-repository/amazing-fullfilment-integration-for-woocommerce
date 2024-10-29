<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_Client {
	private $apiConfig;
	private $fbaInventory = NULL;
	private $fbaOutbound = NULL;
	private $marketplace = NULL;

	public function __construct($sellerId, $accessKeyId, $secretAccessKeyId, $countryCode, $appName, $appVersion) {
		$this->apiConfig = new AmzFulfillment_Amazon_MWS_APIConfig();
		$this->apiConfig->setSellerId($sellerId);
		$this->apiConfig->setAccessKeyId($accessKeyId);
		$this->apiConfig->setSecretAccessKeyId($secretAccessKeyId);
		$this->apiConfig->setCountryCode($countryCode);
		$this->apiConfig->setAppName($appName);
		$this->apiConfig->setAppVersion($appVersion);
	}

	/**
	 * @return AmzFulfillment_Amazon_MWS_FBAInventoryService
	 */
	public function fbaInventory() {
		if($this->fbaInventory === NULL) {
			$this->fbaInventory = new AmzFulfillment_Amazon_MWS_FBAInventoryService($this->apiConfig);
		}
		return $this->fbaInventory;
	}

	/**
	 * @return AmzFulfillment_Amazon_MWS_FBAOutboundService
	 */
	public function fbaOutbound() {
		if($this->fbaOutbound === NULL) {
			$this->fbaOutbound = new AmzFulfillment_Amazon_MWS_FBAOutboundService($this->apiConfig);
		}
		return $this->fbaOutbound;
	}

	/**
	 * @return AmzFulfillment_Amazon_MWS_MarketplaceWebService
	 */
	public function marketplace() {
		if($this->marketplace === NULL) {
			$this->marketplace = new AmzFulfillment_Amazon_MWS_MarketplaceWebService($this->apiConfig);
		}
		return $this->marketplace;
	}

	/**
	 * @param string $value
	 * @param string $currencyCode
	 * @return FBAOutboundServiceMWS_Model_Currency
	 */
	public static function getPrice($value, $currencyCode) {
		$currency = new FBAOutboundServiceMWS_Model_Currency();
		$currency->setCurrencyCode($currencyCode);
		$currency->setValue($value);
		return $currency;
	}

	public static function getAddress($firstname, $lastname, $company, $city, $country, $address1, $address2, $phone, $postalCode, $state) {
		$address = new FBAOutboundServiceMWS_Model_Address();
		$address->setName(trim($firstname . ' ' . $lastname . ' ' . $company));
		$address->setCity($city);
		$address->setCountryCode($country);
		$address->setLine1($address1);
		$address->setLine2($address2);
		$address->setPhoneNumber($phone);
		$address->setPostalCode($postalCode);
		$address->setStateOrProvinceCode($state);
		return $address;
	}
}
