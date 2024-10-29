<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Action {
	const WOOCOMMERCE_ORDER_PENDING		= 'wcOrderPending';
	const WOOCOMMERCE_ORDER_PROCESSING	= 'wcOrderProcessing';
	const WOOCOMMERCE_ORDER_ONHOLD		= 'wcOrderOnHold';
	const WOOCOMMERCE_ORDER_COMPLETED	= 'wcOrderCompleted';
	const WOOCOMMERCE_ORDER_CANCELLED	= 'wcOrderCancelled';
	const WOOCOMMERCE_ORDER_REFUNDED	= 'wcOrderRefunded';
	const WOOCOMMERCE_ORDER_FAILED		= 'wcOrderFailed';
	const AMAZON_FULFILLMENT_CREATE		= 'amFulfillmentCreate';
	const AMAZON_FULFILLMENT_CANCEL		= 'amFulfillmentCancel';
	const EMAIL_ADMIN					= 'emailAdmin';
	const EMAIL_PACKAGE_TRACKING		= 'emailPackageTracking';

	private static $values = NULL;

	/**
	 * 
	 * @param string $actionId
	 * @throws InvalidArgumentException
	 * @return AmzFulfillment_Entity_Action
	 */
	public static function instance($actionId) {
		foreach(self::values() as $action) {
			if($action->getId() == $actionId) {
				return $action;
			}
		}
		throw new InvalidArgumentException("Unknown action: " . $action);
	}

	/**
	 * @return AmzFulfillment_Entity_Action[]
	 */
	public static function values() {
		if(self::$values === NULL) {
			self::$values = array(
					new self(self::WOOCOMMERCE_ORDER_PENDING,		'set woocommerce status to pending payment',	'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_PROCESSING,	'set woocommerce status to processing',			'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_ONHOLD,		'set woocommerce status to on-hold',			'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_COMPLETED,		'set woocommerce status to completed',			'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_CANCELLED,		'set woocommerce status to cancelled',			'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_REFUNDED,		'set woocommerce status to refunded',			'WooCommerce'),
					new self(self::WOOCOMMERCE_ORDER_FAILED,		'set woocommerce status to failed',				'WooCommerce'),
					new self(self::AMAZON_FULFILLMENT_CREATE,		'create fulfillment',							'Amazon'),
					new self(self::AMAZON_FULFILLMENT_CANCEL,		'cancel fulfillment',							'Amazon'),
					new self(self::EMAIL_ADMIN,						'send email notification to wp admin',			'Notification'),
					new self(self::EMAIL_PACKAGE_TRACKING,			'send package tracking email',					'Notification'));
		}
		return self::$values;
	}

	private $id;
	private $name;
	private $group;

	private function __construct($id, $name, $group) {
		$this->id = $id;
		$this->name = $name;
		$this->group = $group;
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
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getGroup() {
		return $this->group;
	}
}
