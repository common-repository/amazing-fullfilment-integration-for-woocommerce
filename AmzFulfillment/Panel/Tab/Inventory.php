<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Tab_Inventory extends AmzFulfillment_Panel_Tab {
	const ID = 'Inventory';
	const TITLE = 'Inventory';
	const PRO = FALSE;

	const UPDATE_ACTION = 'update';

	private $optionsRepo;
	private $wooCommerceSKUs = array();

	public function __construct() {
		parent::__construct(self::ID, self::TITLE, self::PRO);
		$this->optionsRepo = AmzFulfillment_Main::instance()->data()->options();
	}

	public function doActions() {
		if($this->hasAction(self::UPDATE_ACTION)) {
			$this->update();
		}
	}

	private function update() {
		try {
			$syncSkus = array();
			if(isset($_REQUEST['syncSkus']) && is_array($_REQUEST['syncSkus'])) {
				$syncSkus = $_REQUEST['syncSkus'];
			}
			$options = AmzFulfillment_Main::instance()->data()->options()->options();
			$options->setSyncSkus($syncSkus);
			AmzFulfillment_Main::instance()->data()->options()->set($options);
			AmzFulfillment_Main::instance()->worker()->runTask(AmzFulfillment_Worker::SYNC_INVENTORY_TASK);
			AmzFulfillment_Logger::info("Inventory updated");
		} catch(Exception $e) {
			AmzFulfillment_Logger::error("Failed to run sync task: " . $e->getMessage());
		}
	}

	public function getInventoryItems() {
		$items = array();
		$products = AmzFulfillment_Main::instance()->wooCommerce()->products()->getAll();
		$amazonItems = AmzFulfillment_Main::instance()->data()->inventory()->get();
		foreach($products as $product) {
			$sku = $product['sku'];
			$this->wooCommerceSKUs[] = $sku;
			if(!isset($amazonItems[$sku])) {
				continue;
			}
			$amazonItem = $amazonItems[$sku];
			$items[] = array(
					'productId' => $product['productId'],
					'sku' => $sku,
					'title' => $product['title'],
					'amazonStock' => $amazonItem->getAmazonStock(),
					'shopStock' => $product['stock'],
					'selected' => $this->optionsRepo->options()->isSyncSku($sku),
					'updatedTime' => $amazonItem->getUpdatedTime());
		}
		return $items;
	}

	public function isAmazonConfigured() {
		return $this->optionsRepo->options()->hasAmazonCredentials();
	}

	public function getListings() {
		return AmzFulfillment_Main::instance()->data()->listings()->getAll();
	}

	public function isWooCommerceSku($sku) {
		foreach(AmzFulfillment_Main::instance()->wooCommerce()->products()->getAll() as $product) {
			if($product['sku'] == $sku) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
