<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_WooCommerce_WooCommerce {
	private $products = NULL;
	private $orders = NULL;
	private $panel;
	private $packageTrackingEmail = NULL;

	public function __construct() {
		$this->panel = new AmzFulfillment_WooCommerce_Panel();
	}

	/**
	 * @return AmzFulfillment_WooCommerce_Products
	 */
	public function products() {
		if($this->products === NULL) {
			$this->products = new AmzFulfillment_WooCommerce_Products();
		}
		return $this->products;
	}

	/**
	 * @return AmzFulfillment_WooCommerce_Orders
	 */
	public function orders() {
		if($this->orders === NULL) {
			$this->orders = new AmzFulfillment_WooCommerce_Orders();
		}
		return $this->orders;
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return get_option('woocommerce_currency', '');
	}

	/**
	 * @return WooCommerce
	 */
	public function getHandle() {
		global $woocommerce;
		return $woocommerce;
	}

	public function getPackageTrackingEmail() {
		if($this->packageTrackingEmail === NULL) {
			$this->packageTrackingEmail = new AmzFulfillment_WooCommerce_Email_PackageTrackingEmail();
		}
		return $this->packageTrackingEmail;
	}

	public function registerEmailTemplates($emailClasses) {
		$emailClasses[AmzFulfillment_WooCommerce_Email_Emails::PACKAGE_TRACKING] = $this->getPackageTrackingEmail();
		return $emailClasses;
	}
}
