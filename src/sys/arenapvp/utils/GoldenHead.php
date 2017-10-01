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

	public function __construct($meta = 1) {
		parent::__construct($meta);

	}

	public function getAdditionalEffects(): array {
		return [Effect::getEffect(Effect::ABSORPTION)->setDuration(20 * 120), Effect::getEffect(Effect::REGENERATION)->setAmplifier(1)->setDuration(20 * ($this->getDamage() == 1 ? 10 : 5))];
	}

}