<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:20 PM
 */

namespace sys\arenapvp\command;

use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\BaseArenaUserCommand;

class EloCommand extends BaseArenaUserCommand {

	public function __construct(ArenaPvP $main) {
		parent::__construct($main, "elo", "Get the elo of anyone", "/elo [player] [kit]", []);
	}

	/**
	 * @param CommandSender|ArenaPlayer $sender
	 * @param array $args
	 * @return bool|string
	 */
	public function onExecute(CommandSender $sender, array $args) {
		return $this->sendElo($sender, $args[0] ?? null);
	}

	public function sendElo(ArenaPlayer $sender, $player = null) {
		$playerObject = $sender->getServer()->getPlayer($player);
		if ($playerObject instanceof ArenaPlayer) {
			$sender->sendArgsMessage(TextFormat::GRAY . "---- {0}'s Elo ----", $playerObject->getName());
			foreach ($playerObject->getAllElo() as $elo) {
				$sender->sendMessage(TextFormat::GRAY . $elo->getKit()->getName() . ": " . TextFormat::GOLD . $elo->getElo());
			}
		} else if (is_string($player)) {
			$path = $this->getPlugin()->getServer()->getPluginPath() . DIRECTORY_SEPARATOR . "Core" . DIRECTORY_SEPARATOR . "players";
			$files = scandir($path);
			foreach ($files as $file) {
				if ($file == strtolower($player) . ".data") {
					$cfg = new Config($path . DIRECTORY_SEPARATOR . $file, Config::JSON);
					$elo = $cfg->get("elo");
					$sender->sendArgsMessage(TextFormat::GRAY . "---- {0}'s Elo ----", $player);
					foreach ($elo as $kit => $eloInt) {
						$sender->sendMessage(TextFormat::GRAY . $kit . ": " . TextFormat::GOLD . $eloInt);
					}
				}
			}
			$sender->sendMessage(TextFormat::RED . "No person under that name was found!");
			return false;
		} else {
			$sender->sendMessage(TextFormat::GRAY . "---- Your Elo ----");
			foreach ($sender->getAllElo() as $elo) {
				$sender->sendArgsMessage(TextFormat::GRAY . "{0}: " . TextFormat::GOLD . "{1}", $elo->getKit()->getName(), $elo->getElo());
			}
		}
		$sender->sendMessage(TextFormat::GRAY . "------------------");
		return true;
	}

}