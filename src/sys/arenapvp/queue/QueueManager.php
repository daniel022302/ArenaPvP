<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/13/2017
 * Time: 5:21 PM
 */

namespace sys\arenapvp\queue;


use sys\arenapvp\ArenaPvP;
use sys\arenapvp\kit\Kit;
use sys\arenapvp\task\QueueTask;
use sys\irish\CorePlayer;

class QueueManager {

    /** @var Queue[] */
    private $unrankedQueue = [];

	/** @var Queue[] */
	private $rankedQueue = [];

    /** @var ArenaPvP */
    private $plugin;

    public function __construct(ArenaPvP $plugin) {
        $this->plugin = $plugin;
        $this->loadQueue();
    }

	/**
	 * @return ArenaPvP
	 */
    public function getPlugin(): ArenaPvP {
        return $this->plugin;
    }

	/**
	 * @return Queue[]
	 */
    public function getRankedQueues(): array {
    	return $this->rankedQueue;
    }

	/**
	 * @return Queue[]
	 */
    public function getUnrankedQueues(): array {
    	return $this->unrankedQueue;
    }

	/**
	 * @param Kit $kit
	 * @param bool $ranked
	 * @param bool $win10
	 * @return Queue
	 */
    public function getQueue(Kit $kit, bool $ranked = false, $win10 = false): Queue {
    	$search = $kit->getName();
    	if($win10) {
		    $search .= CorePlayer::OS_WIN10;
	    }
	    if($ranked) {
    		return $this->rankedQueue[$search];
	    } else {
		    return $this->unrankedQueue[$search];
	    }
    }

    public function createQueue(Kit $kit) {
    	$this->unrankedQueue[$kit->getName()] = new Queue($this->getPlugin(), $kit);
	    $this->unrankedQueue[$kit->getName() . CorePlayer::OS_WIN10] = new Queue($this->getPlugin(), $kit, false, CorePlayer::OS_WIN10);
	    $this->rankedQueue[$kit->getName()] = new Queue($this->getPlugin(), $kit, true);
	    $this->rankedQueue[$kit->getName() . CorePlayer::OS_WIN10] = new Queue($this->getPlugin(), $kit, true, CorePlayer::OS_WIN10);
    }

    private function loadQueue(){
        foreach($this->getPlugin()->getKitManager()->getKits() as $kit) {
        	$this->createQueue($kit);
        }
        new QueueTask($this->getPlugin());
    }

    public function checkQueue(){
    	foreach($this->getRankedQueues() as $queue) $queue->pickMatch();
    	foreach($this->getUnrankedQueues() as $queue) $queue->pickMatch();
    }

}