<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
final class AmzFulfillment_Worker {
	const ID 						= 'amzFulfillmentWorkerRun';
	const SCHEDULE					= 'amzFulfillmentWorkerSchedule';
	const LOCK_FILE					= 'amzFulfillmentWork.lock';
	const SYNC_INVENTORY_TASK		= 0;
	const SYNC_FULFILLMENTS_TASK	= 1;
	const SYNC_PACKAGES_TASK		= 2;
	const SYNC_LISTINGS_TASK		= 3;

	private $tasks;

	public function __construct() {
		$this->tasks = array(
				self::SYNC_INVENTORY_TASK		=> new AmzFulfillment_Task_SyncInventoryTask(),
				self::SYNC_FULFILLMENTS_TASK	=> new AmzFulfillment_Task_SyncFulfillmentsTask(),
				self::SYNC_PACKAGES_TASK		=> new AmzFulfillment_Task_SyncPackagesTask(),
				self::SYNC_LISTINGS_TASK		=> new AmzFulfillment_Task_SyncListingsTask());
		AmzFulfillment_Main::instance()->schedule(self::ID, self::SCHEDULE);
		$scheduleTime = AmzFulfillment_Main::instance()->getScheduleTime(self::ID);
		if($scheduleTime !== FALSE) {
			AmzFulfillment_Logger::trace(sprintf('Next worker run scheduled in %d sec', $scheduleTime - time()));
		} else {
			AmzFulfillment_Logger::trace('No worker run scheduled');
		}
	}

	public function run() {
		$lock = NULL;
		$lockFile = WP_CONTENT_DIR . '/' . self::LOCK_FILE;
		if(!is_writable(dirname($lockFile))) {
			AmzFulfillment_Logger::warn(sprintf('Worker run without locking (No write permissions for %s)', WP_CONTENT_DIR));
		} else {
			$lock = new AmzFulfillment_ExclusiveLock($lockFile);
			if($lock->acquire()) {
				AmzFulfillment_Logger::info('Worker run');
			} else {
				AmzFulfillment_Logger::warn(sprintf('Failed to acquire %s: Worker is already running', $lockFile));
				return;
			}
		}
		try {
			AmzFulfillment_Main::instance()->featureController()->update();
			try {
				foreach(array_keys($this->tasks) as $taskId) {
					$this->runTask($taskId);
				}
			} catch (Exception $e) {
				AmzFulfillment_Logger::error('Uncaught exception while executing tasks: ' . $e->getMessage());
			}
			AmzFulfillment_Logger::info('All tasks done');
		} catch(Exception $e) {
			AmzFulfillment_Logger::error('Uncaught worker root exception: ' . $e->getMessage());
		}
		if($lock !== NULL) {
			$lock->release();
		}
	}

	public function runTask($taskId) {
		$this->tasks[$taskId]->runTask();
	}
}
