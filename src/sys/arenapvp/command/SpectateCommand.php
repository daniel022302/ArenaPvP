<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:20 PM
 */

namespace sys\arenapvp\command;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\BaseArenaUserCommand;
use sys\arenapvp\utils\ArenaPermissions;

class SpectateCommand extends BaseArenaUserCommand {

	public function __construct(ArenaPvP $main) {
		parent::__construct($main, "spectate", "Spectate other players", "/spectate [player]", ["spec"], ArenaPermissions::PERMISSION_SPECTATE);
	}

	/**
	 * @param CommandSender|ArenaPlayer $sender
	 * @param array $args
	 * @return bool|mixed|string
	 */
	public function onExecute(CommandSender $sender, array $args) {
		if (count($args) > 0) {
			$player = $this->getPlayer($args[0]);
			if ($sender === $player) return TextFormat::RED . "You can't spectate yourself!";

			if (!$player) return TextFormat::RED . "That player is not online!";

			if ($sender->inMatch()) return TextFormat::RED . "You can't spectate whilst in a match!";

			if (!$player->inMatch()) return TextFormat::RED . "That player is not in a match!";

			$sender->teleport($player);
			$player->getMatch()->addSpectator($sender);
			$sender->getInventory()->setItem($sender->getInventory()->getHotbarSlotIndex(8), Item::get(Item::REDSTONE_TORCH)->setCustomName(TextFormat::GREEN . "Spectator Toggle Off"));

			return true;
		}
		return TextFormat::RED . "Usage: " . $this->getUsage();
	}

}