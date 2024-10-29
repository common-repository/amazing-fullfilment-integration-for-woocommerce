<?php if($controller->hasLicense()): ?>
	<?php include('LicenseDisplay.php'); ?>
<?php else: ?>
	<?php include('LicenseSetup.php'); ?>
<?php endif; ?>
