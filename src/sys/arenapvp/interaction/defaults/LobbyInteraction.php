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
use sys\arenapvp\menu\defaults\PartyChoiceMenu;
use sys\arenapvp\menu\defaults\RankedQueueKitMenu;
use sys\arenapvp\menu\defaults\UnrankedQueueKitMenu;

class LobbyInteraction extends Interaction {

	public function __construct(ArenaPvP $plugin, array $items = []) {
		parent::__construct($plugin, $items);
	}

	public function onInteract(ArenaPlayer $player, Item $item) {
		if(!$player->inMatch()) {
			switch($item->getId()) {
				case Item::EMPTY_MAP:
					$menu = new PartyChoiceMenu($this->getPlugin());
					$player->addMenu($menu);
					$player->sendMenu("Party Events");
					break;
				case Item::GOLDEN_SWORD:
					if(!$player->inParty()) {
						$menu = new UnrankedQueueKitMenu($this->getPlugin());
						$player->addMenu($menu);
						$player->sendMenu("Unranked Queue");
					} else {
						$player->sendMessage(TextFormat::RED."You can't join queues while in a party!");
					}
					break;
				case Item::DIAMOND_SWORD:
					if(!$player->inParty()) {
						$menu = new RankedQueueKitMenu($this->getPlugin());
						$player->addMenu($menu);
						$player->sendMenu("Ranked Queue");
					} else {
						$player->sendMessage(TextFormat::RED."You can't join queues while in a party!");
					}
					break;

			}
		}


	}
}