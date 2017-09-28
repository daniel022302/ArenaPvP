<?php
/**
 *
 * This file was created by Matt on 7/22/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp;


class Multiplier {

	/** @var int */
	private $count;

	/** @var float */
	private $multiplier;

	public function __construct(float $multiplier, int $count = 0) {
		$this->count = $count;
		$this->multiplier = $multiplier;
	}

	/**
	 * @return int
	 */
	public function getCount(): int {
		return $this->count;
	}

	/**
	 * @return mixed
	 */
	public function getMultiplier(): float {
		return $this->multiplier;
	}

	/**
	 * @param int $count
	 */
	public function setCount(int $count) {
		$this->count = $count;
	}

}