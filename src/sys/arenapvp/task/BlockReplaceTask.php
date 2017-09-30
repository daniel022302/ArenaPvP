<?php
/**
 *
 * This file was created by Matt on 8/7/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\task;


use pocketmine\block\Block;
use pocketmine\Player;
use sys\arenapvp\basefiles\ArenaTask;

class BlockReplaceTask extends ArenaTask {

	/** @var Block $block */
	private $block;

	/** @var Player $player */
	private $player;


	/** @noinspection PhpMissingParentConstructorInspection | I wish PHPStorm would be smart enough to detect line breaks with this
	 * @param Block $block
	 * @param Player $player
	 */
	public function __construct(Block $block, Player $player) {
		$player->getServer()->getScheduler()->scheduleDelayedTask($this, 5);
		$this->block = $block;
		$this->player = $player;
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick) {
		$this->block->getLevel()->sendBlocks([$this->player], [$this->block]);
		unset($this->player, $this->block);
	}
}