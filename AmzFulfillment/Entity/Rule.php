<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Rule {
	private $event;
	private $action;

	public function __construct($eventId, $actionId) {
		if(!is_string($eventId)) {
			throw new InvalidArgumentException('Value of eventId needs to be string');
		}
		if(!is_string($actionId)) {
			throw new InvalidArgumentException('Value of actionId needs to be string');
		}
		$this->event = AmzFulfillment_Entity_Event::instance($eventId);
		$this->action = AmzFulfillment_Entity_Action::instance($actionId);
	}

	/**
	 * @return AmzFulfillment_Entity_Event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * @return AmzFulfillment_Entity_Action
	 */
	public function getAction() {
		return $this->action;
	}
}
