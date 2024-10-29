<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Panel {
	const MENU_TITLE = 'Amazing Fulfillment';
	const MENU_PERMISSION = 'manage_options';
	const MENU_ID = 'amzFulfillment';
	const MAIN_TEMPLATE = 'AmzFulfillment';

	private $tabs;
	private $activeTabId;
	private $loaded;

	public function __construct() {
		$this->loaded = FALSE;
		$this->tabs = array();
	}

	public function enqueueRessources() {
		wp_enqueue_style('jquery-ui',				'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		wp_enqueue_style('datatables.min',			'https://cdn.datatables.net/v/dt/dt-1.10.15/datatables.min.css');
		wp_enqueue_style('amzFulfillment',			AMZFULFILLMENT_PLUGIN_URL . '/assets/css/amzFulfillment.css');
		wp_enqueue_script('jquery-1.12.4',			'https://code.jquery.com/jquery-1.12.4.min.js');
		wp_enqueue_script('jquery-ui',				'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js');
		wp_enqueue_script('datatables.min',			'https://cdn.datatables.net/v/dt/dt-1.10.15/datatables.min.js');
		wp_enqueue_script('amzFulfillment',			AMZFULFILLMENT_PLUGIN_URL . '/assets/js/amzFulfillment.js');
		wp_enqueue_script('amzFulfillmentData',		AMZFULFILLMENT_PLUGIN_URL . '/assets/js/amzFulfillmentData.js');
		wp_localize_script('amzFulfillmentData',	'amzFulfillmentLogs', array('ajax_url' => admin_url('admin-ajax.php')));
	}

	public function load() {
		add_submenu_page(AmzFulfillment_WooCommerce_Panel::MENU_ID, self::MENU_TITLE, self::MENU_TITLE, self::MENU_PERMISSION, self::MENU_ID, array($this, 'show'));
		if(empty($this->tabs)) {
			try {
				$this->tabs = array(
						new AmzFulfillment_Panel_Tab_Settings(),
						new AmzFulfillment_Panel_Tab_Inventory(),
						new AmzFulfillment_Panel_Tab_Logs(),
						new AmzFulfillment_Panel_Tab_Automation(),
						new AmzFulfillment_Panel_Tab_License());
			} catch(Exception $e) {
				AmzFulfillment_Logger::error(sprintf("Uncaught plugin panel exception in %s:%d : %s", $e->getFile(), $e->getLine(), $e->getMessage()));
			}
		}
		$this->loaded = true;
	}

	public function show() {
		try {
			$this->activeTabId = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : $this->tabs[0]->getId();
			$this->getTab($this->activeTabId)->doActions();
			AmzFulfillment_Main::instance()->messages()->show();
			AmzFulfillment_Template::load(self::MAIN_TEMPLATE, $this);
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf("Tab %s: %s", $this->activeTabId, $e->getMessage()));
		}
	}

	public function isLoaded() {
		return $this->loaded;
	}

	public function getActiveTab() {
		return $this->getTab($this->activeTabId);
	}

	public function getTab($id) {
		foreach($this->tabs as $tab) {
			if($tab->getId() == $id) {
				return $tab;
			}
		}
		AmzFulfillment_Main::instance()->messages()->error("Invalid tab: " . $id);
		return $this->tabs[0];
	}

	public function getTabs() {
		return $this->tabs;
	}

	public function isActiveTab($tabId) {
		return $this->activeTabId == $tabId;
	}

	/**
	 * @param string $action
	 * @param string $tab
	 * @return string
	 */
	public static function getUrl($action = NULL, $tab = NULL) {
		$url = '/wp-admin/admin.php?page=amzFulfillment&tab=';
		if($tab !== NULL) {
			$url .= $tab;
		} elseif(isset($_REQUEST['tab'])) {
			$url .= $_REQUEST['tab'];
		} else {
			$url .= AmzFulfillment_Panel_Tab_Settings::ID;
		}
		if($action !== NULL) {
			$url .= "&action=";
			$url .= $action;
		}
		return $url;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public static function getUrlWithArg($key, $value = NULL) {
		$url = $_SERVER['REQUEST_URI'];
		if(strpos($url, '?') !== false) {
			$url .= '&';
		} else {
			$url .= '?';
		}
		$url .= urlencode($key);
		if($value !== NULL) {
			$url .= '=' . urlencode($value);
		}
		return $url;
	}
}
