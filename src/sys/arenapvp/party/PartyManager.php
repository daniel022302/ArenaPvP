<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 3/1/2017
 * Time: 4:11 PM
 */

namespace sys\arenapvp\party;


use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;

class PartyManager {

    /** @var Invite[] */
    private $invites = [];

    /** @var Party[] */
    private $parties = [];

    /** @var ArenaPvP */
    private $plugin;

    public function __construct(ArenaPvP $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin() {
        return $this->plugin;
    }

    public function getParties(){
        return $this->parties;
    }

    public function addParty(Party $party){
        if(!isset($this->parties[$party->getId()])){
            $this->parties[$party->getId()] = $party;
        }
    }

    public function removeParty(Party $party){
        if(isset($this->parties[$party->getId()])){
            unset($this->parties[$party->getId()]);
        }
    }

	/**
	 * @param string $name
	 * @return Party
	 */
    public function getPartyByInvite(string $name): Party {
        return $this->getPlayer($name)->getParty();
    }

    public function getPartyFromPlayer(ArenaPlayer $player){
        foreach($this->getParties() as $party){
            if($party->getMember($player->getPlayerName()) !== null){
                return $party;
            }
        }
        return null;
    }

    public function hasInvite(ArenaPlayer $player, string $playerName = ""){
	    $host = $this->getPlayer($playerName);
	    if($host instanceof ArenaPlayer) {
		    foreach($this->invites as $invite) {
			    if($invite->getFrom()->getName() == $host->getName()) {
			    	return $invite->isInvited($player);
			    }
		    }
	    }
        return false;
    }

	/**
	 * @param ArenaPlayer $player
	 * @param string $playerName
	 * @return bool|Invite
	 */
    private function getInvite(ArenaPlayer $player, string $playerName = ""){
	    $host = $this->getPlayer($playerName);
	    if($host instanceof ArenaPlayer and $this->hasInvite($player, $playerName)) {
		    foreach($this->invites as $invite) {
			    if($invite->getFrom()->getName() == $host->getName() and $invite->isInvited($player)) {
				    return $invite;
			    }
		    }
	    }
	    return false;
    }

    public function acceptInvite(ArenaPlayer $player, string $playerName = ""){
        if($this->hasInvite($player, $playerName)){
        	$invite = $this->getInvite($player, $playerName);
	        $invite->getFrom()->sendMessage(TextFormat::GREEN.$player->getPlayerName(). " has accepted the invite!");
	        if($invite->getFrom()->inParty()) {
        		$invite->getFrom()->getParty()->addPlayer($player);
	        } else {
        		$party = new Party($invite->getFrom());
        		$party->addPlayer($player);
        		$this->addParty($party);
	        }
	        return TextFormat::GREEN."You have successfully accepted the party invite!";
        } else {
            return TextFormat::RED."You have no invites from this person!";
        }
    }

    public function denyInvite(ArenaPlayer $player, string $playerName = ""){
	    if ($this->hasInvite($player, $playerName)) {
	    	$invite = $this->getInvite($player, $playerName);
		    $invite->getFrom()->sendMessage(TextFormat::RED.$player->getPlayerName()." has denied your party invite!");
		    $this->removeInvite($player, $playerName);
		    return TextFormat::GREEN . "You have successfully denied the invite!";
	    } else {
		    return TextFormat::RED . "You have no invites from this person!";
	    }
    }

    public function addInvite(ArenaPlayer $to, ArenaPlayer $from) {
	    if(!isset($this->invites[$from->getName()])) {
		    $invite = new Invite($from);
		    $this->invites[$from->getName()] = $invite;
	    } else {
	    	$invite = $this->invites[$from->getName()];
	    }
	    $invite->addInvite($to);

    }

    public function removeInvite(ArenaPlayer $player, string $pName = ""){
    	if(strlen($pName) > 0) {
		    if($this->hasInvite($player, $pName)) {
			    $this->getInvite($player, $pName)->removeInvite($player);
		    }
	    }
    }

    public function hasInviteObject(ArenaPlayer $player) {
    	return isset($this->invites[$player->getName()]);
    }

    public function removeHostObject(ArenaPlayer $player) {
    	if(isset($this->invites[$player->getName()])) {
    		unset($this->invites[$player->getName()]);
	    }
    }

    /**
     * @param string $name
     *
     * @return ArenaPlayer|null
     */
    public function getPlayer(string $name){
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        /** @var ArenaPlayer[] $players */
        $players = $this->getPlugin()->getServer()->getOnlinePlayers();
        foreach($players as $player) {
            if (stripos($player->getPlayerName(), $name) === 0) {
                $curDelta = strlen($player->getPlayerName()) - strlen($name);
                if ($curDelta < $delta) {
                    $found = $player;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }
        return $found;
    }

}