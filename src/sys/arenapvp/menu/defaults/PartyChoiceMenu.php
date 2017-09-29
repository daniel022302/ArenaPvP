<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 3/1/2017
 * Time: 6:18 PM
 */

namespace sys\arenapvp\menu\defaults;


use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\menu\Menu;
use sys\arenapvp\utils\ArenaChestInventory;

class PartyChoiceMenu extends Menu {

	private $num = 0;
	private $teams = false;

	public function __construct(ArenaPvP $plugin) {
		parent::__construct($plugin, $this->itemInit());
	}

	private function itemInit() {
		$itemString = "xxxxxxxxxxxfxxxtxxxxxxxxxxx";
		$itemArray = str_split($itemString);
		$items = [];
		foreach ($itemArray as $item) {
			$id = 0;
			$meta = 0;
			switch ($item) {
				case "f":
					$id = Item::IRON_SWORD;
					$name = TextFormat::GREEN . "Start FFA Event";
					break;
				case "x":
					$id = Item::AIR;
					break;
				case "t":
					$id = Item::DIAMOND_SWORD;
					$name = TextFormat::GREEN . "Start Team Event";
					break;
			}
			$i = Item::get($id, $meta);
			if (isset($name)) $i->setCustomName($name);
			$items[] = $i;
		}
		return $items;
	}

	/**
	 * @param ArenaPlayer $player
	 * @param ArenaChestInventory $inventory
	 * @param Item $item
	 */
	public function getInteraction(ArenaPlayer $player, ArenaChestInventory $inventory, Item $item) {

		if ($player->inParty()) {
			if ($player === $player->getParty()->getLeader()) {
				if (count($player->getParty()->getOnlineMembers()) >= 2) {
					switch ($this->num) {
						case 0:
							if ($item->getId() === Item::AIR) return;
							if ($item->getId() === Item::DIAMOND_SWORD) {
								$this->teams = true;
							}
							$inventory->setContents($this->getPlugin()->getKitManager()->getAllKitItems());
							$this->num = 1;
							break;
						case 1:
							$kit = $this->getPlugin()->getKitManager()->getKitByName(TextFormat::clean($item->getCustomName()));
							if ($kit !== null) {
								$player->removeMenu();
								$inventory->onClose($player);
								$message = $this->teams ? "Trying to start a team event..." : "Trying to start an FFA event...";
								$player->getParty()->broadcastMessage(TextFormat::GREEN . $message);
								$this->getPlugin()->getMatchManager()->createMatch($player->getParty()->getOnlineMembers(), $kit, $this->teams);
							}
					}
				} else {
					$player->removeMenu();
					$inventory->onClose($player);
					$player->sendMessage(TextFormat::RED . "You must have at least 2 people online in your party to start an event!");
				}
			} else {
				$player->removeMenu();
				$inventory->onClose($player);
				$player->sendMessage(TextFormat::RED . "You must be the leader to start a party event!");
			}
		} else {
			$player->removeMenu();
			$inventory->onClose($player);
			$player->sendMessage(TextFormat::RED . "You must be in a party to start a party event!");
		}
	}
}