<?php
/**
 *
 * This file was created by Matt on 7/11/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\party;


use sys\arenapvp\ArenaPlayer;

class Invite {

	/** @var ArenaPlayer */
	private $from;

	/** @var ArenaPlayer[] */
	private $players = [];

	public function __construct(ArenaPlayer $from) {
		$this->from = $from;
	}

	/**
	 * @return ArenaPlayer
	 */
	public function getFrom(): ArenaPlayer {
		return $this->from ?? null;
	}

	/**
	 * @return ArenaPlayer[]
	 */
	public function getPlayers(): array {
		return $this->players;
	}

	/**
	 * @param ArenaPlayer $player
	 * @return bool
	 */
	public function isInvited(ArenaPlayer $player): bool {
		return isset($this->players[$player->getName()]);
	}

	public function addInvite(ArenaPlayer $player) {
		$this->players[$player->getName()] = $player;
	}

	public function removeInvite(ArenaPlayer $player) {
		if(isset($this->players[$player->getName()])) unset($this->players[$player->getName()]);
	}

}