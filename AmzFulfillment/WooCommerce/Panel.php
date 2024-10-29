<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_WooCommerce_Panel {
	const MENU_ID = 'woocommerce';

	public function __construct() {
		add_action('woocommerce_order_actions',								array($this, 'addActions'));
		add_action('woocommerce_order_action_create_amazon_fulfillment',	array($this, 'doCreateAmazonFulfillment'));
		add_action('woocommerce_order_action_cancel_amazon_fulfillment',	array($this, 'doCancelAmazonFulfillment'));
		add_action('admin_footer-edit.php',									array($this, 'addBulkActions'));
		add_action('load-edit.php',											array($this, 'onBulk'));
		add_action('add_meta_boxes', 										array($this, 'addMetaBoxes'), 10, 2);
		add_filter('manage_edit-shop_order_columns', 						array($this, 'addShopOrderColumns'), 20);
		add_action('manage_shop_order_posts_custom_column' , 				array($this, 'onShopOrderColumns'), 20, 2);
	}

	public function addActions($actions) {
		$actions['create_amazon_fulfillment'] = 'Amazon fulfillment';
		return $actions;
	}

	public function doCreateAmazonFulfillment($order) {
		if(is_int($order)) {
			$orderId = $order;
		} else {
			$orderId = $order->get_id();
		}
		AmzFulfillment_Main::instance()->fulfillmentController()->create($orderId);
	}

	public function doCancelAmazonFulfillment($order) {
		if(is_int($order)) {
			$orderId = $order;
		} else {
			$orderId = $order->get_id();
		}
		AmzFulfillment_Main::instance()->fulfillmentController()->cancel($orderId);
	}

	public function addBulkActions() {
		global $post_type;
		if ($post_type == 'shop_order') {
			AmzFulfillment_Template::load('OrdersBulkActions');
		}
	}

	public function onBulk() {
		switch(_get_list_table('WP_Posts_List_Table')->current_action()) {
			case 'bulkAmazonFulfillment':
				$this->doBulkFulfillment();
				break;
			default:
				break;
		}
	}

	public function doBulkFulfillment() {
		$orderIds = array_map('absint', (array) $_REQUEST['post']);
		if(!count($orderIds)) {
			AmzFulfillment_Logger::warn('No orders selected for fulfillment');
			return;
		}
		AmzFulfillment_Logger::debug(sprintf('Start bulk fulfillment for orders: %s', implode(', ', $orderIds)));
		foreach($orderIds as $orderId) {
			AmzFulfillment_Main::instance()->fulfillmentController()->create($orderId);
		}
	}

	public function addMetaBoxes($postType, $post) {
		if($this->hasFulfillment()) {
			add_meta_box('woocommerce_order_amazon_fulfillment', 'Amazon Fulfillment', array($this, 'getOrderFulfillmentMetaBox'), 'shop_order', 'normal', 'high');
			if(isset($_REQUEST['showPackage']) && !empty($_REQUEST['showPackage'])) {
				$packageNumber = $_REQUEST['showPackage'];
				add_meta_box('woocommerce_order_amazon_package', sprintf('Amazon Package %d', $packageNumber), array($this, 'getOrderPackageMetaBox'), 'shop_order', 'normal', 'high');
			}
		}
	}

	public function getOrderFulfillmentMetaBox() {
		AmzFulfillment_Template::load('OrderFulfillmentMetaBox', $this);
	}

	public function getOrderPackageMetaBox() {
		AmzFulfillment_Template::load('OrderPackageMetaBox', $this);
	}

	public function addShopOrderColumns($columns) {
		$columns['order_amazon_fulfillment'] = 'Amazon Fulfillment';
		return $columns;
	}

	public function onShopOrderColumns($column, $orderId) {
		if($column == 'order_amazon_fulfillment') {
			if($this->hasFulfillment($orderId)) {
				$status = $this->getFulfillment($orderId)->getStatus();
				if(!empty($status)) {
					$status = ucfirst(str_replace('_', ' ', strtolower($status)));
				} else {
					$status = "Fulfillment created";
				}
				printf('<mark class="amzfulfillment-status wide"><span>%s</span></mark>', $status);
			}
		}
	}

	public function hasFulfillment($orderId = NULL) {
		if($orderId === NULL) {
			$orderId = $this->getOrderId();
		}
		return AmzFulfillment_Main::instance()->data()->fulfillments()->exist($orderId);
	}

	public function getFulfillment($orderId = NULL) {
		if($orderId === NULL) {
			$orderId = $this->getOrderId();
		}
		return AmzFulfillment_Main::instance()->data()->fulfillments()->get($orderId);
	}

	public function getPackage($packageNumber = NULL) {
		if($packageNumber === NULL) {
			if(isset($_REQUEST['showPackage']) && !empty($_REQUEST['showPackage'])) {
				$packageNumber = $_REQUEST['showPackage'];
			}
		}
		return AmzFulfillment_Main::instance()->data()->packages()->get($packageNumber);
	}

	public function getPackages($orderId = NULL) {
		if($orderId === NULL) {
			$orderId = $this->getOrderId();
		}
		$packages = array();
		foreach(AmzFulfillment_Main::instance()->data()->packages()->getByOrder($orderId) as $package) {
			$status = sprintf("&#35;%s %s", $package->getPackageNumber(), ucfirst(str_replace('_', ' ', strtolower($package->getStatus()))));
			if($package->getStatus() != "DELIVERED" && !empty($package->getEstimatedArrivalTime())) {
				$status .= sprintf(" &#40;ETA %s&#41;", AmzFulfillment_Main::instance()->getFormatedDate($package->getEstimatedArrivalTime()));
			}
			$packages[] = sprintf('<mark class="amzfulfillment-package"><span class="amzfulfillment-package-button" data-packagenumber="%d"><span class="dashicons dashicons-archive"></span> %s</span></mark>',
					$package->getPackageNumber(), $status);
		}
		return implode($packages);
	}

	public function getOrderId() {
		return get_the_ID();
	}

	public function translateFulfillmentTrackingEvent($eventId) {
		if(isset(AmzFulfillment_Amazon_Package::$trackingEvent[$eventId])) {
			return AmzFulfillment_Amazon_Package::$trackingEvent[$eventId];
		} else {
			return $eventId;
		}
	}
}
