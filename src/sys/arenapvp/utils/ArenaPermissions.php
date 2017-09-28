<?php
/**
 *
 * This file was created by Matt on 7/21/2017
 * Any attempts to copy, steal, or use this code
 * without permission will result in various consequences.
 *
 */

namespace sys\arenapvp\utils;



interface ArenaPermissions {


	const ARENA_PERMISSIONS = "arena.permissions.";

	const PERMISSION_SPECTATE = self::ARENA_PERMISSIONS."spectate";
	const PERMISSION_PARTY_HERO = self::ARENA_PERMISSIONS."heroParty";
	const PERMISSION_PARTY_HERO_PLUS = self::ARENA_PERMISSIONS."heroPlusParty";
	const PERMISSION_PARTY_PARANORMAL = self::ARENA_PERMISSIONS."paranormalParty";


}