<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 2/10/2017
 * Time: 9:48 PM
 */

namespace sys\arenapvp;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;
use sys\arenapvp\basefiles\BaseListener;
use sys\arenapvp\match\Match;
use sys\arenapvp\menu\defaults\DuelAcceptMenu;
use sys\arenapvp\utils\ArenaChest;
use sys\arenapvp\utils\ArenaChestInventory;

class LobbyListener extends BaseListener {

	public function __construct(ArenaPvP $plugin) {
		parent::__construct($plugin);
		$this->init();
	}

	private function init() {
		Tile::registerTile(ArenaChest::class);
	}

	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if ($player->inQueue()) {
				$player->removeFromQueue();
			}
			if ($this->getPlugin()->getPartyManager()->hasInviteObject($player)) {
				$this->getPlugin()->getPartyManager()->removeHostObject($player);
			}
			if ($player->inParty()) {
				if ($player->getName() === $player->getParty()->getLeader()->getName()) {
					$this->getPlugin()->getPartyManager()->removeParty($player->getParty());
					$player->getParty()->disbandParty();
				} else {
					$party = $player->getParty();
					$party->removePlayer($player);
				}
			}
		}
	}

	public function onKick(PlayerKickEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if ($player->inQueue()) {
				$player->removeFromQueue();
			}
			if ($this->getPlugin()->getPartyManager()->hasInviteObject($player)) {
				$this->getPlugin()->getPartyManager()->removeHostObject($player);
			}
			if ($player->inParty()) {
				if ($player->getName() === $player->getParty()->getLeader()->getName()) {
					$this->getPlugin()->getPartyManager()->removeParty($player->getParty());
					$player->getParty()->disbandParty();
				} else {
					$party = $player->getParty();
					$party->removePlayer($player);
				}
			}
		}
	}

	public function onDropItem(PlayerDropItemEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if (!$player->getMatch() instanceof Match and !$player->isOp()) {
				$event->setCancelled();
			}
		}
	}

	public function onPickup(InventoryPickupItemEvent $event) {
		$player = $event->getInventory()->getHolder();
		if ($player instanceof ArenaPlayer) {
			if (!$player->getMatch() instanceof Match and !$player->isOp()) {
				$event->setCancelled();
			}
			if ($player->isSpectating()) {
				$event->setCancelled();
			}
		}
	}

	public function onCombust(EntityCombustEvent $event) {
		$player = $event->getEntity();
		if ($player instanceof ArenaPlayer) {
			if (!$player->inMatch() or $player->isSpectating() or $event instanceof EntityCombustByEntityEvent) {
				$event->setCancelled();
			}
		}
	}

	public function onInteract(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		$action = $event->getAction();
		if ($action !== PlayerInteractEvent::LEFT_CLICK_AIR) {
			if ($player instanceof ArenaPlayer and !$player->inMenu()) {
				$item = $event->getItem();
				foreach ($this->getPlugin()->getInteractionManager()->getInteractions() as $interaction) {
					if ($interaction->exists($item)) {
						$event->setCancelled();
						$interaction->onInteract($player, $item);
					}
				}
			}
		}
	}

	public function onPacketReceive(DataPacketReceiveEvent $event) {
		$packet = $event->getPacket();
		if ($packet instanceof LoginPacket) {
			$player = $event->getPlayer();
			if ($player instanceof ArenaPlayer) {
				if (isset($packet->clientData["DeviceOS"])) {
					$player->setClientOs($packet->clientData["DeviceOS"]);
				}
			}
		}
	}

	public function onDeath(PlayerDeathEvent $event) {
		$event->setDeathMessage(null);
	}

	public function onRespawn(PlayerRespawnEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			$this->getPlugin()->getArenaManager()->addLobbyItems($player);
		}
	}

	public function onDamage(EntityDamageEvent $event) {
		$player = $event->getEntity();
		if ($player instanceof ArenaPlayer) {
			if ($player->isSpectating()) {
				$event->setCancelled();
			}
			if ($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				if ($damager instanceof ArenaPlayer) {
					if ($damager->isSpectating()) {
						$event->setCancelled();
					}
				}

			}
		}
	}

	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			$player->loadElo();
			$player->setNameTag(TextFormat::GRAY . $player->getName());
			$player->reset();
			$this->getPlugin()->getArenaManager()->addLobbyItems($player);
		}
	}

	public function onCreation(PlayerCreationEvent $event) {
		$event->setPlayerClass(ArenaPlayer::class);
	}

	public function onTransaction(InventoryTransactionEvent $event) {
		$chestInventory = null;
		$transaction = null;

		$player = $event->getTransaction()->getSource();

		foreach ($event->getTransaction()->getInventories() as $inventory) {
			if ($inventory instanceof ArenaChestInventory) {
				$chestInventory = $inventory;
				break;
			}
		}

		foreach ($event->getTransaction()->getActions() as $action) {
			if (!$action or $action instanceof DropItemAction) {
				continue;
			}
			$transaction = $action;
		}

		$item = $transaction->getSourceItem();
		if ($player instanceof ArenaPlayer and $chestInventory instanceof ArenaChestInventory and $item instanceof Item and $player->inMenu()) {
			$player->getMenu()->getInteraction($player, $chestInventory, $item);
			$event->setCancelled();
		}

	}

	public function onBreak(BlockBreakEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if ($player->isSpectating()) {
				$event->setCancelled();
			}
		}
	}

	public function onPlace(BlockPlaceEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if ($player->isSpectating()) {
				$event->setCancelled();
			}
		}
	}

	/**
	 * @param PlayerExhaustEvent $event
	 */
	public function onExhaust(PlayerExhaustEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer and (!$player->inMatch() or $player->isSpectating())) {
			$event->setCancelled();
		}
	}

	/**
	 * @param InventoryCloseEvent $event
	 */
	public function onClose(InventoryCloseEvent $event) {
		$player = $event->getPlayer();
		if ($player instanceof ArenaPlayer) {
			if ($player->inMenu()) {
				$menu = $player->getMenu();
				if ($menu instanceof DuelAcceptMenu and $player->hasDuelRequest()) { //accidental exit of menu
					$player->setHasDuelRequest(false);
					$player->sendArgsMessage(TextFormat::GREEN . "You have denied {0}'s duel request!", $menu->getOpponent()->getName());
					$menu->getOpponent()->sendArgsMessage(TextFormat::RED . "{0} has denied your duel request!", $player->getName());
					return;
				}
				$player->removeMenu();
			}
		}
	}

}