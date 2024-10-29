<h2>Connect WooCommerce with the Amazon MWS API</h2>
<p class="description">To connect WooCommerce with Amazon, you need to configure Amazon MWS access credentials to fill the blanks below.
<a href="https://amazing-fulfillment.com/doc" target="_blank">Click here to learn how to get and configure Amazon MWS credentials<img src="<?php echo $controller->getResource('images/iconExternalLink.png'); ?>" alt="mws-tutorial-link" /></a></p>

<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc"><label for="MerchantId">Seller ID</label></th>
			<td class="forminp forminp-text">
				<input name="merchantId" id="merchantId" type="text" value="<?php echo $controller->getOption('merchantId'); ?>" class="regular-text code" placeholder="">
				<p class="description">Your Amazon Seller ID</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc"><label for="accessKeyId">AWS Access Key ID</label></th>
			<td class="forminp forminp-text">
				<input name="accessKeyId" id="accessKeyId" type="text" value="<?php echo $controller->getOption('accessKeyId'); ?>" class="regular-text code" placeholder="">
				<p class="description">AWS Access Key ID, form your Amazon MWS credentials</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc"><label for="secretAccessKeyId">Secret Key</label></th>
			<td class="forminp forminp-text">
				<input name="secretAccessKeyId" id="secretAccessKeyId" type="text" value="<?php echo $controller->getOption('secretAccessKeyId'); ?>" class="regular-text code" placeholder="">
				<p class="description">Secret Key, form your Amazon MWS credentials</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc"><label for="marketplace">Marketplace</label></th>
			<td class="forminp forminp-select">
				<select name="marketplace" id="marketplace" class="">
					<option value="">select your marketplace</option>
					<?php foreach($controller->getEndpoints() as $endpoint): ?>
						<optgroup label="<?php echo $endpoint['name']; ?>">
						<?php foreach($controller->getMarketplaces($endpoint['regionCode']) as $marketplace): ?>
							<option value="<?php echo $marketplace['countryCode']; ?>" <?php if($controller->getOptions()->getMarketplace() == $marketplace['countryCode']) echo 'selected' ?>><?php echo $marketplace['name']; ?></option>
						<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
				<p class="description">Amazon Marketplace</p>
			</td>
		</tr>
	</tbody>
</table>

<h2>Fulfillment and scheduling</h2>
<table class="form-table">
	<tbody>
		<tr valign="top" class="option-site-visibility">
			<th scope="row" class="titledesc">Hold fulfillment</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span>Hold fulfillments</span>
					</legend>
					<label for="hold">
						<input name="hold" id="hold" <?php if($controller->getOptions()->getHold() === true) echo 'checked'; ?> type="checkbox" class="" value="1"> Create amazon fulfillments with the hold option
					</label>
					<p class="description">You can put an order hold on a your fulfillments when you want to delay fulfillment for any reason, such as verifying payment. An order hold not only prevents a fulfillment order from being shipped, it also prevents the inventory items in the fulfillment order from being used for Fulfillment by Amazon (FBA) orders on Amazon’s retail website. This can help you avoid overselling your FBA inventory that is available both on Amazon’s retail website and through Multi-Channel Fulfillment. Note: An order hold stays in effect for two weeks. After two weeks, if you have not shipped or cancelled the fulfillment order, it is automatically cancelled. At that point your inventory items in the fulfillment order can be used for FBA orders on Amazon’s retail website.</p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc"><label for="schedulingInterval">Sync interval</label></th>
			<td class="forminp forminp-text">
				<input name="schedulingInterval" id="schedulingInterval" type="number" value="<?php echo $controller->getOptions()->getSchedulingInterval() / 60; ?>" class="code" /> Minutes
				<p class="description">Specifies the run interval for the worker process including sync tasks and automation rule processing. Low values (frequent syncs) may exceededs Amazon API quota. In this case you will see &quot;Request is throttled&quot; in the <a href="admin.php?page=amzFulfillment&tab=Logs">logs</a> tab.</p>
			</td>
		</tr>
		<tr valign="top" class="option-site-visibility">
			<th scope="row" class="titledesc">Automation</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span>Automation</span>
					</legend>
					<label for="automation">
						<?php if(AmzFulfillment_Main::instance()->featureController()->hasAutomation()): ?>
						<input name="automation" id="automation" <?php if($controller->getOptions()->getAutomation()) echo 'checked'; ?> type="checkbox" class="" value="1" > enable
						<?php else: ?>
						<input name="automation" id="automation" type="checkbox" value="1" disabled> requires <span class="amzfulfillment-pro">pro</span>
						<?php endif; ?>
					</label>
					<p class="description">When enabled, <a href="admin.php?page=amzFulfillment&tab=Automation">Automation  <span class="amzfulfillment-pro">pro</span></a> rules will processed on each sync.</p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th></th>
			<td scope="row" class="titledesc">
				<input id="save" type="submit" class="button-primary" name="<?php echo AmzFulfillment_Panel_Tab_Settings::SAVE_ACTION; ?>" value="Save" />
				<input id="testConnect" type="submit" class="button" name="<?php echo AmzFulfillment_Panel_Tab_Settings::TEST_AMAZON_API_ACTION; ?>" value="Amazon Connection Test" />
			</td>
		</tr>
	</tbody>
</table>
