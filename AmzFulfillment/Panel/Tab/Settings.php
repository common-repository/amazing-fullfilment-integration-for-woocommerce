<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Tab_Settings extends AmzFulfillment_Panel_Tab {
	const ID = 'Settings';
	const TITLE = 'Settings';
	const PRO = FALSE;

	const TEST_AMAZON_API_ACTION = 'testAmazonApi';
	const SAVE_ACTION = 'saveSettings';

	const ACCESS_KEY_PATTERN = "/^[a-z0-9]{16,128}$/i";
	const SECRET_ACCESS_KEY_PATTERN = "/^[a-z0-9\/\+=-_\.]{16,128}$/i";
	const MARKETPLACE_PATTERN = "/^[A-Z]{2}$/";
	const MERCHANT_ID_PATTERN = "/^[a-z0-9]{8,64}$/i";
	const SCHDULING_INTERVAL_MINUTES_MAX = 24 * 60;
	const SCHDULING_INTERVAL_MINUTES_MIN = 10;

	private $optionsRepo;
	private $options;

	public function __construct() {
		parent::__construct(self::ID, self::TITLE, self::PRO);
		$this->optionsRepo = AmzFulfillment_Main::instance()->data()->options();
		$this->options = $this->optionsRepo->options();
	}

	public function doActions() {
		if($this->hasAction(self::TEST_AMAZON_API_ACTION)) {
			$this->testAmazonApi();
		}
		if($this->hasAction(self::SAVE_ACTION)) {
			$this->save();
		}
	}

	private function testAmazonApi() {
		try {
			AmzFulfillment_Main::instance()->amazonMWS()->fbaOutbound()->listAllFulfillmentOrders();
			AmzFulfillment_Logger::info('Amazon MWS API test has succeeded');
		} catch(Exception $e) {
			AmzFulfillment_Logger::error('Amazon MWS API test failed. Please check your credentials.');
			AmzFulfillment_Logger::debug('Amazon MWS API test exception: ' . $e->getMessage());
		}
	}

	private function save() {
		if(!isset($_REQUEST['automation'])) {
			$_REQUEST['automation'] = FALSE;
		}
		if(!isset($_REQUEST['hold'])) {
			$_REQUEST['hold'] = FALSE;
		}
		$valid = TRUE;
		foreach($_REQUEST as $key => $value) {
			switch($key) {
				case 'accessKeyId':
					if($this->validate($key, $value)) {
						$this->options->setAccessKeyId($value);
					} else {
						$valid = FALSE;
					}
					break;
				case 'secretAccessKeyId':
					if($this->validate($key, $value)) {
						$this->options->setSecretAccessKeyId($value);
					} else {
						$valid = FALSE;
					}
					break;
				case 'marketplace':
					if($this->validate($key, $value)) {
						$this->options->setMarketplace($value);
					} else {
						$valid = FALSE;
					}
					break;
				case 'merchantId':
					if($this->validate($key, $value)) {
						$this->options->setMerchantId($value);
					} else {
						$valid = FALSE;
					}
					break;
				case 'hold':
					$this->options->setHold($value);
					break;
				case 'schedulingInterval':
					if($this->validate($key, $value)) {
						$this->options->setSchedulingInterval($value * 60);
					} else {
						$valid = FALSE;
					}
					break;
				case 'automation':
					$this->options->setAutomation($value);
					break;
				case 'page':
				case 'tab':
				case 'action':
				case self::SAVE_ACTION:
					break;
				default:
					AmzFulfillment_Logger::debug('Invalid settings key in request: ' . $key);
					break;
			}
		}
		if($valid) {
			$this->optionsRepo->set($this->options);
			AmzFulfillment_Logger::info('Settings saved');
			AmzFulfillment_Main::instance()->unschedule(AmzFulfillment_Worker::ID);
			AmzFulfillment_Main::instance()->schedule(AmzFulfillment_Worker::ID, AmzFulfillment_Worker::SCHEDULE);
		} else {
			AmzFulfillment_Logger::warn('Settings not saved');
		}
	}

	private  function validate($key, $value) {
		switch($key) {
			case 'accessKeyId':
				if(!preg_match(self::ACCESS_KEY_PATTERN, $value)) {
					AmzFulfillment_Logger::warn(__($key . ': Invalid value'));
					return FALSE;
				}
				return TRUE;
			case 'secretAccessKeyId':
				if(!preg_match(self::SECRET_ACCESS_KEY_PATTERN, $value)) {
					AmzFulfillment_Logger::warn(__($key . ': Invalid value'));
					return FALSE;
				}
				return TRUE;
			case 'marketplace':
				if(!preg_match(self::MARKETPLACE_PATTERN, $value)) {
					AmzFulfillment_Logger::warn(__($key . ': Invalid value'));
					return FALSE;
				}
				return TRUE;
			case 'merchantId':
				if(!preg_match(self::MERCHANT_ID_PATTERN, $value)) {
					AmzFulfillment_Logger::warn(__($key . ': Invalid value'));
					return FALSE;
				}
				return TRUE;
			case 'schedulingInterval':
				if((int) $value > self::SCHDULING_INTERVAL_MINUTES_MAX) {
					AmzFulfillment_Logger::warn(__(sprintf('Scheduling interval is set above maximum (%d minutes)', self::SCHDULING_INTERVAL_MINUTES_MAX)));
					return FALSE;
				} elseif((int) $value < self::SCHDULING_INTERVAL_MINUTES_MIN) {
					AmzFulfillment_Logger::warn(__(sprintf('Scheduling interval is set below minimum (%d minutes)', self::SCHDULING_INTERVAL_MINUTES_MIN)));
					return FALSE;
				}
				return TRUE;
		}
		return FALSE;
	}

	public function getOption($key, $default = '') {
		$a = $this->options->getAsArray();
		if(!isset($a[$key]) || $a[$key] === false) {
			return $default;
		} else {
			return $a[$key];
		}
	}

	public function getOptions() {
		return $this->options;
	}

	public function getEndpoints() {
		return AmzFulfillment_Amazon_MWS_Endpoints::getAll();
	}

	public function getMarketplaces($regionCode) {
		$marketplaces = array();
		foreach(AmzFulfillment_Amazon_Marketplaces::getAll() as $marketplace) {
			if($marketplace['regionCode'] == $regionCode) {
				$marketplaces[] = $marketplace;
			}
		}
		return $marketplaces;
	}
}
