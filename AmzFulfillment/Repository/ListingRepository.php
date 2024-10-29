<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
class AmzFulfillment_Repository_ListingRepository extends AmzFulfillment_Repository_Repository {
	const TABLE = 'amzFulfillment_listing';

	public function __construct() {
		$create = "
				CREATE TABLE _TBL_ (
					`listingId`            INT          NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
					`sku`                  VARCHAR(16)  NOT NULL UNIQUE DEFAULT '',
					`asin`                 VARCHAR(16)  NOT NULL        DEFAULT '',
					`name`                 VARCHAR(255) NOT NULL        DEFAULT '')";
		parent::__construct(SELF::TABLE, $create, TRUE);
	}

	/**
	 * @param AmzFulfillment_Entity_Listing[] $listings
	 */
	public function set($listings) {
		$this->setQuery("TRUNCATE TABLE _TBL_");
		foreach($listings as $listing) {
			$query = "INSERT INTO _TBL_ (sku,asin,name) VALUES('%s','%s','%s')";
			$query = sprintf($query, esc_sql($listing->getSku()), esc_sql($listing->getAsin()), esc_sql($listing->getName()));
			$this->setQuery($query);
		}
	}

	/**
	 * @return AmzFulfillment_Entity_Listing[]
	 */
	public function getAll() {
		$results = $this->getQuery("SELECT * FROM _TBL_ ORDER BY `name` ASC");
		$listings = array();
		foreach($results as $result) {
			$listings[] = new AmzFulfillment_Entity_Listing($result->sku, $result->asin, $result->name);
		}
		return $listings;
	}

	public function getByAsin($asin) {
		$results = $this->getQuery(sprintf("SELECT * FROM _TBL_ WHERE `asin`='%s'", esc_sql($asin)));
		$listings = array();
		foreach($results as $result) {
			$listings[] = new AmzFulfillment_Entity_Listing($result->sku, $result->asin, $result->name);
		}
		return $listings;
	}

	public function getBySku($sku) {
		$results = $this->getQuery(sprintf("SELECT * FROM _TBL_ WHERE `sku`='%s'", esc_sql($sku)));
		$listings = array();
		foreach($results as $result) {
			$listings[] = new AmzFulfillment_Entity_Listing($result->sku, $result->asin, $result->name);
		}
		return $listings;
	}
}
