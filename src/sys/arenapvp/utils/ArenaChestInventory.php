<?php
/**
 *
 * This file was created by Matt on 7/19/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\utils;


use pocketmine\block\Block;
use pocketmine\inventory\ChestInventory;
use pocketmine\Player;
use sys\arenapvp\task\BlockReplaceTask;

class ArenaChestInventory extends ChestInventory {

	/** @var array */
	private $replacementBlock = [];

	/** @var bool */
	private $closed = false;

	public function __construct(ArenaChest $tile) {
		parent::__construct($tile);
		$this->holder = $tile; //Tells PHP that "hey this isn't the same class(although InventoryHolder is implemented via a parent class)
		$this->replacementBlock = [$tile->getBlock()->getId(), $tile->getBlock()->getDamage(), $tile->level, $tile->x, $tile->y, $tile->z];
	}

	/**
	 * @return Block
	 */
	public function getReplacementBlock(): Block {
		$block = Block::get($this->replacementBlock[0], $this->replacementBlock[1]);
		$block->setLevel($this->replacementBlock[2]);
		$block->setComponents($this->replacementBlock[3], $this->replacementBlock[4], $this->replacementBlock[5]);
		return $block;
	}

	public function sendRealBlock(Player $player) {
		$block = $this->getReplacementBlock();
		if($block instanceof Block) {
			new BlockReplaceTask($block, $player);
		}
	}

	/**
	 * @return ArenaChest
	 */
	public function getHolder(): ArenaChest {
		return $this->holder;
	}

	public function onClose(Player $who) {
		if(!$this->closed) {
			$this->closed = true;
			parent::onClose($who);
			$this->getHolder()->close();
			$this->sendRealBlock($who);
		}
	}

}