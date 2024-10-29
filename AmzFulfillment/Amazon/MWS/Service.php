<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
abstract class AmzFulfillment_Amazon_MWS_Service {
	const THROTTLED_RETRIES = 10;
	const THROTTLED_WAIT = 10;
	private $servicePath;
	private $apiConfig;

	/**
	 * @param AmzFulfillment_Amazon_MWS_APIConfig $apiConfig
	 * @param string $servicePath
	 */
	public function __construct($apiConfig, $servicePath) {
		$this->servicePath = $servicePath;
		$this->apiConfig = $apiConfig;
	}

	protected abstract function getClient();

	/**
	 * @return AmzFulfillment_Amazon_MWS_APIConfig
	 */
	protected function getApiConfig() {
		return $this->apiConfig;
	}

	protected function getConfig() {
		return array (
				'ServiceURL' => $this->apiConfig->getEndpointUrl() . $this->servicePath,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'ProxyUsername' => null,
				'ProxyPassword' => null,
				'MaxErrorRetry' => 3);
	}
}
