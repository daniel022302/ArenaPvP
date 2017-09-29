<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 6:57 PM
 */

namespace sys\arenapvp\arena;


use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;

class ArenaManager {

	const TERRAIN = 0;
	const FLAT = 1;
	const SG = 2;

	/** @var ArenaPvP */
	private $plugin;

	/** @var Config */
	private $config;

	/** @var Arena[] */
	private $arenas = [];

	public function __construct(ArenaPvP $main) {
		@mkdir($main->getDataFolder());
		$main->saveResource("arenas.yml");
		$this->config = new Config($main->getDataFolder() . "arenas.yml", Config::YAML);
		$this->plugin = $main;
		$this->loadArenas();
	}

	public function onDisable() {
		foreach ($this->getArenas() as $arena) {
			$arena->resetArena();
		}
		$this->getPlugin()->getLogger()->info(TextFormat::GREEN . "The arenas have been reset!");
	}

	private function loadArenas() {
		$arenas = $this->getConfig()->get("arenas");
		if (count($arenas) > 0) {
			foreach ($arenas as $name => $arenaUnparsed) {
				if (!$this->getPlugin()->getServer()->isLevelLoaded($arenaUnparsed["levelName"])) {
					$this->getPlugin()->getServer()->loadLevel($arenaUnparsed["levelName"]);
				}
				$level = $this->getPlugin()->getServer()->getLevelByName($arenaUnparsed["levelName"]);
				for ($i = 1; $i <= 2; $i++) {
					${"pos$i"} = new Position($arenaUnparsed["pos$i"][0], $arenaUnparsed["pos$i"][1], $arenaUnparsed["pos$i"][2], $level);
					${"edge$i"} = new Position($arenaUnparsed["edge$i"][0], $arenaUnparsed["edge$i"][1], $arenaUnparsed["edge$i"][2], $level);
				}
				if (isset($pos1, $pos2, $edge1, $edge2)) {
					$this->createArena($name, $pos1, $pos2, $edge1, $edge2, $level, $arenaUnparsed["type"], $arenaUnparsed["maxBuildHeight"]);
				} else {
					$this->getPlugin()->getLogger()->error(TextFormat::RED . "The positions have been corrupted!");
				}
			}
			$this->getPlugin()->getLogger()->info(TextFormat::GREEN . "The arenas have been loaded! Number of arenas: " . count($this->getArenas()));
		} else {
			$this->getPlugin()->getLogger()->info(TextFormat::RED . "There are no arenas to load!");
		}
	}

	public function getNextArenaIndex() {
		return count($this->arenas);
	}

	public function addArena(Arena $arena) {
		$this->arenas[$arena->getId()] = $arena;
	}

	public function createArena(int $index, Position $pos1, Position $pos2, Position $edge1, Position $edge2, Level $level, int $type, int $maxBuildHeight) {
		$arena = new Arena($index, [$pos1, $pos2], [$edge1, $edge2], $level, $type, $maxBuildHeight);
		$this->addArena($arena);
		$arenas = $this->getConfig()->get("arenas");
		if (!isset($arenas[$index])) {
			$arenas[$index] = $arena->toYAML();
			$this->getConfig()->set("arenas", $arenas);
			$this->getConfig()->save();
		}
	}

	/**
	 * @param int $index
	 * @return bool
	 */
	public function removeArena(int $index): bool {
		if ($this->getArenaById($index) !== null) {
			unset($this->arenas[$index]);
			return true;
		}
		return false;
	}

	public function deleteArena(int $index) {
		$arenas = $this->getConfig()->get("arenas");
		if (isset($arenas[$index]) and isset($this->arenas[$index])) {
			unset($arenas[$index]);
			$this->removeArena($index);
			$arenas = array_values($arenas);
			$this->reorderArenas();
			$this->getConfig()->set("arenas", $arenas);
			$this->getConfig()->save();
			return TextFormat::GREEN . "Arena successfully deleted!";
		}
		return TextFormat::RED . "Arena could not be deleted!";
	}

	public function reorderArenas() {
		$this->arenas = array_values($this->arenas);
		foreach ($this->getArenas() as $index => $arena) {
			if ($arena->getId() !== $index) {
				$arena->setId($index);
			}
		}
	}

	/**
	 * @param int $index
	 * @return Arena|null
	 */
	public function getArenaById(int $index) {
		return $this->arenas[$index] ?? null;
	}

	/**
	 * @return Arena[]
	 */
	public function getArenas(): array {
		return $this->arenas;
	}

	public function getConfig(): Config {
		return $this->config;
	}

	/**
	 * @return Arena[]
	 */
	public function getOpenArenas(): array {
		$arenas = [];
		if (count($this->getArenas()) > 0) {
			foreach ($this->getArenas() as $arena) {
				if (!$arena->inUse()) {
					$arenas[] = $arena;
				}
			}
		}
		return $arenas;
	}

	/**
	 * @param int $type
	 * @return Arena|null
	 */
	public function getOpenArena(int $type) {
		if (count($this->getOpenArenas()) > 0) {
			$typeArenas = [];
			foreach ($this->getOpenArenas() as $arena) {
				if ($arena->getType() === $type) {
					$typeArenas[] = $arena;
				}
			}
			if (count($typeArenas) > 0) {
				$arena = $typeArenas[array_rand($typeArenas, 1)];
				return $arena;
			}
		}
		return null;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	public function addLobbyItems(ArenaPlayer $player) {
		$player->getInventory()->clearAll();
		$items = $this->getLobbyItems();
		$player->getInventory()->resetHotbar();
		for($i = 0; $i < count($items); $i++){
			$index = $player->getInventory()->getHotbarSlotIndex($i);
			$player->getInventory()->setItem($index, $items[$i]);
		}
	}

	/**
	 * @return Item[]
	 */
	public function getLobbyItems(): array {
		$items = [
			[Item::DIAMOND_SWORD, TextFormat::AQUA."Join Ranked Queue"],
			[Item::GOLDEN_SWORD, TextFormat::DARK_AQUA."Join Unranked Queue"],
			[Item::EMPTY_MAP, TextFormat::LIGHT_PURPLE."Start Party Event"],
		];
		$list = [];
		foreach($items as $item){
			$list[] = Item::get($item[0])->setCustomName($item[1]);
		}
		return $list;
	}
}