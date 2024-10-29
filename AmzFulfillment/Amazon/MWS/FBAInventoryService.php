<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_FBAInventoryService extends AmzFulfillment_Amazon_MWS_Service {
	const PATH = '/FulfillmentInventory/2010-10-01';

	private $client = NULL;

	public function __construct($apiConfig) {
		parent::__construct($apiConfig, self::PATH);
	}

	/**
	 * @param string[] $sellerSkus
	 * @return FBAInventoryServiceMWS_Model_InventorySupply[]
	 */
	public function getItems($sellerSkus) {
		$items = array();
		$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
		$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
		$request->setSellerId($this->getApiConfig()->getSellerId());
		$skuList = new FBAInventoryServiceMWS_Model_SellerSkuList();
		$skuList->setmember($sellerSkus);
		$request->setSellerSkus($skuList);
		$response = $this->getClient()->ListInventorySupply($request);
		$result = $response->getListInventorySupplyResult();
		foreach($result->getInventorySupplyList()->getmember() as $supplyItem) {
			$items[] = $supplyItem;
		}
		while($result->isSetNextToken()) {
			$nextToken = $result->getNextToken();
			$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyByNextTokenRequest();
			$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
			$request->setSellerId($this->getApiConfig()->getSellerId());
			$request->setNextToken($nextToken);
			$response = $this->getClient()->ListInventorySupplyByNextToken($request);
			$result = $response->getListInventorySupplyByNextTokenResult();
			foreach($result->getInventorySupplyList()->getmember() as $supplyItem) {
				$items[] = $supplyItem;
			}
		}
		return $items;
	}

	/**
	 * @return FBAInventoryServiceMWS_Client
	 */
	protected function getClient() {
		if($this->client == null) {
			$this->client = new FBAInventoryServiceMWS_Client(
					$this->getApiConfig()->getAccessKeyId(),
					$this->getApiConfig()->getSecretAccessKeyId(),
					$this->getApiConfig()->getAppName(),
					$this->getApiConfig()->getAppVersion(),
					$this->getConfig());
		}
		return $this->client;
	}
}
