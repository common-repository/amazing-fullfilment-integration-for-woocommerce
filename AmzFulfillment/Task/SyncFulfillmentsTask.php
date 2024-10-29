<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Task_SyncFulfillmentsTask extends AmzFulfillment_Task_Task {
	const FULFILLMENT_TIME = 30 * 24 * HOUR_IN_SECONDS;

	protected function run() {
		$mws = AmzFulfillment_Main::instance()->amazonMWS();
		$fulfillments = AmzFulfillment_Main::instance()->fulfillmentController();
		if(!AmzFulfillment_Main::instance()->featureController()->hasFulfillments()) {
			AmzFulfillment_Logger::debug(__CLASS__ . " - Fulfillment sync skipped: Missing feature");
			return;
		}
		$countTotal = 0;
		$countUpdate = 0;
		$date = AmzFulfillment_Amazon_Date::toXmlDate(time() - self::FULFILLMENT_TIME);
		foreach ($mws->fbaOutbound()->listAllFulfillmentOrders($date) as $order) {
			$countTotal++;
			try {
				$prefixedOrderId = $order->getDisplayableOrderId();
				if(!AmzFulfillment_Amazon_MWS_FBAOutboundService::containsPrefix($prefixedOrderId)) {
					continue;
				}
				$orderId = AmzFulfillment_Amazon_MWS_FBAOutboundService::getOrderIdWithoutPrefix($prefixedOrderId);
				if(!AmzFulfillment_Main::instance()->wooCommerce()->orders()->exist($orderId)) {
					AmzFulfillment_Logger::debug(sprintf('%s - Ignoring Amazon fulfillment for non existent WooCommerceOrder-%d', __CLASS__, $orderId));
					continue;
				}
				AmzFulfillment_Logger::debug(sprintf("%s - WooCommerceOrder-%d processing", __CLASS__, $orderId));
				$status = $order->getFulfillmentOrderStatus();
				if($fulfillments->update($orderId, $status)) {
					$countUpdate++;
				}
			} catch(Exception $e) {
				AmzFulfillment_Logger::error(sprintf("%s - WooCommerceOrder-%d failed to sync fulfillment: %s", __CLASS__, $orderId, $e->getMessage()));
			}
		}
		AmzFulfillment_Logger::debug(sprintf("%s - %d of %d fulfillments updated", __CLASS__, $countUpdate, $countTotal));
	}
}
