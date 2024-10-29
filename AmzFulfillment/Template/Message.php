<div class="<?php echo $controller->getClasses(); ?> amzfulfillment-message">
	<?php if($controller->isDismissable() && $controller->hasDismissAction()): ?>
	<a class="amzfulfillment-message-close notice-dismiss" href="<?php echo $controller->getDismissAction(); ?>"></a>
	<?php endif; ?>
	<p><strong><?php echo $controller->getCaption(); ?></strong></p>
	<?php if($controller->hasMessage()): ?>
	<p><?php echo $controller->getMessage(); ?></p>
	<?php endif; ?>
</div>
