<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Controller_AjaxTableDataController {
	const FULFILLMENTS_DATASET	= 'amzFulfillmentFulfillments';
	const LOGS_DATASET 			= 'amzFulfillmentLogs';

	private $db;
	private $dataSet;
	private $fulfillmentRepository = NULL;
	private $logRepository = NULL;
	private $packageRepository = NULL;
	private $query;
	private $draw = 0;
	private $search = NULL;
	private $orderColumn = NULL;
	private $orderDirection = 'ASC';
	private $table;
	private $columns = array();
	private $start = 0;
	private $length = 15;
	private $recordsTotal = 0;
	private $recordsFiltered = 0;
	private $rows = array();

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->fulfillmentRepository = new AmzFulfillment_Repository_FulfillmentRepository();
		$this->logRepository = new AmzFulfillment_Repository_LogRepository();
		$this->packageRepository = new AmzFulfillment_Repository_PackageRepository();
	}

	public function getFulfillmentsData() {
		$this->processRequest(self::FULFILLMENTS_DATASET);
		$q = "SELECT SQL_CALC_FOUND_ROWS
				post.post_date AS orderTime,
				fulfillment.orderId,
				post.post_status AS orderStatus,
				fulfillmentStatus,
				fulfillmentTime
			FROM `%s` fulfillment
			LEFT JOIN `%sposts` post ON post.ID = fulfillment.orderId
			%s %s %s";
		$this->query = sprintf($q, $this->table, $this->db->prefix, $this->getQueryWhere(), $this->getQueryOrder(), $this->getQueryLimit());
		$results = $this->db->get_results($this->query);
		$this->calculateRecords();
		$this->rows = array();
		foreach($results as $result) {
			$orderId = $result->orderId;
			$orderTime = AmzFulfillment_Main::instance()->getFormatedDateTime($result->orderTime);
			if(!empty($result->orderStatus)) {
				$orderStatus = '<mark class="amzfulfillment-status"><span>' . ucfirst(str_replace('wc-', '', $result->orderStatus)) . '</span></mark>';
			} else {
				$orderStatus = '';
			}
			$fulfillmentTime = AmzFulfillment_Main::instance()->getFormatedDateTime($result->fulfillmentTime);
			if(!empty($result->fulfillmentStatus)) {
				$fulfillmentStatus = '<mark class="amzfulfillment-status"><span>' . ucfirst(strtolower($result->fulfillmentStatus)) . '</span></mark>';
			} else {
				$fulfillmentStatus = '';
			}
			$packages = array();
			foreach($this->packageRepository->getByOrder($orderId) as $num => $package) {
				$status = ucfirst(str_replace('_', ' ', strtolower($package->getStatus())));
				$estimatedArrivalTime = AmzFulfillment_Main::instance()->getFormatedDate($package->getEstimatedArrivalTime());
				if($package->getStatus() != "DELIVERED" && !empty($package->getEstimatedArrivalTime())) {
					$packages[] = sprintf('<mark class="amzfulfillment-status"><span>Package-%d %s &#40;Estimated %s&#41;</span></mark>', $num + 1, $status, $estimatedArrivalTime);
				} else {
					$packages[] = sprintf('<mark class="amzfulfillment-status"><span>Package-%d %s</span></mark>', $num + 1, $status);
				}
			}
			$this->rows[] = array(
					'orderTime'			=> $orderTime,
					'orderId'			=> $orderId,
					'orderStatus'		=> $orderStatus,
					'fulfillmentTime'	=> $fulfillmentTime,
					'fulfillmentStatus'	=> $fulfillmentStatus,
					'packages'			=> implode($packages));
		}
		$this->sendJsonResponse();
	}

	public function getLogsData() {
		$this->processRequest(self::LOGS_DATASET);
		$this->query = sprintf("SELECT SQL_CALC_FOUND_ROWS %s FROM `%s` %s %s %s",
				$this->getQueryColumns(),
				$this->table,
				$this->getQueryWhere(),
				$this->getQueryOrder(),
				$this->getQueryLimit());
		$results = $this->db->get_results($this->query);
		$this->calculateRecords();
		$this->rows = array();
		foreach($results as $result) {
			$this->rows[] = array(
					'time'		=> AmzFulfillment_Main::instance()->getFormatedDateTime($result->logTime),
					'message'	=> $result->logMessage
			);
		}
		$this->sendJsonResponse();
	}

	protected function sendJsonResponse() {
		wp_send_json(array(
				"draw"				=> $this->draw,
				"recordsTotal"		=> $this->recordsTotal,
				"recordsFiltered"	=> $this->recordsFiltered,
				"data" 				=> $this->rows));
	}

	protected function processRequest($dataSet) {
		$this->dataSet = $dataSet;
		switch($this->dataSet) {
			case self::FULFILLMENTS_DATASET:
				$this->table = $this->fulfillmentRepository->getTable();
				$this->columns = array('orderTime', 'orderId', 'orderStatus', 'fulfillmentTime', 'fulfillmentStatus');
				break;
			case self::LOGS_DATASET:
				$this->table = $this->logRepository->getTable();
				$this->columns = array('logTime', 'logMessage');
				break;
			default:
				AmzFulfillment_Logger::error("Invalid ajax table data source: " . $data);
				return;
		}
		if(isset($_REQUEST['draw'])) {
			$this->draw = (int) $_REQUEST['draw'];
		}
		if(isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
			$this->search = esc_sql($_REQUEST['search']['value']);
		}
		if(isset($_REQUEST['order'][0]['column'])) {
			$columnNum = $_REQUEST['order'][0]['column'];
			if(isset($this->columns[$columnNum])) {
				$this->orderColumn = $this->columns[$columnNum];
			}
		}
		if(isset($_REQUEST['order'][0]['dir'])) {
			$this->orderDirection = strtoupper($_REQUEST['order'][0]['dir']);
		}
		if(isset($_REQUEST['start'])) {
			$this->start = (int) $_REQUEST['start'];
		}
		if(isset($_REQUEST['length'])) {
			$this->length = (int) $_REQUEST['length'];
		}
	}

	protected function getQueryColumns() {
		$c = array();
		foreach($this->columns as $column) {
			$c[] = '`' . $column . '`';
		}
		return implode(',', $c);
	}

	protected function getQueryWhere() {
		if($this->search === NULL) {
			return "";
		}
		switch($this->dataSet) {
			case self::FULFILLMENTS_DATASET:
				return sprintf("WHERE (post.post_date LIKE '%%%s%%' OR fulfillment.orderId LIKE '%%%s%%' OR post.post_status LIKE '%%%s%%' OR fulfillmentStatus LIKE '%%%s%%' OR fulfillmentTime LIKE '%%%s%%')", $this->search, $this->search, $this->search, $this->search, $this->search);
			case self::LOGS_DATASET:
				return sprintf("WHERE `logMessage` LIKE '%%%s%%'", $this->search);
		}
	}

	protected function getQueryOrder() {
		if($this->orderColumn === NULL) {
			return "";
		} else {
			return sprintf(" ORDER BY `%s` %s", $this->orderColumn, $this->orderDirection);
		}
	}

	protected function getQueryLimit() {
		if($this->start > 0) {
			return sprintf('LIMIT %d, %d', $this->start, $this->length);
		} else {
			return sprintf('LIMIT %d', $this->length);
		}
	}

	protected function calculateRecords() {
		if($this->search !== NULL) {
			$result = $this->db->get_results("SELECT FOUND_ROWS() as filteredRows");
			$this->recordsFiltered = (int) $result[0]->filteredRows;
			$result = $this->db->get_results(sprintf("SELECT COUNT(*) as `count` FROM `%s`", $this->table));
			$this->recordsTotal = (int) $result[0]->count;
		} else {
			$result = $this->db->get_results(sprintf("SELECT COUNT(*) as `count` FROM `%s`", $this->table));
			$this->recordsFiltered = (int) $result[0]->count;
			$this->recordsTotal = (int) $result[0]->count;
		}
	}
}
