<?php
/**
 *
 * This file was created by Matt on 7/20/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\basefiles;


use pocketmine\event\Listener;
use sys\arenapvp\ArenaPvP;

class BaseListener implements Listener {

	/** @var ArenaPvP */
	private $plugin;

	public function __construct(ArenaPvP $plugin) {
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

}