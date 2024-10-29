<div class="wrap">
	<form method="post" id="amzFulfillmentMenu" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php foreach($controller->getTabs() as $tab): ?>
			<a href="?page=amzFulfillment&tab=<?php echo $tab->getId(); ?>" class="<?php if($controller->isActiveTab($tab->getId())) echo 'nav-tab nav-tab-active'; else echo 'nav-tab'; ?>">
				<?php echo $tab->getTitle(); ?>
				<?php if($tab->isPro()): ?>
				<span class="amzfulfillment-pro">pro</span>
				<?php endif; ?>
			</a>
			<?php endforeach; ?>
		</nav>
		<h1 class="screen-reader-text"><?php echo $controller->getActiveTab()->getTitle(); ?></h1>
		<br class="clear">
		<?php $controller->getActiveTab()->show(); ?>
		<div class="clear"></div>
	</form>
</div>

<div class="clear"></div>
