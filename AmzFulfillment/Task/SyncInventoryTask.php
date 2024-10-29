<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Task_SyncInventoryTask extends AmzFulfillment_Task_Task {
	protected function run() {
		$mws = AmzFulfillment_Main::instance()->amazonMWS();
		$wooCommerce = AmzFulfillment_Main::instance()->wooCommerce();
		$inventoryRepo = AmzFulfillment_Main::instance()->data()->inventory();
		$options = AmzFulfillment_Main::instance()->data()->options()->options();
		$itemsTotal = 0;
		$quantityTotal = 0;
		$products = $wooCommerce->products()->getAll();
		$skus = $wooCommerce->products()->getSkus();
		if(!count($skus)) {
			AmzFulfillment_Logger::warn(__CLASS__ . ' - Failed to sync inventory: No WooCommere products with sku found');
			return;
		}
		foreach($mws->fbaInventory()->getItems($skus) as $item) {
			try {
				$sku = $item->getSellerSKU();
				$quantity = $item->getInStockSupplyQuantity();
				$inventoryRepo->set($sku, $quantity);
				if(!in_array($sku, $options->getSyncSkus())) {
					continue;
				}
				AmzFulfillment_Logger::debug(sprintf("%s - Processing product '%s'", __CLASS__, $sku));
				$itemsTotal++;
				$quantityTotal += $quantity;
				$product = $wooCommerce->products()->getProductBySku($sku);
				if($product == NULL) {
					continue;
				}
				if($product['varriantId'] !== FALSE) {
					$productId = $product['varriantId'];
				} else {
					$productId = $product['productId'];
				}
				if($product['stock'] != $quantity) {
					$quantityOld = $product['stock'];
					$wooCommerce->products()->setStock($productId, $quantity);
					AmzFulfillment_Logger::debug(sprintf("%s - Updated '%s' stock %d to %d", __CLASS__, $sku, $quantityOld, $quantity));
					if($quantityOld > 0 && $quantity == 0) {
						$wooCommerce->products()->setAvailable($productId, FALSE);
						AmzFulfillment_Logger::info(sprintf(__CLASS__ . " - Updated '%s' to 'out of stock'", $sku));
					} elseif($quantityOld == 0 && $quantity > 0) {
						$wooCommerce->products()->setAvailable($productId, TRUE);
						AmzFulfillment_Logger::info(sprintf("%s - Updated '%s' to 'in stock'", __CLASS__, $sku));
					}
				}
			} catch(Exception $e) {
				AmzFulfillment_Logger::warn(sprintf("%s - Failed to update '%s' inventory: %s", __CLASS__, $item->getSellerSKU(), $e->getMessage()));
			}
		}
		AmzFulfillment_Logger::debug(sprintf('%s - Synced inventory with %d items and %d units total stock', __CLASS__, $itemsTotal, $quantityTotal));
	}
}
