<div id="amzfulfillment-license-box">
	<?php if($controller->getLicenseType() == AmzFulfillment_Controller_FeatureController::FULL): ?>
	<div id="amzfulfillment-license-seal-full"></div>
	<?php elseif($controller->getLicenseType() == AmzFulfillment_Controller_FeatureController::EVALUATION): ?>
	<div id="amzfulfillment-license-seal-eval"></div>
	<?php endif; ?>
	<div class="amzfulfillment-license-title">Amazing Fulfillment Integration for WooCommerce</div>
	<div class="amzfulfillment-license-field">
		<p><?php echo __("License"); ?></p>
		<input type="text" value="<?php echo $controller->getLicenseText(); ?>" readonly />
	</div>
	<div class="amzfulfillment-license-field">
		<p><?php echo __("Validity"); ?></p>
		<input type="text" id="licenseKey" name="licenseKey" readonly value="<?php echo $controller->getValidityText(); ?>" placeholder="" />
	</div>
	<div class="amzfulfillment-license-footer">
	<?php if($controller->getLicenseType() == AmzFulfillment_Controller_FeatureController::FULL): ?>If you want to move your license to another wordpress installation or server, you need to <a href="#" id="deactivate">deactivate</a> your license first.
	<?php elseif($controller->getLicenseType() == AmzFulfillment_Controller_FeatureController::EVALUATION): ?>
	<?php if($controller->isExpired()): ?>Your evaluation license has been expired.
	<?php else: ?>Your evaluation license will expire in <b><?php echo $controller->getExpireMessage(); ?></b>.
	<?php endif; ?>
	<p>
		<a href="<?php echo AmzFulfillment_Panel_Tab_License::PRODUCT_FULL_URL; ?>" target="_blank">Purchase a full license <img src="<?php echo $controller->getResource('images/iconExternalLink.png'); ?>" alt="mws-tutorial-link" /></a> now, for unlimited access to all features.<br />
		Click <a href="<?php echo $_SERVER['REQUEST_URI'] . '&action=' . AmzFulfillment_Panel_Tab_License::SETUP_PRO_ACTION; ?>">here</a> to setup your full license.
	</p>
	<?php endif; ?>
	</div>
</div>

<div id="deactivate-confirm" title="Deactivate">
	<p>Please confirm license deactivation on this wordpress instance.</p>
</div>

<?php if($controller->hasAction(AmzFulfillment_Panel_Tab_License::SETUP_PRO_ACTION, $_REQUEST) && $controller->getLicenseType() != AmzFulfillment_Controller_FeatureController::FULL): ?>
<div id="amzfulfillment-license-upgrade-box">
	<?php include 'LicenseSetupFrom.php'; ?>
</div>
<?php endif; ?>

