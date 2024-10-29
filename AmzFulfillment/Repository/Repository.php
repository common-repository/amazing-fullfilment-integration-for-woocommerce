<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once ABSPATH . '/wp-admin/includes/upgrade.php';

class AmzFulfillment_Repository_Repository {
	private $table;
	private $createQuery;
	private $cleanOnDeactivate;
	private $db;

	/**
	 * Construct
	 * 
	 * @param string $tableName
	 * @param string $createQuery
	 * @param bool $cleanOnDeactivate
	 */
	public function __construct($tableName, $createQuery, $cleanOnDeactivate) {
		global $wpdb;
		$this->db = $wpdb;
		$this->table = $this->db->prefix . $tableName;
		$this->createQuery = $this->makeQuery($createQuery);
		$this->cleanOnDeactivate = $cleanOnDeactivate;
	}

	/**
	 * getTable
	 * 
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * makeQuery
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function makeQuery($sql) {
		return str_replace('_TBL_', $this->table, $sql);
	}

	/**
	 * init
	 * 
	 * Create / update database table
	 */
	public function init() {
		if($this->createQuery === NULL) {
			return;
		}
		if($this->db->get_var($this->makeQuery("SHOW TABLES LIKE '_TBL_'")) != $this->table) {
			dbDelta(sprintf($this->createQuery, $this->table));
			AmzFulfillment_Logger::debug('Database table created: ' . $this->table);
		}
	}

	/**
	 * clean
	 * 
	 * Remove / cleanup database table
	 */
	public function clean() {
		if($this->cleanOnDeactivate) {
			$this->db->query($this->makeQuery("DROP TABLE IF EXISTS `_TBL_`"));
			AmzFulfillment_Logger::debug('Database table removed: ' . $this->table);
		} else {
			AmzFulfillment_Logger::debug('Database table keeped: ' . $this->table);
		}
	}

	/**
	 * setQuery
	 *
	 * @param string $sql
	 */
	public function setQuery($sql) {
		$this->db->query($this->makeQuery($sql));
	}

	/**
	 * getQuery
	 *
	 * @param string $sql
	 * @return array|object|NULL
	 */
	public function getQuery($sql) {
		return $this->db->get_results($this->makeQuery($sql));
	}
}
