<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 2/23/2017
 * Time: 6:11 PM
 */

namespace sys\arenapvp\task;


use sys\arenapvp\arena\Arena;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\ParadoxTask;
use sys\arenapvp\kit\Kit;
use sys\arenapvp\match\Match;
use sys\arenapvp\match\TeamMatch;

class MatchTask extends ParadoxTask {

	/** @var ArenaPlayer[] */
    private $players = [];

    /** @var bool */
    private $teams = false;

    /** @var Kit */
    private $kit = null;

    /** @var Arena */
    private $arena = null;

    /** @var bool */
    private $ranked = false;

	/**
	 * MatchTask constructor.
	 * @param ArenaPvP $plugin
	 * @param ArenaPlayer[] $players
	 * @param Kit $kit
	 * @param Arena $arena
	 * @param bool $teams
	 * @param bool $ranked
	 */
    public function __construct(ArenaPvP $plugin, array $players, Kit $kit, Arena $arena, bool $teams = false, bool $ranked = false) {
        parent::__construct($plugin);
	    $plugin->getServer()->getScheduler()->scheduleDelayedTask($this, 60);
	    $arena->setInUse();
	    $this->players = $players;
        $this->kit = $kit;
        $this->teams = $teams;
        $this->arena = $arena;
        $this->ranked = $ranked;
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick) {
    	if($this->teams) {
		    $match = new TeamMatch($this->getPlugin(), $this->kit, $this->players, $this->arena, $this->ranked);
	    } else {
		    $match = new Match($this->getPlugin(), $this->kit, $this->players, $this->arena, $this->ranked);
	    }
	    $this->getPlugin()->getMatchManager()->addMatch($match);
    }
}