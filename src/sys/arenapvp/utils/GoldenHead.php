<?php
/**
 *
 * This file was created by Matt on 7/17/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\utils;

use pocketmine\entity\Effect;
use pocketmine\item\Food;

class GoldenHead extends Food {

	public function __construct($id = self::GOLDEN_APPLE, $meta = 1, $count = 1) {
		parent::__construct($id, $meta, $count, "Golden Head", 4, 9.6, false, [], "air");
	}


	public function getAdditionalEffects(): array {
		$duration = 20 * ($this->getDamage() == 1 ? 10 : 5);
		return [
			Effect::getEffect(Effect::ABSORPTION)->setDuration(20 * 120),
			Effect::getEffect(Effect::REGENERATION)->setAmplifier(1)->setDuration($duration)
			];
	}

}