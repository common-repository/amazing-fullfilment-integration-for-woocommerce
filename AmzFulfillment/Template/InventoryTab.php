<table class="widefat striped" style="width:100%;">
	<thead>
		<tr>
			<th style="width:40px;">Sync</th>
			<th style="width:180px;">SKU</th>
			<th>Product</th>
			<th style="text-align:right; width:100px;">Shop</th>
			<th style="text-align:right; width:100px;">Amazon</th>
		</tr>
	</thead>
	<tbody class="ui-sortable">
		<?php if(!$controller->isAmazonConfigured()): ?>
		<tr>
			<td colspan="5" class="description">
				To be able to sync your products with amazon, you need to configure credentials for amazon MWS API access in the <a href="?page=amzFulfillment&tab=Settings">seettings</a> tab.
			</td>
		</tr>
		<?php else: ?>
		<?php if(!count($controller->getInventoryItems())): ?>
		<tr>
			<td colspan="5" class="description">No amazon products matching your woocommerce products found.</td>
		</tr>
		<?php else: ?>
		<?php foreach($controller->getInventoryItems() as $item): ?>
		<tr>
			<td style="text-align:center"><input type="checkbox" name="syncSkus[]" value="<?php echo $item['sku']; ?>" data-sku="<?php echo $item['sku']; ?>" <?php if($item['selected']) echo "checked"; ?> /></td>
			<td><?php echo $item['sku']; ?></td>
			<td><a href="post.php?post=<?php echo $item['productId']; ?>&action=edit"><?php echo $item['title']; ?></a></td>
			<td style="text-align:right;"><?php echo $item['shopStock']; ?></td>
			<td style="text-align:right;"><?php echo $item['amazonStock']; ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<th colspan="5"><input type="submit" class="button-primary" name="<?php echo AmzFulfillment_Panel_Tab_Inventory::UPDATE_ACTION; ?>" value="Update" /></th>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<p class="description">WooCommerce SKUs needs to match Amazon SKUs to appear in this list. Click <a id="amzfulfillment-listings-button">here</a> to display unmatched Amazon SKUs.</p>

<div id="amzfulfillment-listings">
	<table class="widefat striped" style="width:100%;">
		<thead>
			<tr>
				<th style="width:180px;">SKU</th>
				<th style="width:180px;">ASIN</th>
				<th>Product</th>
			</tr>
		</thead>
		<tbody class="ui-sortable">
			<?php foreach($controller->getListings() as $listing): ?>
			<?php if(!$controller->isWooCommerceSku($listing->getSku())): ?>
			<tr>
				<td><?php echo $listing->getSku(); ?></td>
				<td><?php echo $listing->getAsin(); ?></td>
				<td><?php echo $listing->getName(); ?></td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description">This list refreshes every 8 hours.</p>
</div>
