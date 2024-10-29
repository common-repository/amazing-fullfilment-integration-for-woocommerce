<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_APIConfig {
	private $sellerId;
	private $accessKeyId;
	private $secretAccessKeyId;
	private $countryCode;
	private $appName;
	private $appVersion;

	public function __construct() {
	}

	/**
	 * @return string
	 */
	public function getSellerId() {
		return $this->sellerId;
	}

	/**
	 * @param string $sellerId
	 */
	public function setSellerId($sellerId) {
		$this->sellerId = $sellerId;
	}

	/**
	 * @return string
	 */
	public function getAccessKeyId() {
		return $this->accessKeyId;
	}

	/**
	 * @param string $accessKeyId
	 */
	public function setAccessKeyId($accessKeyId) {
		$this->accessKeyId = $accessKeyId;
	}

	/**
	 * @return string
	 */
	public function getSecretAccessKeyId() {
		return $this->secretAccessKeyId;
	}

	/**
	 * @param string $secretAccessKeyId
	 */
	public function setSecretAccessKeyId($secretAccessKeyId) {
		$this->secretAccessKeyId = $secretAccessKeyId;
	}

	/**
	 * @return string
	 */
	public function getCountryCode() {
		return $this->countryCode;
	}

	/**
	 * @param string $countryCode
	 */
	public function setCountryCode($countryCode) {
		$this->countryCode = $countryCode;
	}

	/**
	 * @return string
	 */
	public function getEndpointUrl() {
		$endpoint = AmzFulfillment_Amazon_MWS_Endpoints::get($this->getRegionCode());
		return $endpoint['url'];
	}

	/**
	 * @return string
	 */
	public function getRegionCode() {
		$marketplace = $this->getMarketplace();
		return $marketplace['regionCode'];
	}

	/**
	 * @return array
	 */
	public function getMarketplace() {
		return AmzFulfillment_Amazon_Marketplaces::getByCountryCode($this->countryCode);
	}

	/**
	 * @return string
	 */
	public function getMarketplaceId() {
		$marketplace = $this->getMarketplace();
		return $marketplace['id'];
	}

	/**
	 * @return string[]
	 */
	public function getMarketplaceIds() {
		return array("Id" => array($this->getMarketplaceId()));
	}

	/**
	 * @return string
	 */
	public function getAppName() {
		return $this->appName;
	}

	/**
	 * @param string $appName
	 */
	public function setAppName($appName) {
		$this->appName = $appName;
	}

	/**
	 * @return string
	 */
	public function getAppVersion() {
		return $this->appVersion;
	}

	/**
	 * @param string $appVersion
	 */
	public function setAppVersion($appVersion) {
		$this->appVersion = $appVersion;
	}

	/**
	 * Max allowed id for Amazon				40
	 * Reserved for :<wooCommerceOrderId>		 7
	 * Max prefix length						33
	 */
	public static function getOrderPrefix() {
		$prefix = strtolower(get_site_url());
		$prefix = preg_replace('/^https?\:\/\//i', '', $prefix);
		$prefix = preg_replace('/:\d+/i', '', $prefix);
		if(strlen($prefix) > 33) {
			$prefix = substr($prefix, 0, 33);
		}
		return $prefix . ':';
	}
}
