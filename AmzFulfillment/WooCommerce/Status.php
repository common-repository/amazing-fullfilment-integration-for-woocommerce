<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_WooCommerce_Status {
	const PENDING		= 'pending';
	const PROCESSING	= 'processing';
	const ONHOLD		= 'on-hold';
	const COMPLETED		= 'completed';
	const CANCELLED		= 'cancelled';
	const REFUNDED		= 'refunded';
	const FAILED		= 'failed';

	public static $orderStatus = array (
			self::PENDING		=> 'Pending payment',
			self::PROCESSING	=> 'Processing',
			self::ONHOLD		=> 'On hold',
			self::COMPLETED		=> 'Completed',
			self::CANCELLED		=> 'Cancelled',
			self::REFUNDED		=> 'Refunded',
			self::FAILED		=> 'Failed'
	);
}
