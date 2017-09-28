<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 2/10/2017
 * Time: 9:20 PM
 */

namespace sys\arenapvp\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sys\arenapvp\ArenaPlayer;
use sys\arenapvp\ArenaPvP;
use sys\arenapvp\basefiles\BaseArenaUserCommand;
use sys\arenapvp\party\Party;

class PartyCommand extends BaseArenaUserCommand {

    public function __construct(ArenaPvP $main) {
        parent::__construct($main, "party", "Create, invite, and join parties", "/party [chat|invite|kick|accept|deny] (player)");
    }

    /**
     * @param CommandSender|ArenaPlayer $sender
     * @param array $args
     * @return mixed|string
     */
    public function onExecute(CommandSender $sender, array $args) {
        if(count($args) > 0){
            switch(strtolower($args[0])) {
	            case "accept":
		            if(isset($args[1])) {
			            return $this->getPlugin()->getPartyManager()->acceptInvite($sender, $args[1]);
		            }
		            $this->sendPartyHelp($sender);
		            break;
	            case "chat":
	            	if(!$sender->inParty()) return TextFormat::RED."You are not in a party!";

	            	if(!isset($args[1])) return TextFormat::RED."You must provide a message! /party chat [message]";

	            	array_shift($args);
	            	$sender->getParty()->broadcastPartyMessage($sender, implode(" ", $args));
	            	break;
	            case "deny":
		            if(isset($args[1])) {
			            return $this->getPlugin()->getPartyManager()->denyInvite($sender, $args[1]);
		            }
		            $this->sendPartyHelp($sender);
		            break;
	            case "disband":
		            if(!$sender->inParty())
			            return TextFormat::RED."You are not in a party!";

		            if($sender->inParty() and !($sender === $sender->getParty()->getLeader()))
			            return TextFormat::RED."You are not the party leader!";

		            $sender->getParty()->disbandParty();
		            break;
	            case "help":
	            	$this->sendPartyHelp($sender);
	            	break;
                case "invite":
                    if(isset($args[1])){
                        if($sender->inParty() and !($sender === $sender->getParty()->getLeader()))
                            return TextFormat::RED."You are not the party leader!";

                        $player = $this->getPlayer($args[1]);

                        if($sender === $player)
                            return TextFormat::RED."You can't add yourself to a party!";

                        if(!$player)
                            return TextFormat::RED."That player is not online!";

                        if($sender->inParty() and $player->inParty() and $sender->getParty() === $player->getParty())
                            return TextFormat::RED."That player is already in your party!";

                        if(!$player->hasPartyInvitesEnabled())
                            return TextFormat::RED."That player is not accepting party invites at this time!";

                        if($player->inParty())
                            return TextFormat::RED."That player is already in a party!";

                        if($sender->inParty() and count($sender->getParty()->getMembers()) >= Party::$MAX_PLAYERS)
                            return TextFormat::RED."Your party already contains the maximum amount of players!";


                        $this->getPlugin()->getPartyManager()->addInvite($player, $sender);
                        $player->sendArgsMessage(TextFormat::GREEN."You have received a party invite from {0}!", $sender->getPlayerName());
                        $player->sendArgsMessage(TextFormat::GREEN."Use /party accept {0} or /party deny {0} to respond to the request!", $sender->getPlayerName());
                        return TextFormat::GREEN."Successfully sent a party invite to ".$player->getPlayerName()."!";
                    } else {
                        $this->sendPartyHelp($sender);
                        return true;
                    }
                    break;
	            case "kick":
		            if(isset($args[1])){
			            if(!$sender->inParty())
				            return TextFormat::RED."You are not in a party!";

			            if($sender->inParty() and !($sender === $sender->getParty()->getLeader()))
				            return TextFormat::RED."You are not the party leader!";

			            $player = $sender->getParty()->getMember($args[1]);

			            if($player === null)
				            return TextFormat::RED."That player is not in your party!";

			            if($sender === $player)
				            return TextFormat::RED."You can't kick yourself from the party!";

			            $sender->getParty()->removePlayer($player);
			            $sender->getParty()->broadcastMessage(TextFormat::GREEN.$player->getPlayerName()." has successfully been removed from the group!");
			            return TextFormat::GREEN."You have successfully removed ".$player->getPlayerName()." from the party!";

		            } else {
			            $this->sendPartyHelp($sender);
		            }
		            break;
                case "leave":
                	if(!$sender->inParty()) return TextFormat::RED . "You are not in a party!";

                	if($sender->getParty()->getLeader() === $sender) return TextFormat::RED. "You can't leave the party while you are the leader! Do /party disband to disband your party!";

                	$sender->getParty()->removePlayer($sender);
                	return TextFormat::GREEN. " You have successfully left your party!";
                    break;
	            case "list":
		            if(!$sender->inParty())
			            return TextFormat::RED."You are not in a party!";

		            $sender->sendMessage(TextFormat::AQUA."--- ".$sender->getParty()->getLeader()->getPlayerName()."'s party ---");
		            foreach($sender->getParty()->getMembers() as $member){
			            $sender->sendMessage(TextFormat::GOLD." - ".TextFormat::AQUA.$member->getPlayerName());
		            }
		            return TextFormat::AQUA."------------------";
		            break;
            }
        } else {
            $this->sendPartyHelp($sender);
        }
        return true;
    }

    private function sendPartyHelp(CommandSender $sender){
        $messages = [
            TextFormat::GRAY."----- Party Command Help -----",
            TextFormat::GOLD."/party accept [player]".TextFormat::GRAY." - Accept party invites",
	        TextFormat::GOLD."/party chat [msg]".TextFormat::GRAY." - Send a chat message to your party",
            TextFormat::GOLD."/party disband".TextFormat::GRAY." - Disband a party if in one(Leader only)",
            TextFormat::GOLD."/party deny".TextFormat::GRAY." - Deny party invites!",
            TextFormat::GOLD."/party invite [player]".TextFormat::GRAY." - Invite players to parties(Creates a party if not in one)",
            TextFormat::GOLD."/party kick [player]".TextFormat::GRAY." - Kick members from parties(Leader only)",
            TextFormat::GOLD."/party leave".TextFormat::GRAY." - Leave a party if in one",
            TextFormat::GOLD."/party list".TextFormat::GRAY." - Lists all members in a party"
        ];
        foreach($messages as $message){
            $sender->sendMessage($message);
        }
    }

}