<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Panel_Tab_Logs extends AmzFulfillment_Panel_Tab {
	const ID = 'Logs';
	const TITLE = 'Logs';
	const PRO = FALSE;

	public function __construct() {
		parent::__construct(self::ID, self::TITLE, self::PRO);
	}

	public function doActions() {
		return;
	}
}
