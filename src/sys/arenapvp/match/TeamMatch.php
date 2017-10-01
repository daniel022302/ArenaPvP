<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 7:11 PM
 */

namespace sys\arenapvp\match;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use sys\arenapvp\arena\Arena;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\kit\Kit;

class TeamMatch extends Match {

	/** @var Team[] */
	private $teams = [];

	/** @var Team */
	private $winningTeam = null;

	/**
	 * Match constructor.
	 * @param ArenaPvP $plugin
	 * @param Kit $kit
	 * @param ArenaPlayer[] $players
	 * @param Arena $arena
	 * @param bool $ranked
	 */
	public function __construct(ArenaPvP $plugin, Kit $kit, array $players, Arena $arena, bool $ranked = false) {
		parent::__construct($plugin, $kit, $players, $arena, $ranked);
	}

	/**
	 * parent::init() would be nice here
	 */
	public function init() {
		$this->shufflePlayers();
		foreach ($this->getPlayers() as $player) {
			$player->reset(ArenaPlayer::SURVIVAL);
			$player->setMatch($this);
			$this->getBossBar()->addBossBar($player);
			$this->getBossBar()->setBossBarProgress(20);
		}
		/** @var ArenaPlayer[][] $split */
		$split = array_chunk($this->getPlayers(), ceil(count($this->getPlayers()) / 2));
		for ($i = 0; $i <= 1; $i++) {
			$team = new Team();
			$this->addTeam($i, $team);
			foreach ($split[$i] as $player) {
				$this->setTeam($player, $i);
				$this->setMatchPosition($player, $i);
				$this->getBossBar()->setBossBarTitle(TextFormat::GRAY . "Starting in " . TextFormat::GOLD . gmdate("i:s", $this->countdown) . TextFormat::GRAY . "...");
			}
		}
		$this->teleportPlayers();
	}

	public function setTeam(ArenaPlayer $player, int $index) {
		$team = $this->teams[$index];
		if (!$team->hasPlayer($player)) {
			$team->addPlayer($player);
		}
	}

	public function addTeam(int $index, Team $team) {
		$this->teams[$index] = $team;
	}

	public function setMatchPosition(ArenaPlayer $player, int $index) {
		$this->positions[$player->getName()] = $this->getArena()->getPosition($index);
	}

	/**
	 * @param ArenaPlayer $player
	 * @return Position|null
	 */
	public function getMatchPosition(ArenaPlayer $player) {
		return $this->positions[$player->getName()] ?? null;
	}

	/**
	 * @param ArenaPlayer $player
	 */
	public function sendNameTags(ArenaPlayer $player) {
		foreach ($this->getTeams() as $team) {
			$opponentTeam = $this->getOtherTeam($team);
			if ($opponentTeam instanceof Team) {
				foreach ($team->getPlayers() as $player) {
					$player->setCustomNameTag(TextFormat::GREEN . $player->getName(), $team->getPlayers());
				}
				foreach ($opponentTeam->getPlayers() as $player) {
					$player->setCustomNameTag(TextFormat::RED . $player->getName(), $team->getPlayers());
				}
			}
		}
	}

	public function onDamage(EntityDamageEvent $event) {
		$player = $event->getEntity();
		if ($player instanceof ArenaPlayer and $event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if ($damager instanceof ArenaPlayer and !$damager->isSpectating()) {
				$team = $this->getTeam($damager);
				$otherTeam = $this->getTeam($player);
				if ($team instanceof Team and $otherTeam instanceof Team) {
					if ($team->onTeam($player, $damager) and $otherTeam->onTeam($player, $damager)) {
						$event->setCancelled();
					}
				}
			}
		}
		parent::onDamage($event);
	}

	/**
	 * @param Team $team
	 * @return null|Team
	 */
	public function getOtherTeam(Team $team) {
		foreach ($this->getTeams() as $matchTeam) {
			if ($team === $matchTeam) {
				continue;
			}
			return $matchTeam;
		}
		return null;
	}

	/**
	 * @return Team[]
	 */
	public function getTeams(): array {
		return $this->teams;
	}

	/**
	 * @param ArenaPlayer $player
	 * @return null|Team
	 */
	public function getTeam(ArenaPlayer $player) {
		foreach ($this->getTeams() as $team) {
			if ($team->hasPlayer($player)) {
				return $team;
			}
		}
		return null;
	}

	public function setWinningTeam(Team $team) {
		$this->winningTeam = $team;
	}

	/**
	 * @return Team|null
	 */
	public function getWinningTeam() {
		return $this->winningTeam ?? null;
	}

	/**
	 * @return Team|null
	 */
	public function checkTeams() {
		foreach ($this->getTeams() as $team) {
			if ($team->isDead()) return $team;
		}
		return null;
	}

	public function sendFightingMessage() {
		foreach ($this->getTeams() as $team) {
			$oppositeTeamString = $this->getOtherTeam($team)->__toString();
			$team->sendArgsMessage(TextFormat::GOLD . "Now in match against: {0}", $oppositeTeamString);
		}
	}

	public function handleDeath(ArenaPlayer $player = null, $leaving = false) {
		if ($this->isPlayer($player)) {
			$this->removePlayer($player);
			$team = $this->getTeam($player);
			if ($team instanceof Team) {
				$team->subtractPlayerCount();
			}

			$teamDead = $this->checkTeams();
			if ($teamDead instanceof Team) {
				$this->getArena()->getLevel()->dropItem($player->getPosition(), $player->getInventory()->getItemInHand());
				foreach ($this->getPlayers() as $arenaPlayer) {
					$arenaPlayer->setHealth($player->getMaxHealth());
					$arenaPlayer->setGamemode(ArenaPlayer::CREATIVE);
					$this->setWinningTeam($this->getOtherTeam($team));
				}
				$this->broadcastArgsMessage(TextFormat::GREEN . "Winners: {0}", $this->getWinningTeam()->__toString());
				$this->triggerKillTask();
			} else {
				$player->dropAllItems();
			}

			if (!$leaving) $this->addSpectator($player, true);

		}
	}

	public function nullify() {
		$this->teams = null;
		$this->winningTeam = null;
		parent::nullify();
	}


	public function kill() {
		foreach ($this->teams as $team) $team->nullify();
		parent::kill();
	}

}