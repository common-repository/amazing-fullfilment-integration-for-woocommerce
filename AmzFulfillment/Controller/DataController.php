<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_DataController {
	private $fulfillmentRepository = NULL;
	private $inventoryRepository = NULL;
	private $logRepository = NULL;
	private $packageRepository = NULL;
	private $listingRepository = NULL;
	private $options = NULL;
	private $rules = NULL;

	public function __construct() {
	}

	/**
	 * @return AmzFulfillment_Repository_FulfillmentRepository
	 */
	public function fulfillments() {
		if($this->fulfillmentRepository === NULL) {
			$this->fulfillmentRepository = new AmzFulfillment_Repository_FulfillmentRepository();
		}
		return $this->fulfillmentRepository;
	}

	/**
	 * @return AmzFulfillment_Repository_InventoryRepository
	 */
	public function inventory() {
		if($this->inventoryRepository === NULL) {
			$this->inventoryRepository = new AmzFulfillment_Repository_InventoryRepository();
		}
		return $this->inventoryRepository;
	}

	/**
	 * @return AmzFulfillment_Repository_LogRepository
	 */
	public function logs() {
		if($this->logRepository === NULL) {
			$this->logRepository = new AmzFulfillment_Repository_LogRepository();
		}
		return $this->logRepository;
	}

	/**
	 * @return AmzFulfillment_Repository_PackageRepository
	 */
	public function packages() {
		if($this->packageRepository === NULL) {
			$this->packageRepository = new AmzFulfillment_Repository_PackageRepository();
		}
		return $this->packageRepository;
	}

	/**
	 * @return AmzFulfillment_Repository_ListingRepository
	 */
	public function listings() {
		if($this->listingRepository === NULL) {
			$this->listingRepository = new AmzFulfillment_Repository_ListingRepository();
		}
		return $this->listingRepository;
	}

	/**
	 * @return AmzFulfillment_Repository_Repository[]
	 */
	public function repositories() {
		return array($this->fulfillments(), $this->inventory(), $this->logs(), $this->packages(), $this->listings());
	}

	/**
	 * @return AmzFulfillment_Option_Options
	 */
	public function options() {
		if($this->options === NULL) {
			$this->options = new AmzFulfillment_Option_Options();
		}
		return $this->options;
	}

	/**
	 * @return AmzFulfillment_Option_Rules
	 */
	public function rules() {
		if($this->rules === NULL) {
			$this->rules = new AmzFulfillment_Option_Rules();
		}
		return $this->rules;
	}
}
