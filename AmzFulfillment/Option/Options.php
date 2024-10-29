<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2017 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Option_Options extends  AmzFulfillment_Option_CachedOption {
	const KEY = 'amzFulfillmentConfiguration';

	public function __construct() {
		parent::__construct(self::KEY);
	}

	/**
	 * @return AmzFulfillment_Entity_Options
	 */
	public function options() {
		return $this->get();
	}

	protected function toObject($value) {
		if(!is_array($value)) {
			return NULL;
		}
		$object = new AmzFulfillment_Entity_Options();
		$object->setByArray($value);
		return $object;
	}

	protected function toValue($object) {
		return $object->getAsArray();
	}

	protected function defaultObject() {
		return new AmzFulfillment_Entity_Options();
	}
}
