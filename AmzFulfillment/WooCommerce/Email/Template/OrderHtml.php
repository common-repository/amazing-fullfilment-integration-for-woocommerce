<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<?php defined('ABSPATH') or exit; ?>

<?php $text_align = is_rtl() ? 'right' : 'left'; ?>

<h2><?php echo wp_kses_post(sprintf(__( 'Order #%s', 'woocommerce') . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format('c'), wc_format_datetime($order->get_date_created()))); ?></h2>

<div style="margin-bottom: 40px;">
	<table class="td" style="width:100%; font-family:'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:1px solid; border-collapse:collapse;">
		<thead>
			<tr>
				<th scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($order->get_items() as $item): ?>
			<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
				<td style="text-align:<?php echo $text_align; ?>;" valign="top"><?php echo($item->get_name()); ?></td>
				<td style="text-align:<?php echo $text_align; ?>;" valign="top"><?php echo($item->get_quantity()); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<table id="addresses" style="width:100%; vertical-align:top; margin-bottom:40px; padding:0; border:1px solid; border-collapse:collapse;">
	<tr>
		<td style="text-align:<?php echo $text_align; ?>; padding:6px; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
			<h2><?php _e('Shipping address', 'woocommerce'); ?></h2>
			<address class="address"><?php echo ($address = $order->get_formatted_shipping_address()) ? $address: __('N/A', 'woocommerce'); ?></address>
		</td>
	</tr>
</table>
