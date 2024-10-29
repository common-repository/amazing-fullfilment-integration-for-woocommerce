<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_WooCommerce_Orders {
	public function get($orderId) {
		if(!$this->exist($orderId)) {
			throw new RuntimeException(sprintf('Invalid woocommerce order with id %d', $orderId));
		}
		return WC_Order_Factory::get_order($orderId);
	}

	public function exist($orderId) {
		return WC_Order_Factory::get_order($orderId) !== false;
	}

	public function setStatus($orderId, $status) {
		if(!array_key_exists($status, AmzFulfillment_WooCommerce_Status::$orderStatus)) {
			throw new InvalidArgumentException('Invalid order status type: ' . $status);
		}
		$order = $this->get($orderId);
		if($order->get_status() == $status) {
			throw new RuntimeException('Order is already in the status to be set: ' . $status);
		}
		$order->set_status($status, 'Order changed by Amazing Fulfillment plugin to ' . $status, true);
		$order->save();
	}

	public function note($orderId, $note) {
		$order = $this->get($orderId);
		$order->add_order_note($note);
	}

	public function getOpen($maxAgeDays) {
		$args = array(
				'orderby'		=> 'modified',
				'order'			=> 'DESC',
				'date_created'	=> '>' . (time() - $maxAgeDays * HOUR_IN_SECONDS)
		);
		return wc_get_orders($args);
	}
}
