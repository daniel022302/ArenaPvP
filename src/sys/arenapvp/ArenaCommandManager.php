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

class ArenaCommandManager {

	/** @var array */
	private $commandData = [];

	public function initCommands(ArenaPvP $plugin) {
		$plugin->saveResource("commands.json", true);
		$config = new Config($plugin->getDataFolder() . DIRECTORY_SEPARATOR . "commands.json");
		$this->addCommandData($config->getAll());
		$plugin->getServer()->getCommandMap()->registerAll("arenapvp", [new AddArenaCommand($plugin), new DuelCommand($plugin), new EditEloCommand($plugin), new EloCommand($plugin), new SpectateCommand($plugin), new PartyCommand($plugin)]);
	}

	public function addCommandData(array $data) {
		$this->commandData = array_merge($this->commandData, $data);
	}

	public function unregisterCommand(ArenaPvP $plugin, string $name) {
		$command = $plugin->getServer()->getCommandMap()->getCommand($name);
		$command->setAliases([]);
		$command->setLabel($name . "_disabled");
		$command->unregister($plugin->getServer()->getCommandMap());
	}

	public function getIndividualCommandData(string $commandName) {
		return $this->commandData[$commandName] ?? null;
	}

	/**
	 * @return array
	 */
	public function getCommandData(): array {
		return $this->commandData;
	}


}