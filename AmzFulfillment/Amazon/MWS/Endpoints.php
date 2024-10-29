<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2017 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_MWS_Endpoints {
	public static $values = array(
			array('regionCode' => 'NA', 'name' => 'North America',	'url' => 'https://mws.amazonservices.com'),
			array('regionCode' => 'BR', 'name' => 'Brazil',			'url' => 'https://mws.amazonservices.com'),
			array('regionCode' => 'EU', 'name' => 'EU',				'url' => 'https://mws-eu.amazonservices.com'),
			array('regionCode' => 'IN', 'name' => 'India',			'url' => 'https://mws.amazonservices.in'),
			array('regionCode' => 'CN', 'name' => 'China',			'url' => 'https://mws.amazonservices.com.cn'),
			array('regionCode' => 'JP', 'name' => 'Japan',			'url' => 'https://mws.amazonservices.jp'));

	/**
	 * @return string[]
	 */
	public static function getAll() {
		return self::$values;
	}

	/**
	 * @param string $regionCode
	 * @throws Exception
	 * @return array
	 */
	public static function get($regionCode) {
		foreach(self::getAll() as $endpoint) {
			if($endpoint['regionCode'] == $regionCode) {
				return $endpoint;
			}
		}
		throw new Exception('Invalid region code: ' . $regionCode);
	}
}
