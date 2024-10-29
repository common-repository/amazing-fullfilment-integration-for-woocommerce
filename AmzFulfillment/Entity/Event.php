<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2017 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Event {
	const WOOCOMMERCE_ORDER_PENDING					= 'wcOrderPending';
	const WOOCOMMERCE_ORDER_PROCESSING				= 'wcOrderProcessing';
	const WOOCOMMERCE_ORDER_ONHOLD					= 'wcOrderOnHold';
	const WOOCOMMERCE_ORDER_COMPLETED				= 'wcOrderCompleted';
	const WOOCOMMERCE_ORDER_CANCELLED				= 'wcOrderCancelled';
	const WOOCOMMERCE_ORDER_REFUNDED 				= 'wcOrderRefunded';
	const WOOCOMMERCE_ORDER_FAILED					= 'wcOrderFailed';
	const AMAZON_FULFILLMENT_RECEIVED				= 'amFulfillmentReceived';
	const AMAZON_FULFILLMENT_INVALID				= 'amFulfillmentInvalid';
	const AMAZON_FULFILLMENT_PLANNING				= 'amFulfillmentPlanning';
	const AMAZON_FULFILLMENT_PROCESSING				= 'amFulfillmentProcessing';
	const AMAZON_FULFILLMENT_CANCELLED				= 'amFulfillmentCancelled';
	const AMAZON_FULFILLMENT_COMPLETE 				= 'amFulfillmentComplete';
	const AMAZON_FULFILLMENT_COMPLETE_PARTIALLED	= 'amFulfillmentCompletePartialled';
	const AMAZON_FULFILLMENT_UNFULFILLABLE			= 'amFulfillmentUnfillable';
	const AMAZON_PACKAGE_TRACKING_NUMBER			= 'amPackageTrackingNumber';

	private static $values = NULL;

	/**
	 * @param stringn $eventId
	 * @throws InvalidArgumentException
	 * @return AmzFulfillment_Entity_Event
	 */
	public static function instance($eventId) {
		foreach(self::values() as $event) {
			if($event->getId() == $eventId) {
				return $event;
			}
		}
		throw new InvalidArgumentException("Unknown event: " . $eventId);
	}

	/**
	 * @return AmzFulfillment_Entity_Event[]
	 */
	public static function values() {
		if(self::$values === NULL) {
			self::$values = array(
					new self(self::WOOCOMMERCE_ORDER_PENDING,
							'WooCommerce',
							'WooCommerce order pending',
							'WooCommerce order status changed to pending'),
					new self(self::WOOCOMMERCE_ORDER_PROCESSING,
							'WooCommerce',
							'WooCommerce order processing',
							'WooCommerce order status changed to processing'),
					new self(self::WOOCOMMERCE_ORDER_ONHOLD,
							'WooCommerce',
							'WooCommerce order on-hold',
							'WooCommerce order status changed to on hold'),
					new self(self::WOOCOMMERCE_ORDER_COMPLETED,
							'WooCommerce',
							'WooCommerce order completed',
							'WooCommerce order status changed to completed'),
					new self(self::WOOCOMMERCE_ORDER_CANCELLED,
							'WooCommerce',
							'WooCommerce order cancelled',
							'WooCommerce order status changed to cancelled'),
					new self(self::WOOCOMMERCE_ORDER_REFUNDED,
							'WooCommerce',
							'WooCommerce order refunded',
							'WooCommerce order status changed to refounded'),
					new self(self::WOOCOMMERCE_ORDER_FAILED,
							'WooCommerce',
							'WooCommerce order failed',
							'WooCommerce order status changed to failed'),
					new self(self::AMAZON_FULFILLMENT_RECEIVED,
							'Amazon',
							'fulfillment received',
							'The fulfillment order was received by Amazon Marketplace Web Service (Amazon MWS) and validated. Validation includes determining that the destination address is valid and that Amazon s records indicate that the seller has enough sellable (undamaged) inventory to fulfill the order. The seller can cancel a fulfillment order that has a status of received.'),
					new self(self::AMAZON_FULFILLMENT_INVALID,
							'Amazon',
							'fulfillment invalid',
							'The fulfillment order was received by Amazon Marketplace Web Service (Amazon MWS) but could not be validated. The reasons for this include an invalid destination address or Amazon s records indicating that the seller does not have enough sellable inventory to fulfill the order. When this happens, the fulfillment order is invalid and no items in the order will ship.'),
					new self(self::AMAZON_FULFILLMENT_PLANNING,
							'Amazon',
							'fulfillment planning',
							'The fulfillment order has been sent to Amazon s fulfillment network to begin shipment planning, but no unit in any shipment has been picked from inventory yet. The seller can cancel a fulfillment order that has a status of planning.'),
					new self(self::AMAZON_FULFILLMENT_PROCESSING,
							'Amazon',
							'fulfillment processing',
							'The process of picking units from inventory has begun on at least one shipment in the fulfillment order. The seller cannot cancel a fulfillment order that has a status of processing.'),
					new self(self::AMAZON_FULFILLMENT_CANCELLED,
							'Amazon',
							'fulfillment cancelled',
							'The fulfillment order has been cancelled by the seller.'),
					new self(self::AMAZON_FULFILLMENT_COMPLETE,
							'Amazon',
							'fulfillment complete',
							'All item quantities in the fulfillment order have been fulfilled.'),
					new self(self::AMAZON_FULFILLMENT_COMPLETE_PARTIALLED,
							'Amazon',
							'fulfillment partial complete',
							'Some item quantities in the fulfillment order were fulfilled; the rest were either cancelled or unfulfillable.'),
					new self(self::AMAZON_FULFILLMENT_UNFULFILLABLE,
							'Amazon',
							'fulfillment unfillable',
							'No item quantities in the fulfillment order could be fulfilled because the Amazon fulfillment center workers found no inventory for those items or found no inventory that was in sellable (undamaged) condition'),
					new self(self::AMAZON_PACKAGE_TRACKING_NUMBER,
							'Amazon',
							'tracking number provided',
							'A tracking number for a fulfillment package was provided by Amazon')
			);
		}
		return self::$values;
	}

	private $id;
	private $group;
	private $name;
	private $description;

	/**
	 * @param string $id
	 * @param string $group
	 * @param string $name
	 * @param string $description
	 */
	private function __construct($id, $group, $name, $description) {
		$this->id = $id;
		$this->group = $group;
		$this->name = $name;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
}
