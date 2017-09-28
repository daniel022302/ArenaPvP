<?php
/**
 *
 * This file was created by Matt on 7/16/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\queue;


use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\kit\Kit;
use sys\arenapvp\task\MatchTask;

class Queue {

	/** @var Kit */
	private $kit;

	/** @var int */
	private $os = 0;

	/** @var bool  */
	private $ranked = false;

	/** @var ArenaPvP */
	private $plugin;

	/** @var ArenaPlayer[] */
	private $players = [];

	public function __construct(ArenaPvP $plugin, Kit $kit, bool $ranked = false, int $os = 0) {
		$this->plugin = $plugin;
		$this->kit = $kit;
		$this->ranked = $ranked;
		$this->os = $os;
	}

	public function setOs(int $os) {
		$this->os = $os;
	}

	/**
	 * @return bool
	 */
	public function isRanked(): bool {
		return $this->ranked;
	}

	/**
	 * @return Kit
	 */
	public function getKit(): Kit {
		return $this->kit;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	/**
	 * @return ArenaPlayer[]
	 */
	public function getPlayers(): array {
		return $this->players;
	}

	public function getCount(): int {
		return count($this->players);
	}

	public function isPlayer(ArenaPlayer $player) {
		return isset($this->players[$player->getName()]);
	}

	public function addPlayer(ArenaPlayer $player) {
		$this->players[$player->getName()] = $player;
		$player->setQueue($this);
	}

	public function removePlayer(ArenaPlayer $player) {
		if(isset($this->players[$player->getName()])) {
			unset($this->players[$player->getName()]);
			$player->removeFromQueue();
		}
	}

	/**
	 * @param int $count
	 * @return ArenaPlayer[]
	 */
	public function getRandomPlayers($count = 2) {
		$playerIndexes = array_rand($this->getPlayers(), $count);
		/** @var ArenaPlayer[] $players */
		$players = [];
		foreach($playerIndexes as $index) {
			$player = $this->players[$index];
			if($player->fullyInMatch() and $this->isPlayer($player)) {
				$this->removePlayer($player);
				return $this->getRandomPlayers();
			} else {
				$players[$player->getName()] = $player;
				break;
			}
		}

		return $players;
	}

	public function pickMatch() {
		if($this->getCount() >= 2) {
			$arena = $this->getPlugin()->getArenaManager()->getOpenArena($this->getKit()->getMapType());
			if ($arena !== null) {
				$players = $this->getRandomPlayers();
				new MatchTask($this->getPlugin(), $players, $this->getKit(), $arena, false, $this->isRanked());
				foreach ($players as $player) {
					if ($this->isRanked()) {
						$player->sendMessage(TextFormat::GREEN . "Found a ranked match!");
					} else {
						$player->sendMessage(TextFormat::GREEN . "Found an unranked match!");
					}
					$this->removePlayer($player);
				}
			}
		}
	}

}