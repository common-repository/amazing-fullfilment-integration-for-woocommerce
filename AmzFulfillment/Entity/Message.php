<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Entity_Message {
	const TEMPLATE = 'Message';
	const MAX_AGE = 180;

	private $level;
	private $caption;
	private $message;
	private $dismissable;
	private $dismissAction;
	private $createTime;

	public function __construct($level, $caption, $message = '') {
		$this->level = $level;
		$this->caption = $caption;
		$this->message = $message;
		$this->dismissable = true;
		$this->dismissAction = null;
		$this->createTime = time();
	}

	/**
	 * @return string
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @return string
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return boolean
	 */
	public function isDismissable() {
		return $this->dismissable;
	}

	/**
	 * @return string
	 */
	public function getDismissAction() {
		return $this->dismissAction;
	}

	/**
	 * @return boolean
	 */
	public function hasDismissAction() {
		return !empty($this->dismissAction);
	}

	/**
	 * @param string $level
	 */
	public function setLevel($level) {
		$this->level = $level;
	}

	/**
	 * @param string $caption
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return boolean
	 */
	public function hasMessage() {
		return !empty($this->message);
	}

	/**
	 * @param boolean $dismissable
	 */
	public function setDismissable($dismissable) {
		$this->dismissable = (boolean) $dismissable;
	}

	/**
	 * @param string $dismissAction
	 */
	public function setDismissAction($dismissAction) {
		$this->dismissAction = $dismissAction;
	}

	/**
	 * @return string
	 */
	public function getClasses() {
		$classes = 'notice';
		switch($this->level) {
			case AmzFulfillment_Logger::INFO:
				$classes .= ' notice-success';
				break;
			case AmzFulfillment_Logger::WARN:
				$classes .= ' notice-warning';
				break;
			case AmzFulfillment_Logger::ERROR:
				$classes .= ' notice-error';
				break;
		}
		if(!$this->hasDismissAction()) {
			$classes.= ' is-dismissible';
		}
		return $classes;
	}

	public function render() {
		if($this->createTime >= time() - self::MAX_AGE) {
			AmzFulfillment_Template::load(self::TEMPLATE, $this);
		}
	}
}
