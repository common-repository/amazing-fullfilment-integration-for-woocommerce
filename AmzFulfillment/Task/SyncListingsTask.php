<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Task_SyncListingsTask extends AmzFulfillment_Task_Task {
	const OPTIONS_KEY = 'amzFulfillmentListingsTime';
	const LISTINGS_TTL = 8 * 60 * 60;
	const MAX_WAIT = 4 * 60;
	const REPORT_TYPE = '_GET_MERCHANT_LISTINGS_DATA_';

	protected function run() {
		$ttl = get_option(self::OPTIONS_KEY, 0) + self::LISTINGS_TTL - time();
		AmzFulfillment_Logger::debug(sprintf('%s - listings ttl %d', __CLASS__, $ttl));
		if($ttl >= 0) {
			return;
		}
		try {
			AmzFulfillment_Logger::debug(sprintf('%s - Request report', __CLASS__));
			$response = AmzFulfillment_Main::instance()->amazonMWS()->marketplace()->requestReport(self::REPORT_TYPE);
			$reportId = $this->waitReport($response->getReportRequestInfo()->getReportRequestId());
			if($reportId !== null) {
				AmzFulfillment_Logger::debug(sprintf('%s - Request ready', __CLASS__));
				$report = AmzFulfillment_Main::instance()->amazonMWS()->marketplace()->getReport($reportId);
				$listings = array();
				for($i = 1; $i < count($report); $i++) {
					$sku = $report[$i][3];
					$asin = $report[$i][16];
					$name = mb_convert_encoding($report[$i][0], "UTF-8");
					$listing = new AmzFulfillment_Entity_Listing($sku, $asin, $name);
					$listings[] = $listing;
					AmzFulfillment_Logger::debug(sprintf('%s - Listing: %s %s %s', __CLASS__, $listing->getSku(), $listing->getAsin(), $listing->getName()));
				}
				AmzFulfillment_Main::instance()->data()->listings()->set($listings);
				AmzFulfillment_Logger::debug(sprintf('%s - %d amazon listings', __CLASS__, count($listings)));
			}
			update_option(self::OPTIONS_KEY, time());
		} catch(Exception $e) {
			AmzFulfillment_Logger::error(sprintf('%s - Failed to get report: %s', __CLASS__, $e->getMessage()));
		}
	}

	private function waitReport($requestId) {
		$maxTime = time() + self::MAX_WAIT;
		while(time() < $maxTime) {
			sleep(20);
			$result = AmzFulfillment_Main::instance()->amazonMWS()->marketplace()->getReportRequestList(self::REPORT_TYPE);
			$requestInfos = $result->getReportRequestInfoList();
			foreach($requestInfos as $requestInfo) {
				if($requestInfo->getReportRequestId() == $requestId && $requestInfo->getReportProcessingStatus() == '_DONE_') {
					return $requestInfo->getGeneratedReportId();
				}
			}
		}
		throw new Exception('Timeout');
	}
}
