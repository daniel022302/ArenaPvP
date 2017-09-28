<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 7:08 PM
 */

namespace sys\arenapvp\match;

use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\kit\Kit;
use sys\arenapvp\task\MatchTask;

class MatchManager {

    const MAX_MATCHES = 80;

    /** @var MatchHeartbeat */
    private $heartbeat;

    /** @var ArenaPvP */
    private $plugin;

    /** @var Match[] */
    private $matches = [];

    public function __construct(ArenaPvP $plugin) {
        $this->plugin = $plugin;
        $this->heartbeat = new MatchHeartbeat($plugin, $this);
    }

	/**
	 * @return Match[]
	 */
    public function getMatches(): array {
        return $this->matches;
    }

	/**
	 * @return ArenaPvP
	 */
    public function getPlugin(): ArenaPvP {
        return $this->plugin;
    }

    public function tickMatches() {
        foreach($this->getMatches() as $match) {
            $match->tick();
        }
    }

    public function addMatch(Match $match) {
        $this->matches[$match->getId()] = $match;
    }

    public function removeMatch(Match $match) {
        if(isset($this->matches[$match->getId()])) {
            unset($this->matches[$match->getId()]);
        }
    }

    /**
     * @param ArenaPlayer[] $players
     * @param Kit $kit
     * @param bool $teams
     */
    public function createMatch(array $players, Kit $kit, $teams = false) {
        $arena = $this->getPlugin()->getArenaManager()->getOpenArena($kit->getMapType());
        if($arena !== null) {
	        new MatchTask($this->getPlugin(), $players, $kit, $arena, $teams);
        } else {
            foreach($players as $player) {
                $player->sendMessage(TextFormat::RED."No arenas are available at this time!");
            }
        }
    }

}