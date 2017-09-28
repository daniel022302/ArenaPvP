<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 3/1/2017
 * Time: 4:42 PM
 */

namespace sys\arenapvp\party;


use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;

class Party {

    /** @var string */
    private $id;

    /** @var ArenaPlayer */
    private $leader;

    /** @var ArenaPlayer[] */
    private $players = [];

    public static $MAX_PLAYERS = 25;

    public function __construct(ArenaPlayer $leader, array $players = []) {
        $this->leader = $leader;
        $this->id = md5(base64_encode(mt_rand()));
        if(count($players) > 0) {
            foreach ($this->players as $player) {
                $this->addPlayer($player);
            }
        }
        $leader->setParty($this);
    }

    public function getId(){
        return $this->id;
    }

    public function getLeader(){
        return $this->leader;
    }

    /**
     * @return ArenaPlayer[]
     */
    public function getMembers(){
        return $this->players;
    }

    public function getMember($name){
        if($name instanceof ArenaPlayer) {
            $name = $name->getName();
        }
        if(isset($this->players[$name])){
            return $this->players[$name];
        } else {
            foreach($this->getMembers() as $pName=>$player){
                if(stripos($pName, $name) !== false or stripos($player->getPlayerName(), $name) !== false){
                    return $player;
                }
            }
        }
        return null;
    }

    /**
     * @return ArenaPlayer[]
     */
    public function getOnlineMembers(){
        $members = [];
        foreach($this->getMembers() as $member){
            if($member->isOnline()){
                $members[$member->getName()] = $member;
            }
        }
        if($this->getLeader()->isOnline()){
            $members[$this->getLeader()->getName()] = $this->getLeader();
        }
        return $members;
    }

    public function addPlayer(ArenaPlayer $player){
        if(!isset($this->players[$player->getName()]) and $player !== $this->getLeader()){
            $this->players[$player->getName()] = $player;
            $player->setParty($this);
        }
        $this->broadcastMessage(TextFormat::GREEN.$player->getPlayerName()." has joined the party!");
    }

    public function removePlayer(ArenaPlayer $player){
        if(isset($this->players[$player->getName()]) and $player !== $this->getLeader()){
            unset($this->players[$player->getName()]);
            $player->setParty(null);
        }
        $this->broadcastMessage(TextFormat::GREEN. $player->getPlayerName() . " has left the party!");
    }

    public function removeLeader(){
        $this->getLeader()->setParty(null);
        $this->leader = null;
    }

    public function broadcastMessage(string $message){
        foreach($this->getOnlineMembers() as $member){
            $member->sendMessage($message);
        }
    }

    public function broadcastPartyMessage(ArenaPlayer $player, string $message){
        $this->broadcastMessage(TextFormat::BLUE."(PARTY) ".TextFormat::GRAY.$player->getPlayerName(). " > " . $message);
    }

    public function disbandParty(){
	    $this->broadcastMessage(TextFormat::RED."The party has been disbanded!");
	    foreach($this->getOnlineMembers() as $member){
            $this->removePlayer($member);
        }
        $this->removeLeader();
    }

}