<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:10 PM
 */

namespace sys\arenapvp\basefiles;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use sys\arenapvp\ArenaPvP;

abstract class BaseArenaCommand extends Command {

	private $plugin;

	public function __construct(ArenaPvP $main, $name, $description = "", $usageMessage = null, $aliases = [], string $permission = null) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->plugin = $main;
		$this->setCommandData();
		$this->setPermission($permission);

	}

	public function setCommandData() {
		$commandData = $this->getPlugin()->getCommandManager()->getIndividualCommandData($this->getName());
		if ($commandData !== null) {
			$this->commandData = $commandData["versions"][0];
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 *
	 * @return mixed
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if ($this->testPermission($sender)) {
			$result = $this->onExecute($sender, $args);
			if (is_string($result)) {
				$sender->sendMessage($result);
				return true;
			}
			return true;
		}
		return false;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 *
	 * @return mixed|void
	 */
	public abstract function onExecute(CommandSender $sender, array $args);
}