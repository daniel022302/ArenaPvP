<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 2/23/2017
 * Time: 6:11 PM
 */

namespace sys\arenapvp\task;


use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\ParadoxTask;
use sys\arenapvp\match\Match;

class DeathTask extends ParadoxTask {

	/** @var Match */
    private $match;

	/**
	 * MatchTask constructor.
	 * @param ArenaPvP $plugin
	 * @param Match $match
	 */
    public function __construct(ArenaPvP $plugin, Match $match) {
        parent::__construct($plugin);
	    $plugin->getServer()->getScheduler()->scheduleDelayedTask($this, 60);
        $this->match = $match;
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick) {
	    $this->match->kill();
    }
}