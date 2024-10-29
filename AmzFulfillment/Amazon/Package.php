<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2017 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Amazon_Package {
	public static $locationInfo = array (
			'AS_INSTRUCTED'			=> 'As instructed',
			'CARPORT'				=> 'Carport',
			'CUSTOMER_PICKUP'		=> 'Picked up by customer',
			'DECK'					=> 'Deck',
			'DOOR_PERSON'			=> 'Resident',
			'FRONT_DESK'			=> 'Front desk',
			'FRONT_DOOR'			=> 'Front door',
			'GARAGE'				=> 'Garage',
			'GUARD'					=> 'Residential guard',
			'MAIL_ROOM'				=> 'Mail room',
			'MAIL_SLOT'				=> 'Mail slot',
			'MAILBOX'				=> 'Mailbox',
			'MC_BOY'				=> 'Delivered to male child',
			'MC_GIRL'				=> 'Delivered to female child',
			'MC_MAN'				=> 'Delivered to male adult',
			'MC_WOMAN'				=> 'Delivered to female adult',
			'NEIGHBOR'				=> 'Delivered to neighbor',
			'OFFICE'				=> 'Office',
			'OUTBUILDING'			=> 'Outbuilding',
			'PATIO'					=> 'Patio',
			'PORCH'					=> 'Porch',
			'REAR_DOOR'				=> 'Rear door',
			'RECEPTIONIST'			=> 'Receptionist',
			'RECEIVER'				=> 'Resident',
			'SECURE_LOCATION'		=> 'Secure location',
			'SIDE_DOOR'				=> 'Side door'
	);

	public static $status = array (
			'IN_TRANSIT'			=> 'In transit to the destination address',
			'DELIVERED'				=> 'Delivered to the destination address',
			'RETURNING'				=> 'In the process of being returned to Amazons fulfillment network',
			'RETURNED'				=> 'Returned to Amazons fulfillment network',
			'UNDELIVERABLE'			=> 'Undeliverable because package was lost or destroyed',
			'DELAYED'				=> 'Delayed',
			'AVAILABLE_FOR_PICKUP'	=> 'Available for pickup',
			'CUSTOMER_ACTION'		=> 'Requires customer action' 
	);

	public static $trackingEvent = array (
			'EVENT_101' 			=> 'Carrier notified to pick up package',
			'EVENT_102'				=> 'Shipment picked up from sellers facility',
			'EVENT_201'				=> 'Arrival scan',
			'EVENT_202'				=> 'Departure scan',
			'EVENT_203'				=> 'Arrived at destination country',
			'EVENT_204'				=> 'Initiated customs clearance process',
			'EVENT_205'				=> 'Completed customs clearance process',
			'EVENT_206'				=> 'In transit to pickup location',
			'EVENT_301'				=> 'Delivered',
			'EVENT_302'				=> 'Out for delivery',
			'EVENT_304'				=> 'Delivery attempted',
			'EVENT_306'				=> 'Customer contacted to arrange delivery',
			'EVENT_307'				=> 'Delivery appointment scheduled',
			'EVENT_308'				=> 'Available for pickup',
			'EVENT_309'				=> 'Returned to seller',
			'EVENT_401'				=> 'Held by carrier - incorrect address',
			'EVENT_402'				=> 'Customs clearance delay',
			'EVENT_403'				=> 'Customer moved',
			'EVENT_404'				=> 'Delay in delivery due to external factors',
			'EVENT_405'				=> 'Shipment damaged',
			'EVENT_406'				=> 'Held by carrier',
			'EVENT_407'				=> 'Customer refused delivery',
			'EVENT_408'				=> 'Returning to seller',
			'EVENT_409'				=> 'Lost by carrier',
			'EVENT_411'				=> 'Paperwork received - did not receive shipment',
			'EVENT_412'				=> 'Shipment received - did not receive paperwork',
			'EVENT_413'				=> 'Held by carrier - customer refused shipment due to customs charges',
			'EVENT_414'				=> 'Missorted by carrier',
			'EVENT_415'				=> 'Received from prior carrier',
			'EVENT_416'				=> 'Undeliverable',
			'EVENT_417'				=> 'Shipment missorted',
			'EVENT_418'				=> 'Shipment delayed',
			'EVENT_419'				=> 'Address corrected - delivery rescheduled' 
	);

	public static $fulfillmentShipmentStatus = array (
			'PENDING'				=> 'The process of picking units from inventory has begun',
			'SHIPPED'				=> 'All packages in the shipment have left the fulfillment center',
			'CANCELLED_BY_FULFILLER' => 'The Amazon fulfillment center could not fulfill the shipment as planned. This might be because the inventory was not at the expected location in the fulfillment center. After cancelling the fulfillment order, Amazon immediately creates a new fulfillment shipment and again attempts to fulfill the order',
			'CANCELLED_BY_SELLER'	=> 'The shipment was cancelled using the CancelFulfillmentOrder request' 
	);
}
