<?php

/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/21/2017
 * Time: 9:17 PM
 */

namespace sys\arenapvp\menu\defaults;


use pocketmine\item\Item;
use pocketmine\tile\Skull;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\kit\Kit;
use sys\arenapvp\menu\Menu;
use sys\arenapvp\utils\ArenaChestInventory;

class DuelAcceptMenu extends Menu {

	/** @var ArenaPlayer */
	private $opponent = null;

	/** @var Kit */
	private $kit = null;

	/**
	 * Menu constructor.
	 * @param ArenaPvP $plugin
	 * @param ArenaPlayer $opponent
	 * @param Kit $kit
	 */
	public function __construct(ArenaPvP $plugin, ArenaPlayer $opponent, Kit $kit) {
		$this->opponent = $opponent;
		$this->kit = $kit;
		$items = $this->itemInit();
		parent::__construct($plugin, $items);
	}

	private function itemInit() {
		$itemString = "kxxxgxxxhxxxxxxxxxxxxxrxxxx";
		$itemArray = str_split($itemString);
		$items = [];
		foreach ($itemArray as $item) {
			$id = 0;
			$meta = 0;
			switch ($item) {
				case "k":
					$id = Item::IRON_SWORD;
					$name = TextFormat::GREEN . $this->getKit()->getName();
					break;
				case "x":
					$id = Item::AIR;
					break;
				case "g":
					$id = Item::STAINED_HARDENED_CLAY;
					$meta = 13;
					$name = TextFormat::GREEN . "Accept " . $this->getOpponent()->getName() . "'s request!";
					break;
				case "h":
					$id = Item::SKULL;
					$meta = Skull::TYPE_HUMAN;
					$name = TextFormat::GRAY . "Request From: " . TextFormat::GOLD . $this->getOpponent()->getName();
					break;
				case "r":
					$id = Item::STAINED_HARDENED_CLAY;
					$meta = 14;
					$name = TextFormat::RED . "Deny " . $this->getOpponent()->getName() . "'s request!";
					break;
			}
			$i = Item::get($id, $meta);
			if (isset($name)) $i->setCustomName($name);
			$items[] = $i;
		}
		return $items;
	}

	/**
	 * @return Kit
	 */
	public function getKit() {
		return $this->kit;
	}

	/**
	 * @return ArenaPlayer
	 */
	public function getOpponent() {
		return $this->opponent;
	}

	/**
	 * @param ArenaPlayer $player
	 * @param ArenaChestInventory $inventory
	 * @param Item $item
	 */
	public function getInteraction(ArenaPlayer $player, ArenaChestInventory $inventory, Item $item) {
		if ($item->getId() == Item::STAINED_HARDENED_CLAY) {
			if (!$player->inMatch()) {
				$player->removeMenu();
				$inventory->onClose($player);
				switch ($item->getDamage()) {
					case 13:
						/** @var ArenaPlayer[] $players */
						$players = [$player->getName() => $player, $this->getOpponent()->getName() => $this->getOpponent()];
						foreach ($players as $arenaPlayer) {
							if ($arenaPlayer->inQueue()) $arenaPlayer->getQueue()->removePlayer($player);
						}
						if ($player->fullyInMatch()) {
							$player->sendMessage(TextFormat::RED . "You can't join duels while in a match!");
							$this->getOpponent()->sendMessage(TextFormat::RED . "That player is in a match!");
							return;
						} else if ($this->getOpponent()->fullyInMatch()) {
							$this->getOpponent()->sendMessage(TextFormat::RED . "You can't join duels while in a match!");
							$player->sendMessage(TextFormat::RED . "That player is in a match!");
							return;
						}
						$player->setInMatch();
						$this->getOpponent()->setInMatch();
						$this->getPlugin()->getMatchManager()->createMatch($players, $this->getKit());
						$player->sendMessage(TextFormat::GREEN . "You have accepted " . $this->getOpponent()->getName() . "'s duel request!");
						$this->getOpponent()->sendMessage(TextFormat::GREEN . $player->getName() . " has accepted your duel request!");
						break;
					case 14:
						$player->sendMessage(TextFormat::GREEN . "You have denied " . $this->getOpponent()->getName() . "'s duel request!");
						$this->getOpponent()->sendMessage(TextFormat::RED . $player->getName() . " has denied your duel request!");

				}
			} else {
				$player->sendMessage(TextFormat::RED . "You are already in a match!");
			}
			$player->setHasDuelRequest(false);
		}
	}

}