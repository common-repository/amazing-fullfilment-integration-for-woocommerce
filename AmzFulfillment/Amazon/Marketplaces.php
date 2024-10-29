<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2017 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_Marketplaces {
	const CA = array('countryCode' => 'CA', 'regionCode' => 'NA', 'name' => 'Canada',         'id' => 'A2EUQ1WTGCTBG2');
	const MX = array('countryCode' => 'MX', 'regionCode' => 'NA', 'name' => 'Mexico',         'id' => 'A1AM78C64UM0Y8');
	const US = array('countryCode' => 'US', 'regionCode' => 'NA', 'name' => 'USA',            'id' => 'ATVPDKIKX0DER');
	const BR = array('countryCode' => 'BR', 'regionCode' => 'BR', 'name' => 'Brazil',         'id' => 'A2Q3Y263D00KWC');
	const DE = array('countryCode' => 'DE', 'regionCode' => 'EU', 'name' => 'Germany',        'id' => 'A1PA6795UKMFR9');
	const ES = array('countryCode' => 'ES', 'regionCode' => 'EU', 'name' => 'Spain',          'id' => 'A1RKKUPIHCS9HS');
	const FR = array('countryCode' => 'FR', 'regionCode' => 'EU', 'name' => 'France',         'id' => 'A13V1IB3VIYZZH');
	const IT = array('countryCode' => 'IT', 'regionCode' => 'EU', 'name' => 'Italy',          'id' => 'APJ6JRA9NG5V4');
	const UK = array('countryCode' => 'UK', 'regionCode' => 'EU', 'name' => 'United Kingdom', 'id' => 'A1F83G8C2ARO7P');
	const IN = array('countryCode' => 'IN', 'regionCode' => 'IN', 'name' => 'India',          'id' => 'A21TJRUUN4KGV');
	const CN = array('countryCode' => 'CN', 'regionCode' => 'CN', 'name' => 'China',          'id' => 'AAHKV2X7AFYLW');
	const JP = array('countryCode' => 'JP', 'regionCode' => 'JP', 'name' => 'Japan',          'id' => 'A1VC38T7YXB528');

	public static function getAll() {
		return array(self::CA, self::MX, self::US, self::BR, self::DE, self::ES, self::FR, self::IT, self::UK, self::IN, self::CN, self::JP);
	}

	public static function getByRegion($regionCode) {
		$marketplaces = array();
		foreach(AmzFulfillment_Amazon_MWS_Endpoints::getAll() as $marketplace) {
			if($marketplace['regionCode'] == $regionCode) {
				$marketplaces[$countryCode] = $marketplace;
			}
		}
		if(empty($marketplaces)) {
			throw new Exception('Invalid region code: ' . $regionCode);
		}
		return $marketplaces;
	}

	public static function getByCountryCode($countryCode) {
		foreach(self::getAll() as $marketplace) {
			if($marketplace['countryCode'] == $countryCode) {
				return $marketplace;
			}
		}
		throw new Exception('Invalid country code: ' . $countryCode);
	}
}
