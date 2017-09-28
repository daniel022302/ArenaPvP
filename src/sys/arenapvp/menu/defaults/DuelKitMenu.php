<?php

/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/21/2017
 * Time: 9:17 PM
 */

namespace sys\arenapvp\menu\defaults;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\utils\ArenaChestInventory;
use sys\arenapvp\menu\Menu;

class DuelKitMenu extends Menu {

    /** @var ArenaPlayer */
    private $opponent = null;

    /**
     * Menu constructor.
     * @param ArenaPvP $plugin
     * @param ArenaPlayer $opponent
     */
    public function __construct(ArenaPvP $plugin, ArenaPlayer $opponent) {
        $this->opponent = $opponent;
        parent::__construct($plugin,  $plugin->getKitManager()->getAllKitItems());
    }

    public function getOpponent(){
        return $this->opponent;
    }

    public function getInteraction(ArenaPlayer $player, ArenaChestInventory $inventory, Item $item) {
        $kit = $this->getPlugin()->getKitManager()->getKitByName(TextFormat::clean($item->getCustomName()));
        if($kit !== null) {
	        if($this->getOpponent()->fullyInMatch()) return;
	        $menu = new DuelAcceptMenu($this->getPlugin(), $player, $kit);
            $this->getOpponent()->addMenu($menu);
            $this->getOpponent()->setHasDuelRequest();
            $this->getOpponent()->sendMenu(TextFormat::GRAY."Duel Request from ".TextFormat::GOLD.$this->getOpponent()->getPlayerName().TextFormat::GRAY."!");
            $player->sendMessage(TextFormat::GREEN."Successfully sent a duel request to ".$this->getOpponent()->getPlayerName()."!");
	        $player->removeMenu();
	        $inventory->onClose($player);
        }
    }
}