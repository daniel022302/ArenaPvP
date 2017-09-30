<?php
/**
 *
 * This file was created by Matt on 7/16/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\basefiles;


use pocketmine\scheduler\Task;
use sys\arenapvp\ArenaPvP;

abstract class ArenaTask extends Task {

	/** @var ArenaPvP */
	private $plugin;

	public function __construct(ArenaPvP $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	public function cancel() {
		$this->getPlugin()->getServer()->getScheduler()->cancelTask($this->getTaskId());
	}

}