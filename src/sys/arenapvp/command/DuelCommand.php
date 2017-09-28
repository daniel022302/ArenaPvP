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
use sys\arenapvp\menu\defaults\DuelKitMenu;

class DuelCommand extends BaseArenaUserCommand {

    public function __construct(ArenaPvP $main) {
        parent::__construct($main, "duel", "Duel other players", "/duel [player]");
    }

    /**
     * @param CommandSender|ArenaPlayer $sender
     * @param array $args
     * @return bool|mixed|string
     */
    public function onExecute(CommandSender $sender, array $args) {
        if(count($args) > 0){
            $player = $this->getPlayer($args[0]);
            if($sender === $player) return TextFormat::RED."You can't duel yourself!";

            if(!$player) return TextFormat::RED."That player is not online!";

            if(!$player->hasDuelRequestsEnabled()) return TextFormat::RED."This player is not accepting duel requests at this time!";

            if(!$player->isLoggedIn()) return TextFormat::RED."That player is not logged in!";

            if($sender->inParty()) return TextFormat::RED."You can't duel players while in a party!";

            if($player->inParty()) return TextFormat::RED."You can't duel players while they are in a party!";

            if($player->inMatch()) return TextFormat::RED."You can't duel players if they are already in a match!";

            if($sender->inMatch()) return TextFormat::RED."You can't duel while in a match!";


            $menu = new DuelKitMenu($this->getPlugin(), $player);
            $sender->addMenu($menu);
            $sender->sendMenu(TextFormat::GRAY."Kit Selector");
            return true;
        }
        return TextFormat::RED."Usage: ".$this->getUsage();
    }

}