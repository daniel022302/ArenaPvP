<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 7:39 PM
 */

namespace sys\arenapvp\kit;


use pocketmine\entity\Effect;
use pocketmine\item\Item;
use sys\arenapvp\ArenaPlayer;

class Kit {

    /** @var string */
    private $name;

    /** @var Item */
    private $icon;

    /** @var Effect[] */
    private $effects = [];

    /** @var Item[] */
    private $armor = [];

    /** @var Item[] */
    private $items = [];

    /** @var int */
    private $mapType;

    /** @var bool */
    private $regenActive;

    /** @var bool */
    private $allowsBuilding;

    public function __construct(string $name, Item $icon, array $armor, array $items, int $mapType, bool $regenActive, bool $allowsBuilding) {
        $this->name = $name;
        $this->icon = $icon;
        $this->armor = $armor;
        $this->items = $items;
        $this->mapType = $mapType;
        $this->regenActive = $regenActive;
        $this->allowsBuilding = $allowsBuilding;
    }

	/**
	 * @return bool
	 */
    public function shouldRegen(): bool {
    	return $this->regenActive;
    }

	/**
	 * @return bool
	 */
    public function canBuild(): bool {
    	return $this->allowsBuilding;
    }

	/**
	 * @return array
	 */
    public function getArmor(): array {
        return $this->armor;
    }

	/**
	 * @return Item
	 */
    public function getIcon(): Item {
        return $this->icon;
    }

	/**
	 * @return array
	 */
    public function getItems(): array {
        return $this->items;
    }

	/**
	 * @return string
	 */
    public function getName(): string {
        return $this->name;
    }

	/**
	 * @param string $name
	 * @return bool
	 */
    public function isKit(string $name): bool {
    	return strtolower($this->getName()) == strtolower($name);
    }

	/**
	 * @return Effect[]
	 */
    public function getEffects(): array {
        return $this->effects;
    }

	/**
	 * @return int
	 */
	public function getMapType(): int {
		return $this->mapType;
	}

    public function addEffect(Effect $effect){
        if(!isset($this->effects[$effect->getId()])){
            $this->effects[$effect->getId()] = $effect;
        }
    }

    public function giveKit(ArenaPlayer $player) {
    	$player->getInventory()->setContents($this->getItems());
    	$player->getInventory()->setArmorContents($this->getArmor());
    	if(count($this->getEffects()) > 0) {
    		foreach($this->getEffects() as $effect) $player->addEffect($effect);
	    }
	    $player->getInventory()->sendContents($player);
    }

}