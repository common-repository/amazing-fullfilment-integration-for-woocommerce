<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or exit;
echo wp_kses_post(wc_strtoupper(sprintf(__('Order number: %s', 'woocommerce'), $order->get_order_number()))) . "\n";
echo wc_format_datetime($order->get_date_created()) . "\n";
foreach($order->get_items() as $item) {
	printf("%3d x %s\n", $item->get_quantity(), $item->get_name());
}
echo "\n\n";
echo esc_html(wc_strtoupper(__( 'Shipping address', 'woocommerce'))) . "\n\n";
echo preg_replace('#<br\s*/?>#i', "\n", $order->get_formatted_shipping_address()) . "\n";
?>
