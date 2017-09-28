<?php
/**
 *
 * This file was created by Matt on 7/19/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\utils;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\tile\Chest;

class ArenaChest extends Chest {

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->inventory = new ArenaChestInventory($this);
	}

	public function spawnToAll() {}

	public function spawnTo(Player $player) {}

	public function saveNBT() {
		/*
		 * Don't save the NBT, because that causes bugs and bugs are bad.
		 */
	}

}