<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:10 PM
 */

namespace sys\arenapvp\basefiles;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;

abstract class BaseArenaUserCommand extends BaseArenaCommand {

    public function __construct(ArenaPvP $main, $name, $description = "", $usageMessage = null, $aliases = [], string $permission = null) {
        parent::__construct($main, $name, $description, $usageMessage, $aliases, $permission);
    }

    /**
     * @param CommandSender|ArenaPlayer $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($sender instanceof ArenaPlayer){
            return parent::execute($sender, $commandLabel, $args);
        }
        $sender->sendMessage(TextFormat::RED."Use this command in-game!");
        return false;
    }

}