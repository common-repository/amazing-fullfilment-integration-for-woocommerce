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

<?php /* WooCommerce email header will be auto added here */ ?>

<div style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px; text-align: center;">
	<p>{tracking_text}</p>
	<table style="font-size:13px; background:#fff; margin:0 auto; width:500px; padding:10px; text-align: center;">
		<thead>
			<tr>
				<th>{carrier_text}</th>
				<th>{tracking_number_text}</th>
				<th>{estimated_arrival_text}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{carrier_name}</td>
				<td>{tracking_number}</td>
				<td>{estimated_arrival_time}</td>
			</tr>
		</tbody>
	</table>
</div>

<?php /* WooCommerce email footer will be auto added here */ ?>
