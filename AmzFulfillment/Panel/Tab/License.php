<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Tab_License extends AmzFulfillment_Panel_Tab {
	const ID = 'License';
	const TITLE = 'License';
	const PRO = FALSE;

	const PRODUCT_FULL_URL = "https://www.amazing-fulfillment.com/produkt/amazing-fulfillment-plugin/";
	const PRODUCT_EVAL_URL = "https://www.amazing-fulfillment.com/produkt/amazing-fulfillment-plugin-evaluation/";

	const SETUP_PRO_ACTION = 'setupPro';
	const ACTIVATE_ACTION = 'licenseActivate';
	const DEACTIVATE_ACTION = 'licenseDeactivate';

	private $featureController;

	public function __construct() {
		parent::__construct(self::ID, self::TITLE, self::PRO);
		$this->featureController = AmzFulfillment_Main::instance()->featureController();
	}

	public function doActions() {
		if($this->hasAction(self::ACTIVATE_ACTION)) {
			$this->activate();
		} elseif($this->hasAction(self::DEACTIVATE_ACTION)) {
			$this->deactivate();
		}
	}

	private function activate() {
		$error = FALSE;
		if(!isset($_REQUEST['key']) || empty($_REQUEST['key'])) {
			AmzFulfillment_Logger::error("Key is required");
			$error = true;
		}
		if(!isset($_REQUEST['email']) || empty($_REQUEST['email'])) {
			AmzFulfillment_Logger::error("Email address is required");
			$error = true;
		}
		if($error) {
			return;
		}
		try {
			if(stripos($_REQUEST['key'], "EVAL") === 0) {
				$this->featureController->activate(trim($_REQUEST['key']), trim($_REQUEST['email']), TRUE);
			} else {
				$this->featureController->activate(trim($_REQUEST['key']), trim($_REQUEST['email']), FALSE);
			}
		} catch(Exception $e) {
			AmzFulfillment_Logger::error("Failed to activate: " . $e->getMessage());
		}
	}

	private function deactivate() {
		try {
			$this->featureController->deactivate();
		} catch(Exception $e) {
			AmzFulfillment_Logger::error("Failed to deactivate: " . $e->getMessage());
		}
	}

	public function hasLicense() {
		return $this->featureController->hasEval() || $this->featureController->hasPro();
	}

	public function getLicenseType() {
		return $this->featureController->getLicenseType();
	}

	public function getLicenseText() {
		switch($this->featureController->getLicenseType()) {
			case AmzFulfillment_Controller_FeatureController::NONE:
				return "";
			case AmzFulfillment_Controller_FeatureController::EVALUATION:
				return __("Evaluation - All features");
			case AmzFulfillment_Controller_FeatureController::FULL:
				return __("Pro - All features");
		}
	}

	public function isExpired() {
		return $this->featureController->isExpired();
	}

	public function getValidityText() {
		if(!$this->featureController->isValid()) {
			return "!Invalid! Contact support";
		}
		switch($this->featureController->getLicenseType()) {
			case AmzFulfillment_Controller_FeatureController::NONE:
				return "";
			case AmzFulfillment_Controller_FeatureController::EVALUATION:
				if($this->featureController->isExpired()) {
					return "Expired";
				} else {
					return AmzFulfillment_Main::instance()->getFormatedDateTime($this->featureController->getExpireTime());
				}
			case AmzFulfillment_Controller_FeatureController::FULL:
				return "Active subscription";
		}
	}

	public function getExpireMessage() {
		return $this->featureController->getExpireMessage();
	}
}
