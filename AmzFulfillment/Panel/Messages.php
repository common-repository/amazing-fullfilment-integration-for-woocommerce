<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Messages {
	const KEY = 'messages';

	public function __construct() {
	}

	public function info($msg) {
		$this->add(AmzFulfillment_Logger::INFO, $msg);
	}

	public function warn($msg) {
		$this->add(AmzFulfillment_Logger::WARN, $msg);
	}

	public function error($msg) {
		$this->add(AmzFulfillment_Logger::ERROR, $msg);
	}

	public function add($level, $msg) {
		AmzFulfillment_Main::instance()->sessionController()->add(self::KEY, new AmzFulfillment_Entity_Message($level, $msg));
	}

	public function show() {
		$messages = AmzFulfillment_Main::instance()->sessionController()->get(self::KEY);
		if(is_array($messages)) {
			foreach($messages as $message) {
				if(is_a($message, 'AmzFulfillment_Entity_Message')) {
					$message->render();
				}
			}
		}
		$this->clean();
	}

	public function clean() {
		AmzFulfillment_Main::instance()->sessionController()->set(self::KEY, array());
	}
}
