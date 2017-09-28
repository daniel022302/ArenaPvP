<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 2/10/2017
 * Time: 9:25 PM
 */

namespace sys\arenapvp;


use pocketmine\utils\Config;
use sys\arenapvp\command\AddArenaCommand;
use sys\arenapvp\command\DuelCommand;
use sys\arenapvp\command\EditEloCommand;
use sys\arenapvp\command\EloCommand;
use sys\arenapvp\command\PartyCommand;
use sys\arenapvp\command\SpectateCommand;
use sys\irish\manager\CommandManager;

class ArenaCommandManager extends CommandManager {

    public function initCommands(ArenaPvP $plugin) {
    	$plugin->saveResource("commands.json", true);
    	$config = new Config($plugin->getDataFolder() . DIRECTORY_SEPARATOR . "commands.json");
    	$this->addCommandData($config->getAll());
        $plugin->getServer()->getCommandMap()->registerAll("arenapvp", [
            new AddArenaCommand($plugin),
            new DuelCommand($plugin),
            new EditEloCommand($plugin),
            new EloCommand($plugin),
            new SpectateCommand($plugin),
            new PartyCommand($plugin)
        ]);
    }


}