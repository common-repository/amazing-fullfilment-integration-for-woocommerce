<div class="woocommerce_order_items_wrapper">
	<?php $fulfillment = $controller->getFulfillment(); ?>
	<table id="amzfulfillment-meta-table" class="woocommerce_order_items">
		<thead>
			<tr>
				<th style="width:200px; text-align:left;">Created</th>
				<th style="width:200px; text-align:left;">Status</th>
				<th style="text-align:left;">Packages</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width:200px; text-align:left;"><?php echo AmzFulfillment_Main::instance()->getFormatedDateTime($fulfillment->getCreateTime()); ?></td>
				<td style="width:200px; text-align:left;"><mark class="amzfulfillment-status"><span><?php echo ucfirst(strtolower($controller->getFulfillment()->getStatus())); ?></span></mark></td>
				<td style="text-align:left;"><?php echo $controller->getPackages(); ?></td>
			</tr>
		</tbody>
	</table>
</div>
