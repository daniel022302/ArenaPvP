<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 2/23/2017
 * Time: 6:11 PM
 */

namespace sys\arenapvp\task;


use pocketmine\scheduler\PluginTask;
use sys\arenapvp\ArenaPvP;

class QueueTask extends PluginTask {

    private $plugin;

    /**
     * MatchTask constructor.
     * @param ArenaPvP $owner
     */
    public function __construct(ArenaPvP $owner) {
        parent::__construct($owner);
        $owner->getServer()->getScheduler()->scheduleRepeatingTask($this, 20);
        $this->plugin = $owner;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick) {
        $this->getPlugin()->getQueueManager()->checkQueue();
    }
}