<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
abstract class AmzFulfillment_Panel_Tab {
	private $id;
	private $title;
	private $pro;

	public function __construct($id, $title, $pro) {
		$this->id = $id;
		$this->title = $title;
		$this->pro = $pro;
	}

	public function show() {
		AmzFulfillment_Template::load($this->id . 'Tab', $this);
	}

	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function isPro() {
		return $this->pro;
	}

	public abstract function doActions();

	public function hasAction($name) {
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == $name) {
			return TRUE;
		} elseif(isset($_REQUEST[$name])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getResource($file) {
		return AMZFULFILLMENT_PLUGIN_URL . '/assets/' . $file;
	}
}
