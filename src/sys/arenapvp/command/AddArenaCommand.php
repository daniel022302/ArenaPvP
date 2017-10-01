<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:20 PM
 */

namespace sys\arenapvp\command;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\BaseArenaUserCommand;

class AddArenaCommand extends BaseArenaUserCommand {

	private $positions = [];

	public function __construct(ArenaPvP $main) {
		parent::__construct($main, "addarena", "Add arenas", "/addarena [option]", ["aa", "arena"]);
	}

	/**
	 * @param CommandSender|ArenaPlayer $sender
	 * @param array $args
	 * @return mixed|string
	 */
	public function onExecute(CommandSender $sender, array $args) {
		if ($sender->isOp()) {
			if (isset($args[0])) {
				switch (strtolower($args[0])) {
					case "save":
						if (isset($args[1])) {
							$arena = $this->getPlugin()->getArenaManager()->getArenaById($args[1] - 1);
							if ($arena === null) {
								return TextFormat::RED . "No arena was found by that name!";
							}
							$sender->sendMessage(TextFormat::GREEN . "Saving arena...");
							$start = microtime(true);
							$arena->saveChunks(true);
							return TextFormat::GREEN . "Time taken: " . (number_format(microtime(true) - $start, 3)) . "s";
						}
						break;
					case "count":
						return TextFormat::GREEN . count($this->getPlugin()->getArenaManager()->getArenas()) . " arenas loaded! (" . count($this->getPlugin()->getArenaManager()->getOpenArenas()) . "open)";
					case "reset":
						if (isset($args[1])) {
							$arena = $this->getPlugin()->getArenaManager()->getArenaById($args[1] - 1);
							if ($arena === null) {
								return TextFormat::RED . "No arena was found by that name!";
							}
							$sender->sendMessage(TextFormat::GREEN . "Resetting arena...");
							$start = microtime(true);
							$arena->resetArena();
							return TextFormat::GREEN . "Time taken: " . (number_format(microtime(true) - $start, 3)) . "s";
						}
						break;
					case "removeblocks":
						if (isset($args[1])) {
							$arena = $this->getPlugin()->getArenaManager()->getArenaById($args[1] - 1);
							if ($arena === null) {
								return TextFormat::RED . "No arena was found by that name!";
							}
							$sender->sendMessage(TextFormat::GREEN . "Resetting arena blocks...");
							$start = microtime(true);
							$arena->removeBlocks();
							return TextFormat::GREEN . "Time taken: " . (number_format(microtime(true) - $start, 3)) . "s";
						}
						break;
					case "remove":
						if (isset($args[1])) {
							$name = $this->getPlugin()->getArenaManager()->getArenaById($args[1] - 1);
							if ($name === null) {
								return TextFormat::RED . "No arena was found by that name!";
							}
							$sender->sendMessage(TextFormat::GREEN . "Removing arena...");
							return $this->getPlugin()->getArenaManager()->deleteArena($args[1] - 1);
						}
						break;
					case "tp":
						if ($sender->inMatch()) return TextFormat::RED . "You can't do this while in a match!";
						$arena = $this->getPlugin()->getArenaManager()->getArenaById($args[1] - 1);
						if ($arena === null) {
							return TextFormat::RED . "No arena was found by that name!";
						}
						$sender->teleport($arena->getRandomPosition());
						return TextFormat::GREEN . "Teleporting to arena #" . ($arena->getId() + 1) . "...";
						break;
					case "type":
						if (isset($args[1])) {
							$this->positions[$sender->getName()]["type"] = $args[1];
							return TextFormat::GREEN . "Arena type set!";
						}
						return TextFormat::RED . "You must provide a type!";
						break;
					case "maxbuildheight":
						if (isset($args[1])) {
							$this->positions[$sender->getName()]["maxBuildHeight"] = $args[1];
							return TextFormat::GREEN . "Max build height of arena set!";
						}
						return TextFormat::RED . "You must provide a height!";
						break;
					case "pos1":
						$this->positions[$sender->getName()]["pos1"] = $sender->getPosition();
						return TextFormat::GREEN . "First position set!";
						break;
					case "pos2":
						$this->positions[$sender->getName()]["pos2"] = $sender->getPosition();
						return TextFormat::GREEN . "Second position set!";
						break;
					case "edge1":
						$this->positions[$sender->getName()]["edge1"] = $sender->getPosition();
						return TextFormat::GREEN . "First edge set!";
						break;
					case "edge2":
						$this->positions[$sender->getName()]["edge2"] = $sender->getPosition();
						return TextFormat::GREEN . "Second edge set!";
						break;
					case "finish":
						$pos1 = $this->positions[$sender->getName()]["pos1"];
						$pos2 = $this->positions[$sender->getName()]["pos2"];
						$edge1 = $this->positions[$sender->getName()]["edge1"];
						$edge2 = $this->positions[$sender->getName()]["edge2"];
						$type = $this->positions[$sender->getName()]["type"];
						$maxBuildHeight = $this->positions[$sender->getName()]["maxBuildHeight"];
						if (isset($pos1, $pos2, $type, $edge1, $edge2, $maxBuildHeight)) {
							$sender->addTitle(TextFormat::GREEN . "Arena added!", TextFormat::GRAY . "The arena was successfully created!", 20, 100, 20);
							$this->getPlugin()->getArenaManager()->createArena($this->getPlugin()->getArenaManager()->getNextArenaIndex(), $pos1, $pos2, $edge1, $edge2, $sender->getLevel(), $type, $maxBuildHeight);
							unset($this->positions[$sender->getName()]);
							return TextFormat::GREEN . "Arena created!";
						} else {
							return TextFormat::RED . "All parameters must be reached first!";
						}
				}
			}
			return TextFormat::RED . "Usage: " . $this->getUsage();
		}
		return TextFormat::RED . "You must be OP to use this command!";
	}

}