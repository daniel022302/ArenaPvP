<?php
/**
 *
 * This file was created by Matt on 9/30/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\utils;


use pocketmine\entity\Attribute;
use pocketmine\entity\Squid;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;


/*
 * TODO: Rewrite this a bit
 */

class BossBar {

	/** @var ArenaPvP */
	private $plugin;

	/** @var string */
	private $title = "";

	/** @var Player[] */
	private $players = [];

	const ENTITY_ID = 2500;

	public static $Y_SUBTRACTION = 10; //I personally wouldn't go too far away :P

	public function __construct(ArenaPvP $plugin) {
		$this->plugin = $plugin;
	}

	public function hasPlayer(Player $player) {
		return isset($this->players[$player->getName()]);
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * @return ArenaPvP
	 */
	public function getPlugin(): ArenaPvP {
		return $this->plugin;
	}

	public function getPlayers(): array {
		return $this->players;
	}

	public function addBossBar(ArenaPlayer $player) {
		$this->addEntity($player);
		$this->players[$player->getName()] = $player;
		$pk = new BossEventPacket();
		$pk->unknownShort = 1;
		$pk->overlay = 1;
		$pk->color = 1;
		$pk->bossEid = self::ENTITY_ID;
		$pk->eventType = BossEventPacket::TYPE_SHOW;
		$pk->title = "";
		$pk->healthPercent = 0;
		$player->dataPacket($pk);
	}

	public function removeBossBar(ArenaPlayer $player) {
		unset($this->players[$player->getName()]);
		$this->removeEntity($player);
	}

	public function setBossBarProgress(int $value) {
		$pk = new UpdateAttributesPacket();
		$pk->entries[] = Attribute::getAttribute(Attribute::HEALTH)->setValue($value);
		$pk->entityRuntimeId = self::ENTITY_ID;
		foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
			$player->dataPacket($pk);
		}
	}

	public function setBossBarTitle(string $title) {
		$this->title = $title;
		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = self::ENTITY_ID;
		$pk->metadata = [Squid::DATA_NAMETAG => [Squid::DATA_TYPE_STRING, "\n\n" . $title]];
		foreach ($this->getPlayers() as $player) {
			$player->dataPacket($pk);
		}

	}

	public function setEntitySize(float $size) {
		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = self::ENTITY_ID;
		$pk->metadata = [Squid::DATA_SCALE => [Squid::DATA_TYPE_FLOAT, $size]];
		foreach ($this->getPlayers() as $player) {
			$player->dataPacket($pk);
		}
	}

	public function addEntity(ArenaPlayer $player) {
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = self::ENTITY_ID;
		$pk->type = Squid::NETWORK_ID;
		$pk->x = $player->getX();
		$pk->y = $player->getY() - self::$Y_SUBTRACTION;
		$pk->z = $player->getZ();
		$pk->speedX = 0;
		$pk->speedY = 0;
		$pk->speedZ = 0;
		$pk->yaw = 0;
		$pk->pitch = 0;
		$flags = 0;
		$flags |= 1 << Squid::DATA_FLAG_INVISIBLE;
		$flags |= 1 << Squid::DATA_FLAG_IMMOBILE;
		$flags |= 1 << Squid::DATA_FLAG_SILENT;
		$pk->metadata = [Squid::DATA_FLAGS => [Squid::DATA_TYPE_LONG, $flags], Squid::DATA_AIR => [Squid::DATA_TYPE_SHORT, 400], Squid::DATA_MAX_AIR => [Squid::DATA_TYPE_SHORT, 400], Squid::DATA_NAMETAG => [Squid::DATA_TYPE_STRING, "\n\n" . ""], //The two line breaks are so the text don't overlap with the boss bar.
			Squid::DATA_LEAD_HOLDER_EID => [Squid::DATA_TYPE_LONG, -1], Squid::DATA_SCALE => [Squid::DATA_TYPE_FLOAT, 0.01], Squid::DATA_BOUNDING_BOX_WIDTH => [Squid::DATA_TYPE_FLOAT, 0], Squid::DATA_BOUNDING_BOX_HEIGHT => [Squid::DATA_TYPE_FLOAT, 0]];
		$player->dataPacket($pk);
	}

	public function moveEntity(ArenaPlayer $player) {
		$pk = new MoveEntityPacket();
		$pk->entityRuntimeId = self::ENTITY_ID;
		$pk->x = $player->getX();
		$pk->y = $player->getY() - self::$Y_SUBTRACTION;
		$pk->z = $player->getZ();
		$pk->pitch = 0;
		$pk->headYaw = 0;
		$pk->yaw = 0;
		$player->dataPacket($pk);
	}

	public function removeEntity(ArenaPlayer $player) {
		$pk = new RemoveEntityPacket();
		$pk->entityUniqueId = self::ENTITY_ID;
		$player->dataPacket($pk);
	}


}