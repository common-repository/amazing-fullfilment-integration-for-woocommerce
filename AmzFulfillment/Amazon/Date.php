<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_Date {
	/**
	 * toXmlDate
	 *
	 * Convert date / timestamp to XML date (format 2016-02-11T00:00:00-06:00) for amazon api
	 *
	 * @param string|int $time
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public static function toXmlDate($time) {
		if(is_string($time)) {
			$date = $time;
			$time = strtotime($date);
			if($time <= 0) {
				throw new InvalidArgumentException('Invalid time: ' . $date);
			}
		} elseif(is_integer($time)) {
			return date('Y-m-d', $time) . 'T' . date('H:i:sP', $time);
		} else {
			throw new InvalidArgumentException('Invalid time: ' . $time);
		}
	}

	/**
	 * toDate
	 *
	 * Convert XML date (format 2016-02-11T00:00:00-06:00)
	 *          to date (format 2016-02-11 00:00:00-06:00)
	 *
	 * @param string $xmlDate
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public static function toDate($xmlDate) {
		if(!is_string($xmlDate) || strlen($xmlDate) < 19) {
			throw new InvalidArgumentException('Invalid time: ' . $xmlDate);
		}
		return str_replace('T', ' ', substr($xmlDate, 0, 19));
	}
}
