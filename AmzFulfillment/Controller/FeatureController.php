<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_FeatureController {
	const CHECK_INTERVAL = 24 * 60 * 60;

	const NONE = 'none';
	const EVALUATION = 'eval';
	const FULL = 'full';

	private static $api = "https://www.amazing-fulfillment.com/?wc-api=software-api";
	private static $productIds = array(
			self::EVALUATION => 'AmzFulfillmentEval',
			self::FULL => 'AmzFulfillment');
	private $options;

	public function __construct() {
		$this->options = AmzFulfillment_Main::instance()->data()->options();
	}

	/**
	 * @return boolean
	 */
	public function hasAutomation() {
		if($this->hasPro() || $this->hasEval()) {
			if($this->isExpired()) {
				AmzFulfillment_Logger::trace("AMZF031 - License expired: " . date("c", $this->getExpireTime()));
				return FALSE;
			} elseif(!$this->isValid()) {
				AmzFulfillment_Logger::error("AMZF032 - License validation failed");
				return FALSE;
			} else {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return boolean
	 */
	public function hasSupport() {
		if($this->hasPro() || $this->hasEval()) {
			if($this->isExpired()) {
				AmzFulfillment_Logger::trace("AMZF033 - License expired: " . date("c", $this->getExpireTime()));
				return FALSE;
			} elseif(!$this->isValid()) {
				AmzFulfillment_Logger::error("AMZF034 - License validation failed");
				return FALSE;
			} else {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return boolean
	 */
	public function hasFulfillments() {
		if($this->hasPro() || $this->hasEval()) {
			if($this->isExpired()) {
				AmzFulfillment_Logger::trace("AMZF035 - License expired: " . date("c", $this->getExpireTime()));
				return FALSE;
			} elseif(!$this->isValid()) {
				AmzFulfillment_Logger::error("AMZF036 - License validation failed");
				return FALSE;
			} else {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return boolean
	 */
	public function hasPro() {
		return $this->getLicenseType() == self::FULL;
	}

	/**
	 * @return boolean
	 */
	public function hasEval() {
		return $this->getLicenseType() == self::EVALUATION;
	}

	/**
	 * @return boolean
	 */
	public function hasNone() {
		return $this->getLicenseType() == self::NONE;
	}

	/**
	 * @return boolean
	 */
	public function isExpired() {
		return time() > $this->getExpireTime();
	}

	/**
	 * @return boolean
	 */
	public function isCheckTime() {
		return $this->getCheckTime() + self::CHECK_INTERVAL < time();
	}

	/**
	 * @return NULL|string
	 */
	public function getKey() {
		return $this->options->options()->getLicenseKey();
	}

	/**
	 * @return NULL|string
	 */
	public function getEmail() {
		return $this->options->options()->getLicenseEmail();
	}

	/**
	 * @return number
	 */
	private function getCheckTime() {
		$time = $this->options->options()->getLicenseCheckTime();
		return ($time === NULL || empty($time)) ? PHP_INT_MIN : $time;
	}

	public function isValid() {
		return $this->options->options()->getLicenseValid();
	}

	private function getInstance() {
		return $this->options->options()->getLicenseInstance();
	}

	/**
	 * @return number
	 */
	public function getExpireTime() {
		$time = $this->options->options()->getLicenseExpireTime();
		return ($time === NULL || empty($time)) ? PHP_INT_MIN : $time;
	}

	public function getLicenseType() {
		if(empty($this->getKey()) || empty($this->getEmail()) || empty($this->getCheckTime())) {
			return self::NONE;
		} elseif(preg_match("/^EVAL-[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i", $this->getKey())) {
			return self::EVALUATION;
		} elseif(preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i", $this->getKey())) {
			return self::FULL;
		} else {
			AmzFulfillment_Logger::debug("Invalid license setup");
			return self::NONE;
		}
	}

	/**
	 * @return string
	 */
	private function getProductId($type) {
		if(!isset(self::$productIds[$type])) {
			throw new Exception(sprintf("AMZF040 - Unknown product '%s'", $type));
		}
		return self::$productIds[$type];
	}

	private function setLicense($key, $email, $expireTime, $instance) {
		$options = $this->options->options();
		$options->setLicenseKey($key);
		$options->setLicenseEmail($email);
		$options->setLicenseExpireTime($expireTime);
		$options->setLicenseInstance($instance);
		$this->options->set($options);
		AmzFulfillment_Logger::debug(sprintf("Set license %s %s %s %d", $key, $email, date('c', $expireTime), $instance));
	}

	private function setCheckTime($checkTime) {
		$options = $this->options->options();
		$options->setLicenseCheckTime($checkTime);
		$this->options->set($options);
		AmzFulfillment_Logger::debug(sprintf("Set last check time to %s", date('c', $checkTime)));
	}

	private function setValid($valid) {
		$options = $this->options->options();
		if($options->getLicenseValid() != $valid) {
			$options->setLicenseValid($valid);
			$this->options->set($options);
			AmzFulfillment_Logger::debug(sprintf("Set %s", $valid ? "valid" : "invalid"));
		}
	}

	public function update() {
		if($this->hasNone()) {
			AmzFulfillment_Logger::trace("No license to validate");
			return;
		}
		if($this->isCheckTime()) {
			AmzFulfillment_Logger::debug(sprintf("Check license validity TYPE=%s KEY=%s EMAIL=%s CHECKTIME=%s EXPIRETIME=%s",
					$this->getLicenseType(), $this->getKey(), $this->getEmail(), date('c', $this->getCheckTime()), date('c', $this->getExpireTime())));
			AmzFulfillment_Logger::info("License check scheduled");
			try {
				$this->check();
				$this->setValid(TRUE);
				$this->setCheckTime(time());
				AmzFulfillment_Logger::info("License check succeeded");
			} catch(Exception $e) {
				$this->setValid(FALSE);
				AmzFulfillment_Logger::error("License check failed: " . $e->getMessage());
			}
		}
	}

	/**
	 * @param string $key
	 * @param string $email
	 * @param boolean $eval
	 * @throws Exception
	 */
	public function activate($key, $email, $eval = false) {
		$activateType = $eval ? self::EVALUATION : self::FULL;
		if($this->getLicenseType() == self::FULL) {
			throw new Exception("AMZF010 - Already activated");
		}
		if($this->getLicenseType() == self::EVALUATION && $eval) {
			throw new Exception("AMZF011 - Evaluation license was already activated");
		}
		$response = $this->processRequest(array(
				'request'		=> 'activation',
				'license_key'	=> $key,
				'email'			=> $email,
				'product_id'	=> $this->getProductId($activateType)));
		if(!isset($response->activated) || $response->activated !== TRUE) {
			throw new Exception("AMZF012 - Activation was not confirmed");
		}
		if(!isset($response->activated) || !$response->activated) {
			throw new Exception("AMZF013 - Activation was rejected");
		}
		if($eval) {
			$expireTime = time() + (31 * 24 * 60 * 60);
		} else {
			$expireTime = PHP_INT_MAX;
		}
		$instance = $response->instance;
		$this->setLicense($key, $email, $expireTime, $instance);
		$this->setCheckTime(time());
		$this->setValid(TRUE);
		AmzFulfillment_Logger::info("License activation succeeded");
	}

	/**
	 * @throws Exception
	 */
	public function deactivate() {
		if(empty($this->getKey()) || empty($this->getEmail())) {
			throw new Exception("AMZF020 - No key / email for deactivation");
		}
		if($this->getLicenseType() == self::EVALUATION && $eval) {
			throw new Exception("AMZF021 - Evaluation license deactivation is not allowed");
		}
		$response = $this->processRequest(array(
				'request'		=> 'deactivation',
				'license_key'	=> $this->getKey(),
				'email'			=> $this->getEmail(),
				'product_id'	=> $this->getProductId($this->getLicenseType()),
				'instance'		=> $this->getInstance()));
		if(!isset($response->reset) || !$response->reset) {
			throw new Exception("AMZF022 - Deactivation was rejected");
		}
		$this->setLicense(NULL, NULL, NULL, 0);
		$this->setValid(FALSE);
		AmzFulfillment_Logger::info("License deactivation succeeded");
	}

	private function check() {
		$this->processRequest(array(
				'request'		=> 'check',
				'license_key'	=> $this->getKey(),
				'email'			=> $this->getEmail(),
				'product_id'	=> $this->getProductId($this->getLicenseType())));
	}

	/**
	 * @param string[] $requestArgs
	 * @throws Exception
	 * @return object
	 */
	private function processRequest($requestArgs) {
		$request = sprintf('%s&%s', self::$api, http_build_query($requestArgs));
		AmzFulfillment_Logger::debug(sprintf("AMZF001 - Software request '%s'", $request));
		$response = wp_remote_get($request);
		if(!is_array($response) || !isset($response['body'])) {
			throw new Exception(sprintf("AMZF002 - Software request '%s' failed: Result not readable", $requestArgs['request']));
		}
		$responseCode = (int) wp_remote_retrieve_response_code($response);
		if($responseCode >= 400) {
			throw new Exception(sprintf("AMZF003 - Software request '%s' failed: Http request failed with %d", $requestArgs['request'], $responseCode));
		}
		$responseBody = wp_remote_retrieve_body($response);
		AmzFulfillment_Logger::trace("Software service raw response: " . $responseBody);
		$responseObject = json_decode($responseBody);
		if($responseObject === NULL) {
			throw new Exception(sprintf("AMZF004 - Software request '%s' failed: Unable to read json object", $requestArgs['request']));
		}
		if(isset($responseObject->error)) {
			throw new Exception(sprintf("AMZF005 - Software request '%s' failed: (%d) %s", $requestArgs['request'], $responseObject->code, $responseObject->error));
		}
		if(isset($responseObject->message) && !empty($responseObject->message)) {
			AmzFulfillment_Logger::info($responseObject->message);
		}
		return $responseObject;
	}

	public function getExpireMessage() {
		if($this->getLicenseType() != self::EVALUATION) {
			return "";
		} elseif($this->isExpired()) {
			return "";
		} else {
			$expire = (new DateTime())->setTimestamp($this->getExpireTime());
			$now = new DateTime();
			$diff = $now->diff($expire, TRUE);
			return sprintf("%d days and %d hours", $diff->days, $diff->i);
		}
	}
}
