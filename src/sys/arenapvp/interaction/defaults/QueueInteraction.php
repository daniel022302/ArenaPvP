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
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\interaction\Interaction;

class QueueInteraction extends Interaction {

	public function __construct(ArenaPvP $plugin, array $items = []) {
		parent::__construct($plugin, $items);
	}

	public function onInteract(ArenaPlayer $player, Item $item) {
		switch($item->getId()) {
			case Item::REDSTONE_DUST:
				if($player->inQueue()) {
					$player->getQueue()->removePlayer($player);
					$this->getPlugin()->getArenaManager()->addLobbyItems($player);
				}
				break;
			case Item::PAPER:
				if($player->inQueue()) {
					$player->sendMessage(TextFormat::GRAY . "Kit: " . TextFormat::GOLD . $player->getQueue()->getKit()->getName());
					$player->sendMessage(TextFormat::GRAY . "Ranked: " . TextFormat::GOLD . var_export($player->getQueue()->isRanked(), true));
				}
				break;
		}

	}
}