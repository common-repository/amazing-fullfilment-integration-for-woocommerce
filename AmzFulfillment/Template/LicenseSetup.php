<div id="amzfulfillment-license-setup-box">
	<div class="amzfulfillment-license-setup-column first">
		<h4><?php echo __("I have a License Key"); ?></h4>
		<?php include 'LicenseSetupFrom.php'; ?>
	</div>
	<div class="amzfulfillment-license-setup-column">
		<h4>I don't have a license yet</h4>
		<a href="<?php echo AmzFulfillment_Panel_Tab_License::PRODUCT_EVAL_URL; ?>" target="_blank">
			<img src="<?php echo $controller->getResource('images/iconEval.png'); ?>" />
			Free evaluation License <img src="<?php echo $controller->getResource('images/iconExternalLink.png'); ?>" alt="mws-tutorial-link" />
		</a>
		<p>Get a free evaluation license and test all pro features for a limited time of 1 month</p>
		<br />
		<br />
		<a href="<?php echo AmzFulfillment_Panel_Tab_License::PRODUCT_FULL_URL; ?>" target="_blank">
			<img src="<?php echo $controller->getResource('images/iconOrder.png'); ?>" />
			Purchase license <img src="<?php echo $controller->getResource('images/iconExternalLink.png'); ?>" alt="mws-tutorial-link" />
		</a>
		<p>Purchase a pro license to access all features with no limitation</p>
	</div>
</div>

<div class="clear"></div>
