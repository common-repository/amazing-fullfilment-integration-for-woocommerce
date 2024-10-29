<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
final class AmzFulfillment_Main {
	const EVALUATION_MESSAGE_CLOSE_KEY = 'amzEvalMsgClose';
	const EVALUATION_MESSAGE_CLOSE_TIME = 60 * 60;

	private static $_instance = NULL;
	private $panel;
	private $messages = NULL;
	private $data;
	private $fulfillmentController = NULL;
	private $packageController = NULL;
	private $featureController = NULL;
	private $sessionController;
	private $eventController;
	private $amazonMWS = NULL;
	private $wooCommerce = NULL;
	private $ajaxTableDataController;
	private $worker = NULL;
	private $dateTimeFormat = NULL;
	private $dateFormat = NULL;

	private function __construct() {
		$this->panel = new AmzFulfillment_Panel_Panel();
		$this->sessionController = new AmzFulfillment_Controller_SessionController();
		$this->data = new AmzFulfillment_Controller_DataController();
		$this->eventController = new AmzFulfillment_Controller_EventController();
		$this->wooCommerce = new AmzFulfillment_WooCommerce_WooCommerce();
		$this->ajaxTableDataController = new AmzFulfillment_Controller_AjaxTableDataController();
	}

	public function init() {
		add_action('admin_menu',							array($this->panel,						'load'));
		add_action('admin_enqueue_scripts',					array($this->panel,						'enqueueRessources'));
		add_action('admin_notices',							array($this,							'evaluationNotice'), 11);
		add_action('admin_notices',							array($this->messages(),				'show'), 12);
		add_filter('cron_schedules',						array($this,							'registerSchedules'));
		add_action(AmzFulfillment_Worker::ID,				array($this->worker(),					'run'));
		add_action('woocommerce_order_status_changed',		array($this->eventController(),			'woocommerceOrderStatusChange'));
		add_filter('woocommerce_email_classes',				array($this->wooCommerce(),				'registerEmailTemplates'));
		add_action('wp_ajax_amzFulfillmentFulfillments',	array($this->ajaxTableDataController,	'getFulfillmentsData'));
		add_action('wp_ajax_amzFulfillmentLogs',			array($this->ajaxTableDataController,	'getLogsData'));
	}

