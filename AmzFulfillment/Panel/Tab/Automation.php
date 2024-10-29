<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Tab_Automation extends AmzFulfillment_Panel_Tab {
	const ID = 'Automation';
	const TITLE = 'Automation';
	const PRO = true;
	const SAVE_ACTION = 'saveRules';
	const RESET_ACTION = 'resetRules';

	private $rulesRepo;
	public $selectedEvent, $selectedAction, $id;

	public function __construct() {
		parent::__construct(self::ID, self::TITLE, self::PRO);
		$this->rulesRepo = AmzFulfillment_Main::instance()->data()->rules();
	}

	public function doActions() {
		if($this->hasAction(self::SAVE_ACTION)) {
			$this->save();
		}
		if($this->hasAction(self::RESET_ACTION)) {
			$this->reset();
		}
	}

	private function save() {
		if(!isset($_REQUEST['events']) || !isset($_REQUEST['actions'])) {
			AmzFulfillment_Logger::error("Not saved: Check input");
			return;
		}
		$eventsArray = $_REQUEST['events'];
		$actionsArray = $_REQUEST['actions'];
		$rules = array();
		for($i = 0; $i < min(count($eventsArray), count($actionsArray)); $i++) {
			$eventId = $eventsArray[$i];
			$actionId = $actionsArray[$i];
			if(empty($eventId) || empty($actionId)) {
				continue;
			}
			$rules[] = new AmzFulfillment_Entity_Rule($eventId, $actionId);
		}
		try {
			$this->rulesRepo->set($rules);
			AmzFulfillment_Logger::info(sprintf("%d rules saved", count($rules)));
		} catch(Exception $e) {
			AmzFulfillment_Logger::error("Failed to save rules: " . $e->getMessage());
		}
	}

	public function reset() {
		$this->rulesRepo->setDefaults();
		AmzFulfillment_Logger::info("Default rules restored");
	}

	public function getRules() {
		try {
			return AmzFulfillment_Main::instance()->data()->rules()->get();
		} catch(InvalidArgumentException $e) {
			AmzFulfillment_Logger::error('Load rules: ' . $e->getMessage());
			return array();
		}
	}

	public static function getEventGroups() {
		$groups = array();
		foreach(AmzFulfillment_Entity_Event::values() as $event) {
			$group = $event->getGroup();
			if(!in_array($group, $groups)) {
				$groups[] = $group;
			}
		}
		return $groups;
	}

	public static function getEvents($group) {
		$events = array();
		foreach(AmzFulfillment_Entity_Event::values() as $event) {
			if($event->getGroup() == $group) {
				$events[] = $event;
			}
		}
		return $events;
	}

	public static function getActionGroups() {
		$groups = array();
		foreach(AmzFulfillment_Entity_Action::values() as $action) {
			$group = $action->getGroup();
			if(!in_array($group, $groups)) {
				$groups[] = $group;
			}
		}
		return $groups;
	}

	public static function getActions($group) {
		$actions = array();
		foreach(AmzFulfillment_Entity_Action::values() as $action) {
			if($action->getGroup() == $group) {
				$actions[] = $action;
			}
		}
		return $actions;
	}

	public function addRuleElement($selectedEvent, $selectedAction, $id = '') {
		$this->selectedEvent = $selectedEvent;
		$this->selectedAction = $selectedAction;
		$this->id = $id;
		AmzFulfillment_Template::load('RuleElement', $this);
	}
}
