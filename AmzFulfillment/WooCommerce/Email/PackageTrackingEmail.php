<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

if(!class_exists("WC_Email")) {
	require_once WP_PLUGIN_DIR . '/woocommerce/includes/emails/class-wc-email.php';
}
if(!class_exists("Emogrifier")) {
	require_once WP_PLUGIN_DIR . '/woocommerce/includes/libraries/class-emogrifier.php';
}

class AmzFulfillment_WooCommerce_Email_PackageTrackingEmail extends WC_Email {
	const TITLE = 'Amazon Fulfillment package tracking';
	const DESCRIPTION = 'Amazing Fulfillment Integration for WooCommerce package tracking notification';
	const EMAIL_HEADING = '{site_title} package tracking';
	const EMAIL_SUBJECT = '{site_title} package tracking';

	private $package;
	private $order;

	public function __construct() {
		$this->id					= AmzFulfillment_WooCommerce_Email_Emails::PACKAGE_TRACKING;
		$this->title				= self::TITLE;
		$this->description			= self::DESCRIPTION;
		$this->subject				= apply_filters($this->id . '_default_subject', self::EMAIL_SUBJECT);
		$this->heading				= apply_filters($this->id . '_default_heading', self::EMAIL_HEADING);
		$this->template_base		= __DIR__ . "/";
		$this->template_html		= 'Template/PackageTrackingEmailHtml.php';
		$this->template_order_html	= 'Template/OrderHtml.php';
		$this->template_plain		= 'Template/PackageTrackingEmailPlain.php';
		$this->template_order_plain	= 'Template/OrderPlain.php';
		$this->package				= NULL;
		$this->order				= NULL;
		if(empty($this->get_option("recipient"))) {
			$this->recipient = NULL;
			$this->customer_email = true;
		} else {
			$this->recipient = $this->get_option('recipient');
			$this->customer_email = false;
		}
		$this->setPlaceholders();
		add_action($this->id, array($this, 'trigger'), 10, 2);
		parent::__construct();
	}

	/**
	 * @param string $orderId
	 * @param integer $packageNumber
	 */
	public function trigger($orderId, $packageNumber) {
		if(!$this->is_enabled()) {
			return;
		}
		$this->setup_locale();
		$this->sendMail($orderId, $packageNumber);
		$this->restore_locale();
		$this->package = NULL;
		$this->object = NULL;
		$this->order = NULL;
	}

