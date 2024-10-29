<?php if(!AmzFulfillment_Main::instance()->featureController()->hasAutomation()): ?>

<?php include 'ProNotice.php'; ?>
<img src="<?php echo $controller->getResource('images/proPreviewAutomation.png'); ?>" alt="preview" />

<?php else: ?>

<p class="description">
	Automation rules can be configured to optimize your order and fulfillment workflow. A rule consists of an event (WooCommerce order is created for instance) and an action (e.g. create amazon fulfillment). WooCommerce actions will be excuted immediately, actions on amazon will be executed in the next sync. The sync interval can be configured in the
	<a href="admin.php?page=amzFulfillment&tab=Settings">settings</a> tab.
</p>

<div class="postbox-container">
	<div class="meta-box">
		<div class="postbox panel">
			<div class="inside">
				<div id="amzfulfillment-rules">
					<div class="amzfulfillment-rule-header">
						<div class="column">Events</div>
						<div class="column">Actions</div>
					</div>
					<div id="amzfulfillment-rule-container" class="amzfulfillment-rule-container">
						<?php $controller->addRuleElement(false, false, "amzfulfillment-rule-template"); ?>
						<?php foreach($controller->getRules() as $rule): ?>
						<?php $controller->addRuleElement($rule->getEvent()->getId(), $rule->getAction()->getId()); ?>
						<?php endforeach; ?>
					</div>
					<span id="amzfulfillment-rule-add" class="amzfulfillment-add"></span>
					<label id="amzfulfillment-rule-add-label" for="amzfulfillment-rule-add">Add new rule</label>
					<div class="amzfulfillment-rule-footer">
						<input type="submit" class="button button-primary" name="<?php echo AmzFulfillment_Panel_Tab_Automation::SAVE_ACTION; ?>" value="Save" />
						<input type="submit" class="button" name="<?php echo AmzFulfillment_Panel_Tab_Automation::RESET_ACTION; ?>" value="Reset to defaults" />
					</div>
					<p>Visit <a href="admin.php?page=wc-settings&tab=email">WooCommerce E-Mail settings</a> to manage Amazing Fulfillment Integration for WooCommerce notifications:</p>
					<ol>
						<li>
							<a href="admin.php?page=wc-settings&tab=email&section=<?php echo AmzFulfillment_WooCommerce_Email_Emails::PACKAGE_TRACKING; ?>"><?php echo AmzFulfillment_WooCommerce_Email_PackageTrackingEmail::TITLE; ?></a>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>

<h2>Amazon fulfillment status explained</h2>
<table class="widefat striped">
	<thead>
		<tr>
			<th>Event</th>
			<td>Description</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach(AmzFulfillment_Panel_Tab_Automation::getEvents('Amazon') as $event): ?>
		<tr>
			<td nowrap><b><?php echo ucwords($event->getName()); ?></b></td>
			<td><p class="description"><?php echo $event->getDescription(); ?></p></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>
