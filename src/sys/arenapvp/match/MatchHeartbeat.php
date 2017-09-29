<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 7:23 PM
 */

namespace sys\arenapvp\match;


use pocketmine\scheduler\PluginTask;
use sys\arenapvp\ArenaPvP;

class MatchHeartbeat extends PluginTask {

	/** @var ArenaPvP */
	private $main;

	/** @var MatchManager */
	private $manager;

	public function __construct(ArenaPvP $owner, MatchManager $manager) {
		parent::__construct($owner);
		$owner->getServer()->getScheduler()->scheduleRepeatingTask($this, 20);
		$this->main = $owner;
		$this->manager = $manager;
	}

	/**
	 * Actions to execute when run
	 *
	 * @param $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick) {
		if (count($this->getMatchManager()->getMatches()) <= 0) return;

		$this->getMatchManager()->tickMatches();
	}

	public function getMatchManager() {
		return $this->manager;
	}

	public function getPlugin() {
		return $this->main;
	}
}