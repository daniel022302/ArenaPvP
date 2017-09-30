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
use pocketmine\item\GoldenApple;

class GoldenHead extends GoldenApple {

	public function __construct($id = self::GOLDEN_APPLE) {
		parent::__construct($id);

	}

	public function getAdditionalEffects(): array {
		return [Effect::getEffect(Effect::ABSORPTION)->setDuration(20 * 120), Effect::getEffect(Effect::REGENERATION)->setAmplifier(1)->setDuration(20 * ($this->getDamage() == 1 ? 10 : 5))];
	}

}