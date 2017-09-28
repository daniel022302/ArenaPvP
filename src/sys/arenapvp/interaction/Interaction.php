<?php
/**
 *
 * This file was created by Matt on 7/18/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\interaction;


use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;

abstract class Interaction {

	/** @var Item[] */
	private $items = [];

	/** @var ArenaPvP */
	private $plugin;

	public function __construct(ArenaPvP $plugin, array $items = []) {
		$this->plugin = $plugin;
		$this->items = $items;
	}

	/**
	 * @return Item[]
	 */
	public function getItems(): array {
		return $this->items;
	}

	public function exists(Item $item) {
		foreach($this->getItems() as $interactionItem) {
			if($item->getId() == $interactionItem->getId()) {
				if(TextFormat::clean($item->getCustomName()) == $interactionItem->getCustomName()) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	abstract public function onInteract(ArenaPlayer $player, Item $item);


}