	private function sendMail($orderId, $packageNumber) {
		$this->order = new WC_Order($orderId);
		$this->object = $this->order;
		if(!is_a($this->order, 'WC_Order')) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d package-%d email notification: invalid order', $orderId, $packageNumber));
			return;
		}
		$this->package = AmzFulfillment_Main::instance()->data()->packages()->get($packageNumber);
		if($this->package === NULL) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d package-%d email notification: invalid package', $orderId, $packageNumber));
			return;
		}
		if($this->is_customer_email()) {
			$this->recipient = $this->order->get_billing_email();
		}
		if(empty($this->get_recipient())) {
			AmzFulfillment_Logger::error(sprintf('WooCommerceOrder-%d package-%d email notification: No recipient', $orderId, $packageNumber));
			return;
		}
		$this->setPlaceholders();
		$result = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
		$result = true;
		if(!$result) {
			AmzFulfillment_Logger::warn(sprintf("WooCommerceOrder-%d package-%d email notification: Failed to send notification to '%s'", $orderId, $packageNumber, $this->get_recipient()));
			return;
		}
		AmzFulfillment_Logger::info(sprintf('WooCommerceOrder-%d package-%d email notification send to %s', $orderId, $packageNumber, $this->get_recipient()));
		if(empty($this->get_option("recipient"))) {
			$this->order->add_order_note(sprintf('%s email send to customer', $this->title));
		} else {
			$this->order->add_order_note(sprintf('%s email send to %s', $this->title, $this->get_recipient()));
		}
	}

	public function get_content_html() {
		$content = "";
		$content .= wc_get_template_html('emails/email-header.php', $this->getContentArgs());
		$content .= $this->format_string(wc_get_template_html($this->template_html, array_merge($this->getContentArgs(), $this->getArgs()), '', __DIR__ . '/'));
		$content .= wc_get_template_html($this->template_order_html, array_merge($this->getContentArgs(), $this->getArgs()), '', __DIR__ . '/');
		$content .= wc_get_template_html('emails/email-footer.php', $this->getContentArgs());
		return $content;
	}

	public function get_content_plain() {
		$content = "";
		$content .= $this->format_string(wc_get_template_html($this->template_plain, array_merge($this->getContentArgs(), $this->getArgs()), '', __DIR__ . '/'));
		$content .= wc_get_template_html($this->template_order_plain, array_merge($this->getContentArgs(), $this->getArgs()), '', __DIR__ . '/');
		return $content;
	}

	protected function setPlaceholders() {
		$this->placeholders = array();
		foreach($this->getArgs() as $key => $value) {
			$this->placeholders['{' . $key . '}'] = $value;
		}
	}

	protected function getArgs() {
		$args = array(
				'site_title'				=> $this->get_blogname(),
				'order'						=> $this->object,
				'order_date'				=> '',
				'order_number'				=> '',
				'tracking_number'			=> '',
				'carrier_code'				=> '',
				'carrier_name'				=> '',
				'estimated_arrival_time'	=> '',
				'tracking_text'				=> $this->get_option('tracking_text'),
				'carrier_text'				=> $this->get_option('carrier_text'),
				'tracking_number_text'		=> $this->get_option('tracking_number_text'),
				'estimated_arrival_text'	=> $this->get_option('estimated_arrival_text')
		);
		if(is_a($this->order, 'WC_Order')) {
			$args['order_date']				= wc_format_datetime($this->order->get_date_created()->getTimestamp());
			$args['order_number']			= $this->order->get_order_number();
		}
		if($this->package !== NULL) {
			$args['tracking_number']		= $this->package->getTrackingNumber();
			$args['carrier_code']			= $this->package->getCarrierCode();
			$args['carrier_name']			= AmzFulfillment_Amazon_Carrier::name($this->package->getCarrierCode());
			$args['estimated_arrival_time']	= AmzFulfillment_Main::instance()->getFormatedDate($this->package->getEstimatedArrivalTime());
		}
		return $args;
	}

	protected function getContentArgs() {
		return array(
				'order'			=> $this->object,
				'items'			=> $this->object->get_items(),
				'email_heading'	=> $this->get_heading(),
				'sent_to_admin'	=> false,
				'plain_text'	=> $this->get_email_type() === 'plain' ? TRUE : FALSE,
				'email'			=> $this
		);
	}

	public function init_form_fields() {
		parent::init_form_fields();
		$description = sprintf(__('Available placeholders: %s', 'woocommerce'), '<code>' . implode( '</code>, <code>', array_keys($this->placeholders)) . '</code>');
		$this->form_fields['recipient'] = array(
				'title'			=> __('Recipient(s)', 'woocommerce'),
				'type'			=> 'text',
				'desc_tip'		=> true,
				'description'	=> 'Enter recipients (comma separated) for this email. Keep empty (default) for customer notifications.',
				'placeholder'	=> '',
				'default'		=> '',
		);
		$this->form_fields['tracking_text'] = array(
				'title'			=> 'Tracking text',
				'type'			=> 'text',
				'desc_tip'		=> true,
				'description'	=> $description,
				'default'		=> 'Your order has been shipped and is in the hands of the carrier.'
		);
		$this->form_fields['carrier_text'] = array(
				'title'			=> 'Carrier text',
				'type'			=> 'text',
				'desc_tip'		=> true,
				'description'	=> $description,
				'default'		=> 'Carrier'
		);
		$this->form_fields['tracking_number_text'] = array(
				'title'			=> 'Tracking number text',
				'type'			=> 'text',
				'desc_tip'		=> true,
				'description'	=> $description,
				'default'		=> 'Tracking number'
		);
		$this->form_fields['estimated_arrival_text'] = array(
				'title'			=> 'Estimated arrival text',
				'type'			=> 'text',
				'desc_tip'		=> true,
				'description'	=> $description,
				'default'		=> 'Estimated arrival'
		);
	}
}
