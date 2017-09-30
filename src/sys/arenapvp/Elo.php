<?php
/**
 *
 * This file was created by Matt on 7/18/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp;


use pocketmine\utils\TextFormat;
use sys\arenapvp\kit\Kit;

class Elo {

	const DEFAULT_ELO = 1500;

	/** @var Kit */
	private $kit;

	/** @var int */
	private $elo;

	public function __construct(Kit $kit, int $elo = self::DEFAULT_ELO) {
		$this->kit = $kit;
		$this->elo = $elo;
	}

	/**
	 * @return Kit
	 */
	public function getKit(): Kit {
		return $this->kit;
	}

	/**
	 * @return int
	 */
	public function getElo(): int {
		return $this->elo;
	}

	/**
	 * @param int $elo
	 */
	public function setElo(int $elo) {
		$this->elo = $elo;
	}

	public function calculateNewElo(ArenaPlayer $winner, ArenaPlayer $loser) {
		$winnerElo = $winner->getElo($this->getKit())->getElo();
		$loserElo = $loser->getElo($this->getKit())->getElo();

		$winnerEstimate = (int) (10 ^ ($winnerElo / 400));
		$loserEstimate = (int) (10 ^ ($loserElo / 400));


		$winnerEstimatedElo = $winnerEstimate / ($winnerEstimate + $loserEstimate);
		$winnerKFactor = $winner->getKFactor($this->getKit());

		$winnerAddition = intval($winnerKFactor * (1 - $winnerEstimatedElo));

		$winner->sendArgsMessage(TextFormat::GREEN."You gained {0} ELO!", $winnerAddition);

		$winner->boostElo($this->getKit(), $winnerAddition);
		$winner->saveElo($this->getKit());

		$winner->sendArgsMessage(TextFormat::GOLD."Your ELO for {0}: {1}", $this->getKit()->getName(), $this->getElo());

		$loserKFactor = $loser->getKFactor($this->getKit());

		$loserEstimatedElo = $loserEstimate / ($winnerEstimate + $loserEstimate);

		$loserSubtraction = intval($loserKFactor * (0 - $loserEstimatedElo));

		$loser->sendArgsMessage(TextFormat::RED."You lost {0} ELO!", $loserSubtraction);

		$loser->boostElo($this->getKit(), $loserSubtraction);
		$loser->saveElo($this->getKit());
		$loser->sendArgsMessage(TextFormat::GOLD."Your ELO for {0}: {1}", $this->getKit()->getName(), $this->getElo());

	}

}