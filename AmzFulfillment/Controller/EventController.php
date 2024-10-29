<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_EventController {
	public function __construct() {
	}

	/**
	 * @param integer $orderId
	 * @param AmzFulfillment_Entity_Event $event
	 */
	public function amazonStatusChanged($orderId, $status) {
		switch($status) {
			case 'RECEIVED':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_RECEIVED);
				break;
			case 'INVALID':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_INVALID);
				break;
			case 'PLANNING':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_PLANNING);
				break;
			case 'PROCESSING':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_PROCESSING);
				break;
			case 'CANCELLED':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_CANCELLED);
				break;
			case 'COMPLETE':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_COMPLETE);
				break;
			case 'COMPLETE_PARTIALLED':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_COMPLETE_PARTIALLED);
				break;
			case 'UNFULFILLABLE':
				$this->onEvent(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_UNFULFILLABLE);
				break;
			default:
				AmzFulfillment_Logger::warn(sprintf('WooCommerceOrder-%d Amazon fulfillment changed to unexpected status: %s', $orderId, $status));
				return;
		}
		$eventName = $event = AmzFulfillment_Entity_Event::instance($status)->getName();
		AmzFulfillment_Main::instance()->wooCommerce()->orders()->note($orderId, "Amazon " . $eventName);
	}

	public function amazonPackageChanged($orderId, $packageNumber, $status) {
		switch($status) {
			case AmzFulfillment_Entity_Event::AMAZON_PACKAGE_TRACKING_NUMBER:
				$this->onEvent(AmzFulfillment_Entity_Event::instance(AmzFulfillment_Entity_Event::AMAZON_PACKAGE_TRACKING_NUMBER), $orderId, $packageNumber);
				break;
			default:
				AmzFulfillment_Logger::warn(sprintf('WooCommerceOrder-%d Amazon fulfillment package-%d changed to unexpected status: %s', $orderId, $packageNumber, $status));
				return;
		}
		$eventName = $event = AmzFulfillment_Entity_Event::instance($status)->getName();
		AmzFulfillment_Main::instance()->wooCommerce()->orders()->note($orderId, sprintf("Amazon fulfillment package %d %s", $packageNumber, $eventName));
	}

	/**
	 * @param integer $orderId
	 */
	public function woocommerceOrderStatusChange($orderId) {
		$order = AmzFulfillment_Main::instance()->wooCommerce()->orders()->get($orderId);
		switch($order->get_status()) {
			case AmzFulfillment_WooCommerce_Status::PENDING:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_PENDING, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::PROCESSING:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_PROCESSING, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::ONHOLD:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_ONHOLD, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::COMPLETED:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_COMPLETED, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::CANCELLED:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_CANCELLED, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::REFUNDED:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_REFUNDED, $orderId);
				break;
			case AmzFulfillment_WooCommerce_Status::FAILED:
				$this->onEvent(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_FAILED, $orderId);
				break;
			default:
				AmzFulfillment_Logger::warn(sprintf('WooCommerceOrder-%d changed to unexpected status: %s', $orderId, $status));
				return;
		}
	}

	/**
	 * @param AmzFulfillment_Entity_Event $event
	 * @param integer $orderId
	 */
	private function onEvent($event, $orderId, $packageNumber = NULL) {
		if(!AmzFulfillment_Main::instance()->data()->options()->get()->getAutomation()) {
			return;
		}
		if(is_string($event)) {
			$event = AmzFulfillment_Entity_Event::instance($event);
		}
		$package = "";
		if($packageNumber !== NULL) {
			$package = sprintf(" package-%d", $packageNumber);
		}
		$note = trim($event->getName() . $package);
		$msg = sprintf("WooCommerceOrder-%d %s", $orderId, $note);
		AmzFulfillment_Logger::debug($msg);
		if(stripos($event->getId(), 'wc') === 0) {
			AmzFulfillment_Main::instance()->wooCommerce()->orders()->note($orderId, $note);
		}
		if(AmzFulfillment_Main::instance()->featureController()->hasAutomation()) {
			foreach($this->getActions($event) as $action) {
				AmzFulfillment_Logger::debug(sprintf("WooCommerceOrder-%d triggered action='%s' event='%s' ", $orderId, $action->getId(), $event->getId()));
				$this->doAction($event, $action, $orderId, $packageNumber);
			}
		}
	}

	/**
	 * 
	 * @param AmzFulfillment_Entity_Event $event
	 * @param AmzFulfillment_Entity_Action $action
	 * @param integer $orderId
	 */
	private function doAction($event, $action, $orderId, $packageNumber = NULL) {
		try {
			switch($action->getId()) {
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_PENDING:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::PENDING);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_PROCESSING:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::PROCESSING);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_ONHOLD:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::ONHOLD);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_COMPLETED:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::COMPLETED);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_CANCELLED:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::CANCELLED);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_REFUNDED:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::REFOUNDED);
					break;
				case AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_FAILED:
					AmzFulfillment_Main::instance()->wooCommerce()->orders()->setStatus($orderId, AmzFulfillment_WooCommerce_Status::FAILED);
					break;
				case AmzFulfillment_Entity_Action::AMAZON_FULFILLMENT_CREATE:
					AmzFulfillment_Main::instance()->fulfillmentController()->create($orderId);
					break;
				case AmzFulfillment_Entity_Action::AMAZON_FULFILLMENT_CANCEL:
					AmzFulfillment_Main::instance()->fulfillmentController()->cancel($orderId);
					break;
				case AmzFulfillment_Entity_Action::EMAIL_ADMIN:
					AmzFulfillment_Main::instance()->sendNotification('Order notification', sprintf('WooCommerceOrder-%d has new status: %s', $orderId, $event->getName()));
					break;
				case AmzFulfillment_Entity_Action::EMAIL_PACKAGE_TRACKING:
					AmzFulfillment_Main::instance()->wooCommerce()->getPackageTrackingEmail()->trigger($orderId, $packageNumber);
					break;
			}
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf("WooCommerceOrder-%d action '%s' failed: %s", $orderId, $action->getId(), $e->getMessage()));
		}
	}

	/**
	 * @param AmzFulfillment_Entity_Event $event
	 * @return AmzFulfillment_Entity_Action[]
	 */
	private function getActions($event) {
		$actions = array();
		foreach(AmzFulfillment_Main::instance()->data()->rules()->get() as $rule) {
			if($rule->getEvent()->getId() == $event->getId()) {
				$actions[] = $rule->getAction();
			}
		}
		return $actions;
	}
}