	/**
	 * @return AmzFulfillment_Main
	 */
	public static function instance() {
		try {
			if(null === self::$_instance) {
				self::$_instance = new self;
			}
			return self::$_instance;
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf("Uncaught plugin exception in %s:%d : %s", $e->getFile(), $e->getLine(), $e->getMessage()));
		}
	}

	/**
	 * @return AmzFulfillment_Panel_Panel
	 */
	public function panel() {
		return $this->panel;
	}

	/**
	 * @return AmzFulfillment_Panel_Messages
	 */
	public function messages() {
		if($this->messages == NULL) {
			$this->messages = new AmzFulfillment_Panel_Messages();
		}
		return $this->messages;
	}

	/**
	 * @return boolean
	 */
	public function hasPanel() {
		return $this->panel->isLoaded();
	}

	/**
	 * @return AmzFulfillment_Controller_DataController
	 */
	public function data() {
		return $this->data;
	}
	
	/**
	 * @return AmzFulfillment_Controller_FulfillmentController
	 */
	public function fulfillmentController() {
		if($this->fulfillmentController === NULL) {
			$this->fulfillmentController = new AmzFulfillment_Controller_FulfillmentController();
		}
		return $this->fulfillmentController;
	}

	/**
	 * @return AmzFulfillment_Controller_PackageController
	 */
	public function packageController() {
		if($this->packageController === NULL) {
			$this->packageController = new AmzFulfillment_Controller_PackageController();
		}
		return $this->packageController;
	}

	/**
	 * @return AmzFulfillment_Controller_FeatureController
	 */
	public function featureController() {
		if($this->featureController === NULL) {
			$this->featureController = new AmzFulfillment_Controller_FeatureController();
		}
		return $this->featureController;
	}

	/**
	 * @return AmzFulfillment_Controller_EventController
	 */
	public function eventController() {
		return $this->eventController;
	}

	/**
	 * @return AmzFulfillment_Controller_SessionController
	 */
	public function sessionController() {
		return $this->sessionController;
	}

	/**
	 * @throws Exception
	 * @return AmzFulfillment_Amazon_MWS_Client
	 */
	public function amazonMWS() {
		if($this->amazonMWS === NULL) {
			$options = $this->data()->options()->get();
			if(!$options->hasAmazonCredentials()) {
				throw new Exception("Failed to access amazon MWS api: No credentials configured");
			}
			$this->amazonMWS = new AmzFulfillment_Amazon_MWS_Client(
					$options->getMerchantId(),
					$options->getAccessKeyId(),
					$options->getSecretAccessKeyId(),
					$options->getMarketplace(),
					self::getPluginName(),
					self::getPluginVersion());
		}
		return $this->amazonMWS;
	}

	/**
	 * @return AmzFulfillment_WooCommerce_WooCommerce
	 */
	public function wooCommerce() {
		if($this->wooCommerce === NULL) {
			$this->wooCommerce = new AmzFulfillment_WooCommerce_WooCommerce();
		}
		return $this->wooCommerce;
	}

	/**
	 * @return AmzFulfillment_Worker
	 */
	public function worker() {
		if($this->worker === NULL) {
			$this->worker = new AmzFulfillment_Worker();
		}
		return $this->worker;
	}

	/**
	 * @param string $subject
	 * @param string $message
	 */
	public function sendNotification($subject, $message) {
		$adminEmail = get_option('admin_email', false);
		if(!$adminEmail) {
			return;
		}
		if(wp_mail($adminEmail, "[AmzFulfillment] " . $subject, $message)) {
			AmzFulfillment_Logger::debug($adminEmail . ' notified by email');
		} else {
			AmzFulfillment_Logger::debug('Failed to send email notification to ' . $adminEmail);
		}
	}

	/**
	 * @param string|integer $date
	 * @return string
	 */
	public function getFormatedDateTime($date) {
		if($this->dateTimeFormat === NULL) {
			$this->dateTimeFormat = sprintf("%s %s", get_option('date_format'), get_option('time_format'));
		}
		if(is_integer($date)) {
			$date = date('Y-m-d H:i:s', $date);
		}
		return get_date_from_gmt($date, $this->dateTimeFormat);
	}

	/**
	 * @param string|integer $date
	 * @return string
	 */
	public function getFormatedDate($date) {
		if($this->dateFormat === NULL) {
			$this->dateFormat = get_option('date_format');
		}
		if(is_integer($date)) {
			$date = date('Y-m-d H:i:s', $date);
		}
		return get_date_from_gmt($date, $this->dateFormat);
	}

	public function evaluationNotice() {
		if($this->featureController->getLicenseType() !== AmzFulfillment_Controller_FeatureController::EVALUATION) {
			return;
		}
		if(array_key_exists(self::EVALUATION_MESSAGE_CLOSE_KEY, $_REQUEST) || in_array(self::EVALUATION_MESSAGE_CLOSE_KEY, $_REQUEST)) {
			$this->sessionController->set(self::EVALUATION_MESSAGE_CLOSE_KEY, time());
		}
		$close = $this->sessionController->get(self::EVALUATION_MESSAGE_CLOSE_KEY);
		if(is_int($close) && time() < $close + self::EVALUATION_MESSAGE_CLOSE_TIME) {
			return;
		}
		if($this->featureController->isExpired()) {
			$message = "Evaluation license is expired.";
		} else {
			$message = sprintf("Your evaluation period ends in <b>%s</b>.", $this->featureController()->getExpireMessage());
		}
		$message .= sprintf(' Visit the <a href="%s">license page</a> for upgrade</p>', AmzFulfillment_Panel_Panel::getUrl(NULL, AmzFulfillment_Panel_Tab_License::ID));
		$m = new AmzFulfillment_Entity_Message(AmzFulfillment_Logger::WARN, 'Amazing Fulfillment Integration for WooCommerce', $message);
		$m->setDismissAction(AmzFulfillment_Panel_Panel::getUrlWithArg(self::EVALUATION_MESSAGE_CLOSE_KEY));
		$m->render();
	}

	public static function getPluginName() {
		return AMZFULFILLMENT_PLUGIN_ID;
	}

	public static function getPluginVersion() {
		$pluginDir = get_plugins('/' . plugin_basename(dirname(AMZFULFILLMENT_PLUGIN_FILE)));
		$pluginFile = basename(AMZFULFILLMENT_PLUGIN_FILE);
		return $pluginDir[$pluginFile]['Version'];
	}

	public function clearCrons() {
		AmzFulfillment_Logger::debug('Clear schedules');
		$this->unschedule(AmzFulfillment_Worker::ID);
		wp_clear_scheduled_hook(AmzFulfillment_Worker::ID);
	}

	/**
	 * @param array[] $schedules
	 * @return array[]
	 */
	public function registerSchedules($schedules) {
		$schedules[AmzFulfillment_Worker::SCHEDULE] = array(
				'interval' => $this->data->options()->options()->getSchedulingInterval(),
				'display'  => 'Amazing Fulfillment Integration for WooCommerce worker schedule');
		return $schedules;
	}

	/**
	 * @param string $id
	 * @return false|number
	 */
	public function getScheduleTime($id) {
		return wp_next_scheduled($id);
	}

	/**
	 * @param string $id
	 * @param string $schedule
	 */
	public function schedule($id, $schedule) {
		if($this->getScheduleTime($id) === FALSE) {
			if(wp_schedule_event(time(), $schedule, $id) === false) {
				AmzFulfillment_Logger::warn('Failed to schedule ' . $id);
			} else {
				AmzFulfillment_Logger::debug('Scheduled ' . $id);
			}
		}
	}

	/**
	 * @param string $id
	 */
	public function unschedule($id) {
		$scheduleTime = wp_next_scheduled($id);
		if ($scheduleTime !== FALSE) {
			if(wp_unschedule_event($scheduleTime, $id) === FALSE) {
				AmzFulfillment_Logger::warn('Failed to unschedule ' . $id);
			} else {
				AmzFulfillment_Logger::debug('Unscheduled ' . $id);
			}
		}
	}

	/**
	 * @param string $email
	 * @return boolean
	 */
	public static function isValidEmail($email) {
		return (boolean) preg_match("/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}$/", $email);
	}
}
