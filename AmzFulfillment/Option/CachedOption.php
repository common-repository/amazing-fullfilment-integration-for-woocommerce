<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once ABSPATH . '/wp-admin/includes/upgrade.php';

abstract class AmzFulfillment_Option_CachedOption {
	private $key;
	private $cache = NULL;

	public function __construct($key) {
		$this->key = $key;
	}

	protected abstract function toObject($value);
	protected abstract function toValue($object);
	protected abstract function defaultObject();

	public function get() {
		if($this->cache === NULL) {
			$value = get_option($this->key, NULL);
			if($value === NULL) {
				$this->cache = $this->defaultObject();
			} else {
				$object = $this->toObject($value);
				if($object === NULL) {
					$this->cache = $this->defaultObject();
				} else {
					$this->cache = $object;
				}
			}
		}
		return $this->cache;
	}

	public function set($object) {
		update_option($this->key, $this->toValue($object));
		$this->cache = $object;
	}

	public function setDefaults() {
		$this->set($this->defaultObject());
	}
}
