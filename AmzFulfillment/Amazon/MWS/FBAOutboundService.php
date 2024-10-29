<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_FBAOutboundService extends AmzFulfillment_Amazon_MWS_Service {
	const PATH = '/FulfillmentOutboundShipment/2010-10-01';
	const FULFILLMENT_POLICY = 'FillOrKill';

	private $client;

	public function __construct($apiConfig) {
		parent::__construct($apiConfig, self::PATH);
		$this->client = null;
	}

	/**
	 * @param string $queryStartTime
	 * @return FBAOutboundServiceMWS_Model_FulfillmentOrder[]
	 */
	public function listAllFulfillmentOrders($queryStartTime = NULL) {
		$orders = array();
		$request = new \FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersRequest();
		$request->setSellerId($this->getApiConfig()->getSellerId());
		if($queryStartTime !== NULL) {
			$request->setQueryStartDateTime($queryStartTime);
		}
		$response = $this->doListAllFulfillmentOrders($request);
		$result = $response->getListAllFulfillmentOrdersResult();
		foreach($result->getFulfillmentOrders()->getmember() as $order) {
			$orders[] = $order;
		}
		if($result->isSetNextToken()) {
			$nextToken = $result->getNextToken();
		} else {
			$nextToken = FALSE;
		}
		while($nextToken) {
			$request = new \FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenRequest();
			$request->setSellerId($this->getApiConfig()->getSellerId());
			$request->setNextToken($result->getNextToken());
			$response = $this->doListAllFulfillmentOrdersByNextToken($request);
			$result = $response->getListAllFulfillmentOrdersByNextTokenResult();
			if($result->isSetNextToken()) {
				$nextToken = $result->getNextToken();
			} else {
				$nextToken = FALSE;
			}
			foreach($result->getFulfillmentOrders()->getmember() as $order) {
				$orders[] = $order;
			}
			sleep(1);
		}
		return $orders;
	}

	/**
	 * @param string $sellerFulfillmentOrderId
	 * @return GetFulfillmentOrderResult
	 */
	public function getOrder($sellerFulfillmentOrderId) {
		$request = new \FBAOutboundServiceMWS_Model_GetFulfillmentOrderRequest();
		$request->setSellerId($this->getApiConfig()->getSellerId());
		$request->setSellerFulfillmentOrderId($sellerFulfillmentOrderId);
		$response = $this->doGetFulfillmentOrder($request);
		$result = $response->getGetFulfillmentOrderResult();
		return $result;
	}

	/**
	 * @param string $packageNumber
	 * @return GetPackageTrackingDetailsResult
	 */
	public function getPackageTrackingDetails($packageNumber) {
		$request = new FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsRequest();
		$request->setSellerId($this->getApiConfig()->getSellerId());
		$request->setPackageNumber($packageNumber);
		$response = $this->getClient()->getPackageTrackingDetails($request);
		return $response->getGetPackageTrackingDetailsResult();
	}

	/**
	 * @param string $orderId
	 * @param FBAOutboundServiceMWS_Model_Address $address
	 * @param FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItemList $itemsList
	 * @param string $notes
	 * @param boolean hold
	 * @return FBAOutboundServiceMWS_Model_CreateFulfillmentOrderResponse
	 */
	public function createFulfillmentOrder($orderId, $address, $itemsList, $hold, $notes = '') {
		$request = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderRequest();
		$request->setSellerId($this->getApiConfig()->getSellerId());
		$request->setShippingSpeedCategory("Standard");
		$request->setDestinationAddress($address);
		$request->setItems($itemsList);
		$request->setSellerFulfillmentOrderId(AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix() . $orderId);
		$request->setDisplayableOrderId(AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix() . $orderId);
		$request->setDisplayableOrderDateTime(date("c"));
		if(!empty($notes)) {
			$request->setDisplayableOrderComment("Order Notes " . $notes);
		} else {
			$request->setDisplayableOrderComment("No notes");
		}
		if($hold) {
			$request->setFulfillmentAction("Hold");
		} else {
			$request->setFulfillmentAction("Ship");
		}
		$request->setFulfillmentPolicy(self::FULFILLMENT_POLICY);
		return $this->getClient()->CreateFulfillmentOrder($request);
	}

	/**
	 * @param string $orderId
	 * @return FBAOutboundServiceMWS_Model_CancelFulfillmentOrderResponse
	 */
	public function cancelFulfillmentOrder($orderId) {
		$request = new FBAOutboundServiceMWS_Model_CancelFulfillmentOrderRequest();
		$request->setSellerId($this->getApiConfig()->getSellerId());
		$request->setSellerFulfillmentOrderId(AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix() . $orderId);
		return $this->getClient()->CancelFulfillmentOrder($request);
	}

	/**
	 * @param int $orderId
	 * @return GetFulfillmentOrderResult
	 */
	public function getFulfillmentOrder($orderId) {
		try {
			$request = new FBAOutboundServiceMWS_Model_GetFulfillmentOrderRequest();
			$request->setMarketplace($this->getApiConfig()->getMarketplaceId());
			$request->setSellerId($this->getApiConfig()->getSellerId());
			$request->setSellerFulfillmentOrderId(AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix() . $orderId);
			$response = $this->doGetFulfillmentOrder($request);
			return $response->getGetFulfillmentOrderResult();
		} catch(Exception $e) {
			AmzFulfillment_Logger::error($e);
			throw $e;
		}
	}

	/**
	 * @param string $orderId
	 * @return string
	 */
	public static function getOrderIdWithoutPrefix($orderId) {
		return str_replace(AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix(), '', $orderId);
	}

	/**
	 * @param string $orderId
	 * @return boolean
	 */
	public static function containsPrefix($orderId) {
		return strpos($orderId, AmzFulfillment_Amazon_MWS_APIConfig::getOrderPrefix()) === 0;
	}

	/**
	 * @return \FBAOutboundServiceMWS_Client
	 */
	protected function getClient() {
		if($this->client == null) {
			$this->client = new \FBAOutboundServiceMWS_Client(
					$this->getApiConfig()->getAccessKeyId(),
					$this->getApiConfig()->getSecretAccessKeyId(),
					$this->getConfig(),
					$this->getApiConfig()->getAppName(),
					$this->getApiConfig()->getAppVersion());
		}
		return $this->client;
	}

	private function doGetFulfillmentOrder($request) {
		for($try = 0; $try < AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES; $try++) {
			$response = null;
			try{
				return $this->getClient()->getFulfillmentOrder($request);
			} catch(Exception $e) {
				if($e->getMessage() == "Request is throttled") {
					AmzFulfillment_Logger::warn(sprintf("getFulfillmentOrder failed (try %d / %d): %s", $try + 1, AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES, $e->getMessage()));
					sleep(AmzFulfillment_Amazon_MWS_Service::THROTTLED_WAIT);
				} else { 
					throw $e;
				}
			}
		}
		throw new Exception(sprintf("Failed to access amazon MWS api: Give up getFulfillmentOrder after %d tries". $try + 1));
	}

	private function doListAllFulfillmentOrders($request) {
		for($try = 0; $try < AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES; $try++) {
			$response = null;
			try{
				return $this->getClient()->listAllFulfillmentOrders($request);
			} catch(Exception $e) {
				if($e->getMessage() == "Request is throttled") {
					AmzFulfillment_Logger::warn(sprintf("listAllFulfillmentOrders failed (try %d / %d): %s", $try + 1, AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES, $e->getMessage()));
					sleep(AmzFulfillment_Amazon_MWS_Service::THROTTLED_WAIT);
				} else {
					throw $e;
				}
			}
		}
		throw new Exception(sprintf("Failed to access amazon MWS api: Give up listAllFulfillmentOrders after %d tries". $try + 1));
	}

	private function doListAllFulfillmentOrdersByNextToken($request) {
		for($try = 0; $try < AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES; $try++) {
			$response = null;
			try{
				return $this->getClient()->listAllFulfillmentOrdersByNextToken($request);
			} catch(Exception $e) {
				if($e->getMessage() == "Request is throttled") {
					AmzFulfillment_Logger::warn(sprintf("listAllFulfillmentOrdersByNextToken failed (try %d / %d): %s", $try + 1, AmzFulfillment_Amazon_MWS_Service::THROTTLED_RETRIES, $e->getMessage()));
					sleep(AmzFulfillment_Amazon_MWS_Service::THROTTLED_WAIT);
				} else {
					throw $e;
				}
			}
		}
		throw new Exception(sprintf("Failed to access amazon MWS api: Give up listAllFulfillmentOrdersByNextToken after %d tries". $try + 1));
	}
}
