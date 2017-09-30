<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 7:43 PM
 */

namespace sys\arenapvp\kit;


use pocketmine\entity\Effect;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use sys\arenapvp\arena\ArenaManager;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\utils\CustomBow;
use sys\arenapvp\utils\GoldenHead;

class KitManager {

	/** @var Config */
	private $config;

	/** @var Kit[] */
	private $kits = [];

	/** @var ArenaPvP */
	private $plugin;

	public function __construct(ArenaPvP $plugin) {
		$this->plugin = $plugin;
		$plugin->saveResource("kits.json", false);
		$this->config = new Config($plugin->getDataFolder() . "kits.json", Config::JSON);
		Item::registerItem(new CustomBow(Item::BOW, 0, 1, "Bow", 385));
		Item::registerItem(new GoldenHead());
		$this->loadKits();
	}

	public function loadKits() {
		foreach ($this->getConfig()->getAll() as $kit => $data) {
			$armor = $this->parseArmor($data);
			$items = $this->parseItems($data);
			$icon = $data["icon"];
			if (is_array($icon)) {
				$iconItem = Item::get($icon[0], $icon[1]);
			} else {
				$iconItem = Item::get($icon);
			}
			$iconItem->setCustomName(TextFormat::GREEN . $kit);

			$kitClass = new Kit($kit, $iconItem, $armor, $items, $data["mapType"] ?? ArenaManager::TERRAIN, $data["shouldRegen"] ?? true, $data["allowsBuilding"] ?? false);
			if (isset($data["effects"]) and count($data["effects"]) > 0) {
				foreach ($data["effects"] as $effect) {
					if ($effect[2] == "infinite") {
						$effect[2] = INT32_MAX;
					}
					$kitClass->addEffect(Effect::getEffect($effect[0])->setAmplifier($effect[1])->setDuration($effect[2]));
				}
			}
			$this->addKit($kitClass);
		}
		$this->getPlugin()->getLogger()->info(TextFormat::GREEN . "The kits have been loaded! Number of kits: " . count($this->getKits()));
	}

	public function addKit(Kit $kit) {
		$this->kits[$kit->getName()] = $kit;
	}

	/**
	 * @param string $name
	 * @return null|Kit
	 */
	public function getKitByName(string $name) {
		foreach ($this->getKits() as $kit) {
			if ($kit->isKit($name)) {
				return $kit;
			}
		}
		return null;
	}

	/**
	 * @param array $data
	 * @return Item[]
	 */
	public function parseItems(array $data): array {
		$parsedItems = [];
		foreach ($data["items"] as $item) {
			$parsedItem = Item::get($item["id"], $item["meta"]);
			if (isset($item["enchantments"])) {
				foreach ($item["enchantments"] as $enchantment) {
					$parsedItem->addEnchantment(Enchantment::getEnchantment($enchantment[0])->setLevel($enchantment[1]));
				}
			}
			if (isset($item["customName"])) {
				$parsedItem->setCustomName($item["customName"]);
			}
			if (($item["count"] > $parsedItem->getMaxStackSize()) or ($item["id"] == Item::AIR and $item["count"] > 1) or ($item["id"] == Item::SPLASH_POTION and $item["count"] > 1)) {
				for ($i = 1; $i <= $item["count"]; $i++) {
					$parsedItems[] = $parsedItem;
				}
			} else {
				$parsedItem->setCount($item["count"]);
				$parsedItems[] = $parsedItem;
			}
		}
		return $parsedItems;
	}

	/**
	 * @param array $data
	 * @return Item[]
	 */
	public function parseArmor(array $data): array {
		$parsedArmor = [];
		$armor[] = $data["helmet"];
		$armor[] = $data["chestplate"];
		$armor[] = $data["leggings"];
		$armor[] = $data["boots"];
		foreach ($armor as $item) {
			$parsedItem = Item::get($item[0]);
			if (isset($item[1])) {
				foreach ($item[1] as $ench) {
					$parsedItem->addEnchantment(Enchantment::getEnchantment($ench[0])->setLevel($ench[1]));
				}
			}
			$parsedArmor[] = $parsedItem;
		}
		return $parsedArmor;
	}

	/**
	 * @return Config
	 */
	public function getConfig(): Config {
		return $this->config;
	}

	/**
	 * @return Kit[]
	 */
	public function getKits(): array {
		return $this->kits;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	/**
	 * @return Item[]
	 */
	public function getAllKitItems(): array {
		$items = [];
		foreach ($this->getKits() as $kit) {
			$items[] = $kit->getIcon();
		}
		return $items;
	}

}
