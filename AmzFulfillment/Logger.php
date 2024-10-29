<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
require_once ABSPATH . '/wp-admin/includes/plugin.php';

class AmzFulfillment_Logger {
	const ERROR   = 'ERROR';
	const WARN    = 'WARN';
	const INFO    = 'INFO';
	const DEBUG   = 'DEBUG';
	const TRACE   = 'TRACE';

	public static function error($msg) {
		self::log(self::ERROR, $msg);
	}

	public static function warn($msg) {
		self::log(self::WARN, $msg);
	}

	public static function info($msg) {
		self::log(self::INFO, $msg);
	}

	public static function debug($msg) {
		self::log(self::DEBUG, $msg);
	}

	public static function trace($msg) {
		self::log(self::TRACE, $msg);
	}

	private static function log($level, $msg) {
		$loggingLevel = self::loggingLevel();
		if(is_array($msg) || is_object($msg)) {
			$msg = print_r($msg, true);
		}
		switch($level) {
			case self::ERROR:
				if($loggingLevel == self::ERROR || $loggingLevel == self::WARN || $loggingLevel == self::INFO || $loggingLevel == self::DEBUG || $loggingLevel == self::TRACE) {
					self::logDb($level, $msg);
					self::logPanel($level, $msg);
					self::logWordpress($level, $msg);
				}
				break;
			case self::WARN:
				if($loggingLevel == self::WARN || $loggingLevel == self::INFO || $loggingLevel == self::DEBUG || $loggingLevel == self::TRACE) {
					self::logDb($level, $msg);
					self::logPanel($level, $msg);
					self::logWordpress($level, $msg);
				}
				break;
			case self::INFO:
				if($loggingLevel == self::INFO || $loggingLevel == self::DEBUG || $loggingLevel == self::TRACE) {
					self::logDb('', $msg);
					self::logPanel($level, $msg);
					self::logWordpress($level, $msg);
				}
				break;
			case self::DEBUG:
				if($loggingLevel == self::DEBUG || $loggingLevel == self::TRACE) {
					self::logDb($level, $msg);
					self::logWordpress($level, $msg);
				}
				break;
			case self::TRACE:
				if($loggingLevel == self::TRACE) {
					self::logDb($level, $msg);
					self::logWordpress($level, $msg);
				}
				break;
		}
	}

	private static function logDb($level, $msg) {
		try {
			AmzFulfillment_Main::instance()->data()->logs()->add(trim($level . ' ' . $msg));
		} catch(Exception $e) {
			return;
		}
	}

	private static function logWordpress($level, $msg) {
		try {
			if(!self::isWordpressDebugEnabled()) {
				return;
			}
			$loggerName = str_replace('.php', '', basename(AMZFULFILLMENT_PLUGIN_FILE));
			error_log(sprintf('%s [ %-5s ] %s', $loggerName, $level, $msg));
		} catch(Exception $e) {
			return;
		}
	}

	private static function logPanel($level, $msg) {
		try {
			AmzFulfillment_Main::instance()->messages()->add($level, $msg);
		} catch(Exception $e) {
			error_log($e);
		}
	}

	private static function isWordpressDebugEnabled() {
		return defined('WP_DEBUG') && WP_DEBUG === true;
	}

	private static function loggingLevel() {
		$loggingLevel = NULL;
		if(defined('AMZFULFILLMENT_LOGLEVEL')) {
			$loggingLevel = strtoupper(AMZFULFILLMENT_LOGLEVEL);
		}
		if($loggingLevel == self::ERROR || $loggingLevel == self::WARN || $loggingLevel == self::INFO || $loggingLevel == self::DEBUG || $loggingLevel == self::TRACE) {
			return $loggingLevel;
		} else {
			return self::INFO;
		}
	}
}
