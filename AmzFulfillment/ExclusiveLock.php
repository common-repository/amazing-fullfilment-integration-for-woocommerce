<?php
/**
 * Amazing Fulfillment Integration for WooCommerce
 *
 * @author dejo-commerce
 * @copyright Copyright (c) 2018 dejo-commerce (https://www.dejo-commerce.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

final class AmzFulfillment_ExclusiveLock {
	const DEAD_LOCK_TIME = 30 * 60;

	protected $file = NULL;
	protected $handle = NULL;
	protected $lock = FALSE;

	public function __construct($file) {
		$this->file = $file;
	}

	public function __destruct() {
		if($this->lock == TRUE) {
			$this->unlock();
		}
	}

	public function acquire() {
		if(is_readable($this->file) && $this->isDeadlock()) {
			@unlink($this->file);
		}
		$this->handle = fopen($this->file, 'w+');
		if(!flock($this->handle, LOCK_EX | LOCK_NB)) {
			return FALSE;
		}
		ftruncate($this->handle, 0);
		fwrite($this->handle, time());
		fflush($this->handle);
		$this->lock = TRUE;
		return TRUE;
	}

	public function release() {
		if($this->lock) {
			if(!flock($this->handle, LOCK_UN)) {
				throw new Exception("Failed to release lock");
			}
			fclose($this->handle);
			$this->lock = FALSE;
			@unlink($this->file);
		}
	}

	public function isAcquired() {
		return $this->lock;
	}

	public function isDeadlock() {
		if(is_readable($this->file)) {
			$lockTime = (int) file_get_contents($this->file);
			if(time() > $lockTime + self::DEAD_LOCK_TIME) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
