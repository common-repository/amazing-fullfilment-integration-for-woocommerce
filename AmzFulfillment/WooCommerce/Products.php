<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_WooCommerce_Products {
	public function __construct() {
	}

	/**
	 * getSkus
	 *
	 * Get skus from all products and varriations
	 *
	 * @return array
	 */
	public function getSkus() {
		$skus = array();
		foreach($this->getAll() as $product) {
			if(!empty($product['sku'])) {
				$skus[] = $product['sku'];
			}
		}
		return $skus;
	}

	/**
	 * @param int $productId
	 * @param boolean $available
	 */
	public function setAvailable($productId, $available) {
		$product = wc_get_product($productId);
		if($product == null) {
			throw new RuntimeException(sprintf('Product not found: %d', $productId));
		}
		if($available) {
			$product->set_stock_status('instock');
		} else {
			$product->set_stock_status('outofstock');
		}
		$product->save();
	}

	/**
	 * proucts with propperties:
	 * - title
	 * - sku
	 * - stock
	 * - productId
	 * - varriantId
	 *
	 * @return array
	 */
	public function getAll() {
		global $wpdb;
		$products = array();
		$query = "
			SELECT p.ID, p.post_title, ts.slug
			FROM `%sposts` AS p
			INNER JOIN `%sterm_relationships` AS t ON p.ID = t.object_id
			INNER JOIN `%sterm_taxonomy` AS tt ON t.term_taxonomy_id = tt.term_taxonomy_id
			INNER JOIN `%sterms` AS ts ON tt.term_id = ts.term_id
			WHERE
				tt.taxonomy = 'product_type' AND
				p.post_type = 'product' AND
				p.post_status = 'publish'";
		$results = $wpdb->get_results(sprintf($query, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix));
		foreach($results as $result) {
			if($result->slug == "simple") {
				$products[] = array(
						'title' => $result->post_title,
						'sku' => get_post_meta($result->ID, '_sku', true),
						'stock' => get_post_meta($result->ID, '_stock', true),
						'productId' => $result->ID,
						'varriantId' => false
				);
			} elseif($result->slug == "variable"){
				$args = array('post_type' => 'product_variation', 'post_status' => array( 'publish' ), 'post_parent' => $result->ID);
				$variations = get_posts($args);
				foreach($variations as $variation) {
					$products[] = array(
							'title' => $result->post_title,
							'sku' => get_post_meta($variation->ID, '_sku', true),
							'stock' => get_post_meta($variation->ID, '_stock', true),
							'productId' => $result->ID,
							'varriantId' => $variation->ID
					);
				}
			}
		}
		return $products;
	}

	/**
	 * @param string $sku
	 * @return array|NULL
	 */
	public function getProductBySku($sku) {
		foreach($this->getAll() as $product) {
			if($product['sku'] == $sku) {
				return $product;
			}
		}
		return NULL;
	}

	/**
	 * @param string $sku
	 * @throws Exception
	 * @return int
	 */
	public function getProductIdBySku($sku) {
		global $wpdb;
		$query = "SELECT pm.post_id
			FROM %spostmeta AS pm
			JOIN %sposts AS p ON pm.post_id = p.ID AND
			pm.meta_value='%s' AND
			pm.meta_key='_sku' AND
			p.post_status='publish'";
		$result = $wpdb->get_results(sprintf($query, $wpdb->prefix, $wpdb->prefix, $sku));
		if(!isset($result[0])) {
			throw new Exception('No product found with sku ' . $sku);
		}
		return intval($result[0]->post_id);
	}

	/**
	 * @param int $productId
	 * @param int $stock
	 */
	public function setStock($productId, $quantity) {
		update_post_meta($productId, '_stock', $quantity);
	}
}
