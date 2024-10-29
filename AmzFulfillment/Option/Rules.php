<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Option_Rules extends AmzFulfillment_Option_CachedOption {
	const KEY = 'amzFulfillmentRules';

	public function __construct() {
		parent::__construct(self::KEY);
	}

	protected function toObject($value) {
		if(!is_array($value)) {
			return NULL;
		}
		$rules = array();
		foreach($value as $rule) {
			$rules[] = new AmzFulfillment_Entity_Rule($rule['event'], $rule['action']);
		}
		return $rules;
	}

	protected function toValue($object) {
		$value = array();
		foreach($object as $rule) {
			try {
				$value[] = array(
						'event'  => $rule->getEvent()->getId(),
						'action' => $rule->getAction()->getId());
			} catch(Exception $e) {
				AmzFulfillment_Logger::warn('Failed to load rule: ' . $e->getMessage());
			}
		}
		return $value;
	}

	protected function defaultObject() {
		return array(
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::WOOCOMMERCE_ORDER_PROCESSING,		AmzFulfillment_Entity_Action::AMAZON_FULFILLMENT_CREATE),
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_CANCELLED,		AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_CANCELLED),
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_INVALID,			AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_FAILED),
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_COMPLETE,		AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_COMPLETED),
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::AMAZON_FULFILLMENT_UNFULFILLABLE,	AmzFulfillment_Entity_Action::WOOCOMMERCE_ORDER_FAILED),
				new AmzFulfillment_Entity_Rule(AmzFulfillment_Entity_Event::AMAZON_PACKAGE_TRACKING_NUMBER,		AmzFulfillment_Entity_Action::EMAIL_PACKAGE_TRACKING)
		);
	}
}
