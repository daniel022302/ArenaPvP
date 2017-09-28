<?php
/**
 *
 * This file was created by Matt on 7/18/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\interaction\defaults;


use pocketmine\item\Item;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\interaction\Interaction;

class SpectatingInteraction extends Interaction {

	public function __construct(ArenaPvP $plugin, array $items = []) {
		parent::__construct($plugin, $items);
	}

	public function onInteract(ArenaPlayer $player, Item $item) {
		if($player->isSpectating()) {
			switch($item->getId()) {
				case Item::REDSTONE_TORCH:
					$player->getMatchSpectating()->removeSpectator($player);
					$player->removeFromSpectating();
					$player->teleport($this->getPlugin()->getServer()->getDefaultLevel()->getSpawnLocation());
					$this->getPlugin()->getArenaManager()->addLobbyItems($player);
					break;
			}
		}


	}
}