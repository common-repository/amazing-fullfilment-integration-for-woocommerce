<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_FulfillmentController {
	private $fulfillmentRepo;
	private $wooCommerce;
	private $mws;

	public function __construct() {
		$this->fulfillmentRepo = AmzFulfillment_Main::instance()->data()->fulfillments();
		$this->wooCommerce = AmzFulfillment_Main::instance()->wooCommerce();
		$this->mws = AmzFulfillment_Main::instance()->amazonMWS();
	}

	/**
	 * @param integer $orderId
	 */
	public function create($orderId) {
		try {
			$order = $this->wooCommerce->orders()->get($orderId);
			$address = AmzFulfillment_Amazon_MWS_Client::getAddress(
					$order->get_shipping_first_name(), $order->get_shipping_last_name(), $order->get_shipping_company(), $order->get_shipping_city(),
					$order->get_shipping_country(), $order->get_shipping_address_1(), $order->get_shipping_address_2(), $order->get_billing_phone(),
					$order->get_shipping_postcode(), $order->get_shipping_state());
			$amazonItems = array();
			foreach($order->get_items() as $orderItem) {
				if(isset($orderItem['variation_id']) && !empty($orderItem['variation_id'])) {
					$item = new WC_Product_Variation($orderItem['variation_id']);
				} else {
					$item = new WC_Product($orderItem['product_id']);
				}
				$sku = $item->get_sku();
				$amazonItem = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItem();
				$amazonItem->setPerUnitDeclaredValue(AmzFulfillment_Amazon_MWS_Client::getPrice($item->get_price(), $this->wooCommerce->getCurrency()));
				$amazonItem->setQuantity($orderItem['qty']);
				$amazonItem->setSellerFulfillmentOrderItemId($order->get_id(). "-" . $sku);
				$amazonItem->setSellerSKU($sku);
				$amazonItems[] = $amazonItem;
			}
			$amazonItemList = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItemList();
			$amazonItemList->setmember($amazonItems);
			$hold = AmzFulfillment_Main::instance()->data()->options()->options()->getHold();
			$this->mws->fbaOutbound()->createFulfillmentOrder($order->get_id(), $address, $amazonItemList, $hold, 'WooCommerce order');
			$this->fulfillmentRepo->set(new AmzFulfillment_Entity_Fulfillment($orderId));
			AmzFulfillment_Logger::info(sprintf('WooCommerceOrder-%d successfully created amazon fulfillment', $orderId));
			AmzFulfillment_Main::instance()->wooCommerce()->orders()->note($orderId, "Create Amazon fulfillment: Succeeded");
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d failed to created amazon fulfillment: %s', $orderId, $e->getMessage()));
			AmzFulfillment_Main::instance()->wooCommerce()->orders()->note($orderId, "Create Amazon fulfillment: Failed (" . $e->getMessage() . ")");
		}
	}

	/**
	 * @param integer $orderId
	 */
	public function cancel($orderId) {
		try {
			$this->mws->fbaOutbound()->cancelFulfillmentOrder($orderId);
			AmzFulfillment_Logger::info(sprintf('WooCommerceOrder-%d successfully cancelled amazon fulfillment', $orderId));
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d failed to cancel amazon fulfillment: %s', $orderId, $e->getMessage()));
		}
	}

	/**
	 * @param integer $orderId
	 * @param string $status
	 */
	public function update($orderId, $status) {
		$fulfillment = NULL;
		if(!$this->fulfillmentRepo->exist($orderId)) {
			$fulfillment = new AmzFulfillment_Entity_Fulfillment($orderId);
			$this->fulfillmentRepo->set($fulfillment);
			AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d found existing Amazon fulfillment", $orderId));
		} else {
			$fulfillment = $this->fulfillmentRepo->get($orderId);
		}
		if($fulfillment->getStatus() !== $status) {
			$fulfillment->setStatus($status);
			$this->fulfillmentRepo->set($fulfillment);
			AmzFulfillment_Logger::info(sprintf("WooCommerceOrder-%d Amazon fulfillment status changed to '%s'", $orderId, $status));
			switch($status) {
				case "RECEIVED":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_RECEIVED);
					break;
				case "INVALID":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_INVALID);
					break;
				case "PLANNING":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_PLANNING);
					break;
				case "PROCESSING":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_PROCESSING);
					break;
				case "CANCELLED":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_CANCELLED);
					break;
				case "COMPLETE":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_COMPLETE);
					break;
				case "COMPLETE_PARTIALLED":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_COMPLETE_PARTIALLED);
					break;
				case "UNFULFILLABLE":
					AmzFulfillment_Main::instance()->eventController()->amazonStatusChanged($orderId, AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_UNFULFILLABLE);
					break;
				default:
					AmzFulfillment_Logger::error(sprintf("WooCommerceOrder-%d Amazon fulfillment status changed to unexpected '%s'", $orderId, $status));
					break;
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
