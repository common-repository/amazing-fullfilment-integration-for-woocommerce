<?php $package = $controller->getPackage(); ?>
<div class="woocommerce_order_items_wrapper">

	<?php if($package === NULL): ?>

	<div class="amzfulfillment-tracking-error">There is no tracking data available for this package yet</div>

	<?php elseif($package->isTrackingError()): ?>

	<div class="amzfulfillment-tracking-error">Amazon sync error: <?php echo $package->getTrackingError(); ?></div>

	<?php else: ?>

	<div class="inside">
		<div class="tracking-container">
			<div class="tracking-column">
				<h3>Carrier</h3>
				<p><?php echo $package->getCarrierCode(); ?></p>
				<h3>Tracking number</h3>
				<p><?php echo $package->getTrackingNumber(); ?></p>
				<h3>Status</h3>
				<p><?php echo $package->getStatus(); ?></p>
				<span class="description"><?php echo $package->getStatusText(); ?></span>
			</div>
			<div class="tracking-column">
				<div class="timeline">

					<?php if(!$package->isDelivered()): ?>
					<div class="timeline-row">
						<div class="future"><?php echo AmzFulfillment_Main::instance()->getFormatedDateTime($package->getEstimatedArrivalTime()); ?></div>
						<div class="content">
							Estimated arrival
							<div class="description"><?php echo $package->getAddress(); ?></div>
						</div>
					</div>
					<?php endif; ?>

					<?php foreach($package->getTrackingEvents() as $event): ?>
					<div class="timeline-row">
						<div class="<?php if($event->getCode() == 'EVENT_301') echo "delivered"; else echo "current"; ?>"><?php echo AmzFulfillment_Main::instance()->getFormatedDateTime($event->getTime()); ?></div>
						<div class="content">
							<?php echo $controller->translateFulfillmentTrackingEvent($event->getCode()); ?>
							<div class="description"><?php if($event->getCode() != 'EVENT_301') echo $event->getAddress(); ?></div>
						</div>
					</div>
					<?php endforeach; ?>

					<div class="timeline-row">
						<div class="current"><?php echo AmzFulfillment_Main::instance()->getFormatedDateTime($package->getShipTime()); ?></div>
						<div class="content">
							Shipping created
							<div class="description">Amazon</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>

</div>
