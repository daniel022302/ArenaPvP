<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/19/2017
 * Time: 8:51 PM
 */

namespace sys\arenapvp\menu;


use pocketmine\item\Item;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\utils\ArenaChestInventory;

abstract class Menu {

    /** @var ArenaPvP */
    private $plugin;

    /** @var Item[] */
    private $items;

    /**
     * Menu constructor.
     * @param ArenaPvP $plugin
     * @param Item[] $items
     */
    public function __construct(ArenaPvP $plugin, array $items){
        $this->plugin = $plugin;
        $this->items = $items;
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
    public function getItems(): array {
        return $this->items;
    }

    public abstract function getInteraction(ArenaPlayer $player, ArenaChestInventory $inventory, Item $item);


}