<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Options {
	private $accessKeyId = '';
	private $secretAccessKeyId = '';
	private $marketplace = '';
	private $merchantId = '';
	private $hold = FALSE;
	private $syncSkus = array();
	private $schedulingInterval = 60 * 60;
	private $automation = FALSE;
	private $licenseKey = NULL;
	private $licenseEmail = NULL;
	private $licenseExpireTime = NULL;
	private $licenseCheckTime = NULL;
	private $licenseValid = FALSE;
	private $licenseInstance = 0;

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
	 * @param string|NULL $secretAccessKeyId
	 */
	public function setSecretAccessKeyId($secretAccessKeyId) {
		$this->secretAccessKeyId = $secretAccessKeyId;
	}

	/**
	 * @return string
	 */
	public function getMarketplace() {
		return $this->marketplace;
	}

	/**
	 * @param string $marketplace
	 */
	public function setMarketplace($marketplace) {
		$this->marketplace = $marketplace;
	}

	/**
	 * @return string
	 */
	public function getMerchantId() {
		return $this->merchantId;
	}

	/**
	 * @param string $merchantId
	 */
	public function setMerchantId($merchantId) {
		$this->merchantId = $merchantId;
	}

	/**
	 * @return boolean
	 */
	public function getHold() {
		return $this->hold;
	}

	/**
	 * @param boolean $hold
	 */
	public function setHold($hold) {
		$this->hold = (boolean) $hold;
	}

	/**
	 * @return array:
	 */
	public function getSyncSkus() {
		return $this->syncSkus;
	}

	/**
	 * @param string $sku
	 * @return boolean
	 */
	public function isSyncSku($sku) {
		return in_array($sku, $this->syncSkus);
	}

	/**
	 * @param array: $syncSkus
	 */
	public function setSyncSkus($syncSkus) {
		$this->syncSkus = $syncSkus;
	}

	/**
	 * @return number
	 */
	public function getSchedulingInterval() {
		return $this->schedulingInterval;
	}

	/**
	 * @param number $schedulingInterval
	 */
	public function setSchedulingInterval($schedulingInterval) {
		$this->schedulingInterval = (int) $schedulingInterval;
	}

	/**
	 * @return boolean
	 */
	public function getAutomation() {
		return $this->automation;
	}

	/**
	 * @param boolean $automation
	 */
	public function setAutomation($automation) {
		$this->automation = (bool) $automation;
	}

	/**
	 * @return string
	 */
	public function getLicenseKey() {
		return $this->licenseKey;
	}

	/**
	 * @param string $licenseKey
	 */
	public function setLicenseKey($licenseKey) {
		$this->licenseKey = $licenseKey;
	}

	/**
	 * @return string
	 */
	public function getLicenseEmail() {
		return $this->licenseEmail;
	}

	/**
	 * @param string $licenseEmail
	 */
	public function setLicenseEmail($licenseEmail) {
		$this->licenseEmail = $licenseEmail;
	}

	/**
	 * @return int
	 */
	public function getLicenseExpireTime() {
		return $this->licenseExpireTime;
	}

	/**
	 * @param int $licenseExpireTime
	 */
	public function setLicenseExpireTime($licenseExpireTime) {
		$this->licenseExpireTime = (int) $licenseExpireTime;
	}

	/**
	 * @return int
	 */
	public function getLicenseCheckTime() {
		return $this->licenseCheckTime;
	}

	/**
	 * @param int $licenseCheckTime
	 */
	public function setLicenseCheckTime($licenseCheckTime) {
		$this->licenseCheckTime = (int) $licenseCheckTime;
	}

	/**
	 * @return boolean
	 */
	public function getLicenseValid() {
		return $this->licenseValid;
	}

	/**
	 * @param boolean $licenseCheckTime
	 */
	public function setLicenseValid($licenseValid) {
		$this->licenseValid = (boolean) $licenseValid;
	}

	/**
	 * @return integer
	 */
	public function getLicenseInstance() {
		return $this->licenseInstance;
	}

	/**
	 * @param boolean $licenseInstance
	 */
	public function setLicenseInstance($licenseInstance) {
		$this->licenseInstance = (integer) $licenseInstance;
	}

	/**
	 * @return boolean
	 */
	public function hasAmazonCredentials() {
		return !empty($this->accessKeyId) && !empty($this->secretAccessKeyId) && !empty($this->marketplace) && !empty($this->merchantId);
	}

	public function getAsArray() {
		return get_object_vars($this);
	}

	public function setByArray($array) {
		foreach(array_keys(get_class_vars(__CLASS__)) as $key) {
			if(isset($array[$key])) {
				$this->$key = $array[$key];
			}
		}
	}
}
