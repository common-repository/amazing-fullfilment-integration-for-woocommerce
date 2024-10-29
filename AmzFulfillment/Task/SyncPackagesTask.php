<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Task_SyncPackagesTask extends AmzFulfillment_Task_Task {
	const PACKAGE_MAX_UPDATE_AGE = 30 * 24 * HOUR_IN_SECONDS;

	private $packageRepo;
	private $fulfillmentsRepo;
	private $fulfillmentController;
	private $packageController;
	private $totalCount;
	private $updateCount;
	private $newCount;

	protected function run() {
		$this->totalCount = 0;
		$this->updateCount = 0;
		$this->newCount = 0;
		if(!AmzFulfillment_Main::instance()->featureController()->hasFulfillments()) {
			AmzFulfillment_Logger::debug(__CLASS__ . " - Packages sync skipped: Missing feature");
			return;
		}
		$this->packageRepo = AmzFulfillment_Main::instance()->data()->packages();
		$this->fulfillmentsRepo = AmzFulfillment_Main::instance()->data()->fulfillments();
		$this->fulfillmentController = AmzFulfillment_Main::instance()->fulfillmentController();
		$this->packageController = AmzFulfillment_Main::instance()->packageController();
		$fulfillments = $this->fulfillmentsRepo->getAllOpen(time() - self::PACKAGE_MAX_UPDATE_AGE);
		foreach($fulfillments as $fulfillment) {
			$orderId = $fulfillment->getOrderId();
			if(!AmzFulfillment_Main::instance()->wooCommerce()->orders()->exist($orderId)) {
				AmzFulfillment_Logger::debug(sprintf('%s - Ignoring Amazon fulfillment package for non existent WooCommerceOrder-%d', __CLASS__, $orderId));
				continue;
			}
			AmzFulfillment_Logger::debug(sprintf("%s - WooCommerceOrder-%d processing", __CLASS__, $orderId));
			$this->update($orderId);
			$this->find($orderId);
			$this->closeComplete($orderId);
		}
		AmzFulfillment_Logger::debug(sprintf("%s - %d packages (%d updated and %d new)", __CLASS__, $this->totalCount, $this->updateCount, $this->newCount));
	}

	protected function find($orderId) {
		$count = $this->packageController->find($orderId);
		$this->newCount += $count;
		$this->totalCount += $count;
	}

	protected function update($orderId) {
		foreach($this->packageRepo->getByOrder($orderId) as $package) {
			if($package->getStatus() == "DELIVERED") {
				continue;
			}
			AmzFulfillment_Logger::trace(sprintf("%s - WooCommerceOrder-%d processing package-%s", __CLASS__, $package->getOrderId(), $package->getPackageNumber()));
			if($this->packageController->update($package->getPackageNumber(), $package->getOrderId())) {
				$this->updateCount++;
			}
			$this->totalCount++;
		}
	}

	protected function closeComplete($orderId) {
		$fulfillment = $this->fulfillmentsRepo->get($orderId);
		if($fulfillment->getStatus() === "COMPLETE") {
			foreach($this->packageRepo->getByOrder($orderId) as $package) {
				if($package->getStatus() === 'IN_TRANSIT') {
					AmzFulfillment_Logger::info(sprintf("%s - WooCommerceOrder-%d package-%d status 'DELIVERED' (fulfillment complete)", __CLASS__, $orderId, $package->getPackageNumber()));
					$package->setStatus('DELIVERED');
					$this->packageRepo->set($package);
				}
			}
		}
	}
}
