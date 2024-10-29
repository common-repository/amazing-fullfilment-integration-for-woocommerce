<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_MarketplaceWebService extends AmzFulfillment_Amazon_MWS_Service {
	const PATH = '';

	private $client;

	public function __construct($apiConfig) {
		parent::__construct($apiConfig, self::PATH);
		$this->client = null;
	}

	/**
	 * @param string $type
	 * @return MarketplaceWebService_Model_RequestReportResult
	 */
	public function requestReport($type) {
		$request = new MarketplaceWebService_Model_RequestReportRequest();
		$request->setMerchant($this->getApiConfig()->getSellerId());
		$request->setMarketplaceIdList($this->getApiConfig()->getMarketplaceIds());
		$request->setReportType($type);
		$response = $this->getClient()->requestReport($request);
		return $response->getRequestReportResult();
	}

	/**
	 * @param string $type
	 * @return MarketplaceWebService_Model_GetReportRequestListResult
	 */
	public function getReportRequestList($type = null) {
		$request = new MarketplaceWebService_Model_GetReportRequestListRequest();
		$request->setMerchant($this->getApiConfig()->getSellerId());
		$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
		if($type !== null) {
			$tl = new MarketplaceWebService_Model_TypeList();
			$tl->setType($type);
			$request->setReportTypeList($tl);
		}
		$response = $this->getClient()->getReportRequestList($request);
		return $response->getGetReportRequestListResult();
	}

	/**
	 * @param string $type
	 * @return MarketplaceWebService_Model_GetReportListResult
	 */
	public function getReportList($type = null) {
		$request = new MarketplaceWebService_Model_GetReportListRequest();
		$request->setMerchant($this->getApiConfig()->getSellerId());
		$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
		if($type !== null) {
			$tl = new MarketplaceWebService_Model_TypeList();
			$tl->setType($type);
			$request->setReportTypeList($tl);
		}
		$request->setAcknowledged(false);
		$response = $this->getClient()->getReportList($request);
		return $response->getGetReportListResult();
	}

	/**
	 * Report row fields:
	 *  [0] => item-name
	 *  [1] => item-description
	 *  [2] => listing-id
	 *  [3] => seller-sku
	 *  [4] => price
	 *  [5] => quantity
	 *  [6] => open-date
	 *  [7] => image-url
	 *  [8] => item-is-marketplace
	 *  [9] => product-id-type
	 * [10] => zshop-shipping-fee
	 * [11] => item-note
	 * [12] => item-condition
	 * [13] => zshop-category1
	 * [14] => zshop-browse-path
	 * [15] => zshop-storefront-feature
	 * [16] => asin1
	 * [17] => asin2
	 * [18] => asin3
	 * [19] => will-ship-internationally
	 * [20] => expedited-shipping
	 * [21] => zshop-boldface
	 * [22] => product-id
	 * [23] => bid-for-featured-placement
	 * [24] => add-delete
	 * [25] => pending-quantity
	 * [26] => fulfillment-channel
	 * [27] => optional-payment-type-exclusion
	 * [28] => merchant-shipping-group
	 *
	 * @param string $reportId
	 * @return array[]
	 */
	public function getReport($reportId) {
		$file = tmpfile();
		$request = new MarketplaceWebService_Model_GetReportRequest();
		$request->setMerchant($this->getApiConfig()->getSellerId());
		$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
		$request->setReportId($reportId);
		$request->setReport($file);
		$response = $this->getClient()->getReport($request);
		fseek($file, 0);
		$report = array();
		while(($row = fgetcsv($file, 0, "\t")) !== false) {
			$report[] = $row;
		}
		fclose($file);
		return $report;
	}

	/**
	 * @return MarketplaceWebService_Client
	 */
	protected function getClient() {
		if($this->client == null) {
			$this->client = new \MarketplaceWebService_Client(
					$this->getApiConfig()->getAccessKeyId(),
					$this->getApiConfig()->getSecretAccessKeyId(),
					$this->getConfig(),
					$this->getApiConfig()->getAppName(),
					$this->getApiConfig()->getAppVersion());
		}
		return $this->client;
	}
}
