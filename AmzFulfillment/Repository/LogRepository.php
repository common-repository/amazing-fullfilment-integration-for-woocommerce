<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Repository_LogRepository extends AmzFulfillment_Repository_Repository {
	const TABLE = 'amzFulfillment_log';

	public function __construct() {
		$create = "
				CREATE TABLE _TBL_ (
					`logId`      INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`logTime`    DATETIME     NOT NULL,
					`logMessage` VARCHAR(255) NOT NULL)";
		parent::__construct(SELF::TABLE, $create, FALSE);
	}

	/**
	 * @param unknown $msg
	 */
	public function add($msg) {
		$this->setQuery(sprintf("INSERT INTO _TBL_ (logTime,logMessage) VALUES('%s','%s')", date('Y-m-d H:i:s'), esc_sql($msg)));
	}

	/**
	 * @param int $limit|NULL
	 * @return AmzFulfillment_Entity_Log[]
	 */
	public function get($limit = NULL) {
		$query = "SELECT * FROM _TBL_ ORDER BY logTime DESC";
		if($limit !== NULL) {
			$query .= sprintf(" LIMIT %d", $limit);
		}
		$logs = array();
		foreach($this->getQuery($query) as $row) {
			$logs[] = new AmzFulfillment_Entity_Log($row->logId, $row->logTime, $row->logMessage);
		}
		return $logs;
	}
}
