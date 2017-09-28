<?php
/**
 *
 * This file was created by Matt on 7/28/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\match;


use sys\arenapvp\ArenaPlayer;

class Team {

	/** @var int */
	private $playerCount = 0;

	/** @var ArenaPlayer[] */
	private $players = [];

	/**
	 * Team constructor.
	 * @param ArenaPlayer[] ...$players
	 */
	public function __construct(...$players) {
		$this->players = $players;
	}

	/**
	 * @return ArenaPlayer[]
	 */
	public function getPlayers(): array {
		return $this->players;
	}

	public function hasPlayer(ArenaPlayer $player) {
		return isset($this->players[$player->getName()]);
	}

	public function onTeam(ArenaPlayer $firstPlayer, ArenaPlayer $secondPlayer) {
		return isset($this->players[$firstPlayer->getName()], $this->players[$secondPlayer->getName()]);
	}

	public function addPlayer(ArenaPlayer $player) {
		if(!isset($this->players[$player->getName()])) {
			$this->players[$player->getName()] = $player;
			$this->playerCount++;
		}
	}

	public function subtractPlayerCount() {
		$this->playerCount--;
	}

	public function getPlayerCount() {
		return $this->playerCount;
	}

	public function sendMessage(string $message) {
		foreach($this->getPlayers() as $player) $player->sendMessage($message);
	}

	/**
	 * @param string $message
	 * @param string[] ...$args
	 * It'd be easier to do a foreach loop and send an args message per person, but
	 * it'd register $args as one parameter only
	 */
	public function sendArgsMessage(string $message, ... $args) {
		for($i = 0; $i < count($args); $i++) {
			$message = str_replace("{" . $i . "}", $args[$i], $message);
		}
		$this->sendMessage($message);
	}

	public function nullify() {
		$this->playerCount = null;
		$this->players = null;
	}

	public function isDead() {
		return $this->getPlayerCount() <= 0;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		$string = "[";
		foreach($this->getPlayers() as $player) $string .= $player->getPlayerName() . ", ";
		//$string .= implode(", ", array_keys($this->getPlayers())); buggy, need to think of a better solution
		$string = rtrim($string, ",");
		$string .= "]";
		return $string;
	}

}