<div <?php if(!empty($controller->id)) echo printf('id="%s"', $controller->id); ?> class="amzfulfillment-rule-item">
	<span class="amzfulfillment-remove" onclick="jQuery(this).parent().remove();"></span>
	If status changed to
	<select name="events[]">
		<?php if(empty($controller->selectedEvent)): ?>
		<option value="" disabled selected></option>
		<?php endif; ?>
		<?php foreach(AmzFulfillment_Panel_Tab_Automation::getEventGroups() as $group): ?>
		<optgroup label="<?php echo $group; ?>">
			<?php foreach(AmzFulfillment_Panel_Tab_Automation::getEvents($group) as $event): ?>
			<option <?php if($event->getId() == $controller->selectedEvent) echo "selected"; ?> value="<?php echo $event->getId(); ?>"><?php echo $event->getName(); ?></option>
			<?php endforeach; ?>
		</optgroup>
		<?php endforeach; ?>
	</select>
	then
	<select name="actions[]">
		<?php if(empty($controller->selectedAction)): ?>
		<option value="" disabled selected></option>
		<?php endif; ?>
		<?php foreach(AmzFulfillment_Panel_Tab_Automation::getActionGroups() as $group): ?>
		<optgroup label="<?php echo $group; ?>">
			<?php foreach(AmzFulfillment_Panel_Tab_Automation::getActions($group) as $action): ?>
			<option <?php if($action->getId() == $controller->selectedAction) echo "selected"; ?> value="<?php echo $action->getId(); ?>"><?php echo $action->getName(); ?></option>
			<?php endforeach; ?>
		</optgroup>
		<?php endforeach; ?>
	</select>
</div>
