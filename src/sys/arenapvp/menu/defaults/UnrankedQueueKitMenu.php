<?php

/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/21/2017
 * Time: 9:17 PM
 */

namespace sys\arenapvp\menu\defaults;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\menu\Menu;
use sys\arenapvp\utils\ArenaChestInventory;
use sys\irish\CorePlayer;

class UnrankedQueueKitMenu extends Menu {

	/**
	 * Menu constructor.
	 * @param ArenaPvP $plugin
	 */
	public function __construct(ArenaPvP $plugin) {
		parent::__construct($plugin, $plugin->getKitManager()->getAllKitItems());
	}

	public function getInteraction(ArenaPlayer $player, ArenaChestInventory $inventory, Item $item) {
		$kit = $this->getPlugin()->getKitManager()->getKitByName(TextFormat::clean($item->getCustomName()));
		if ($kit !== null) {
			if ($player->getClientOs() == CorePlayer::OS_WIN10) {
				$queue = $this->getPlugin()->getQueueManager()->getQueue($kit, false, true);
			} else {
				$queue = $this->getPlugin()->getQueueManager()->getQueue($kit);
			}
			$queue->addPlayer($player);
			$player->getInventory()->clearAll();
			$player->getInventory()->setItem(0, Item::get(Item::PAPER)->setCustomName(TextFormat::GRAY . "Queue Info"));
			$player->getInventory()->setItem(8, Item::get(Item::REDSTONE)->setCustomName(TextFormat::RED . "Leave Queue"));
			$player->sendMessage(TextFormat::GREEN . "Successfully added to " . $kit->getName() . " queue!");
			$player->removeMenu();
			$inventory->onClose($player);
		}
	}
}