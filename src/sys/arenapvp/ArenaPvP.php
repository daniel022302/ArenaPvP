<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 6:14 PM
 */

namespace sys\arenapvp;


use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use sys\arenapvp\arena\ArenaManager;
use sys\arenapvp\interaction\InteractionManager;
use sys\arenapvp\kit\KitManager;
use sys\arenapvp\match\MatchManager;
use sys\arenapvp\party\PartyManager;
use sys\arenapvp\queue\QueueManager;

class ArenaPvP extends PluginBase {

	/** @var ArenaManager */
	private $arenaManager;

	/** @var ArenaCommandManager */
	private $commandManager;

	/** @var QueueManager */
	private $queueManager;

	/** @var KitManager */
	private $kitManager;

	/** @var InteractionManager */
	private $interactionManager;

	/** @var MatchManager */
	private $matchManager;

	/** @var PartyManager */
	private $partyManager;

	public function onLoad() {
		$this->loadArenaManager();
		$this->loadCommandManager();
		$this->loadInteractionManager();
		$this->loadKitManager();
		$this->loadMatchManager();
		$this->loadPartyManager();
		$this->loadQueueManager();
		$this->loadListeners();
	}

	public function onEnable() {
		$this->getLogger()->info(TextFormat::GREEN . $this->getDescription()->getName() . " has been enabled!");
	}

	public function onDisable() {
		$this->getArenaManager()->onDisable();
		$this->getLogger()->info(TextFormat::RED . $this->getDescription()->getName() . " has been disabled!");
	}

	private function loadArenaManager() {
		$this->arenaManager = new ArenaManager($this);
	}

	private function loadCommandManager() {
		$this->commandManager = new ArenaCommandManager();
		$this->getCommandManager()->initCommands($this); //TODO: Find a fix for this
	}

	private function loadQueueManager() {
		$this->queueManager = new QueueManager($this);
	}

	private function loadInteractionManager() {
		$this->interactionManager = new InteractionManager($this);
	}

	private function loadKitManager() {
		$this->kitManager = new KitManager($this);
	}

	private function loadListeners() {
		new LobbyListener($this);
		new MatchListener($this);
	}

	private function loadMatchManager() {
		$this->matchManager = new MatchManager($this);
	}

	private function loadPartyManager() {
		$this->partyManager = new PartyManager($this);
	}

	/**
	 * @return ArenaManager
	 */
	public function getArenaManager(): ArenaManager {
		return $this->arenaManager;
	}

	/**
	 * @return ArenaCommandManager
	 */
	public function getCommandManager(): ArenaCommandManager {
		return $this->commandManager;
	}

	/**
	 * @return QueueManager
	 */
	public function getQueueManager(): QueueManager {
		return $this->queueManager;
	}

	/**
	 * @return KitManager
	 */
	public function getKitManager(): KitManager {
		return $this->kitManager;
	}

	/**
	 * @return InteractionManager
	 */
	public function getInteractionManager(): InteractionManager {
		return $this->interactionManager;
	}

	/**
	 * @return MatchManager
	 */
	public function getMatchManager(): MatchManager {
		return $this->matchManager;
	}

	/**
	 * @return PartyManager
	 */
	public function getPartyManager(): PartyManager {
		return $this->partyManager;
	}

}
