<?php

namespace splash\altsfinder\command;

use splash\altsfinder\AltsFinder;
use splash\altsfinder\session\Session;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class CheckAltsCommand extends Command {

    public function __construct() {
        parent::__construct('alts', "Check Player alts", "/alts [playerName]", []);
        $this->setPermission("alts.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($this->testPermission($sender)) {
            if (count($args) < 1) {
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return;
            }
            $ip = filter_var($args[0], FILTER_VALIDATE_IP);
            $session = AltsFinder::getSessionManager()->getSessionByPrefix($args[0]);
            if ($ip !== false) {
                $alts = AltsFinder::getSessionManager()->getAlts($ip);
                if (count($alts) > 1) {
                    $sender->sendMessage(TextFormat::RED . $ip . TextFormat::WHITE . " tiene " . TextFormat::RED . count($alts) . TextFormat::WHITE . " alts: " . TextFormat::RED . join(', ', $alts));
                } else {
                    $sender->sendMessage(TextFormat::GREEN . $ip . " dont have alts!");
                }
            } elseif ($session instanceof Session) {
                if (Server::getInstance()->isOp($session->getUsername())){
                    $sender->sendMessage(TextFormat::RED . "Dont try to see it!");
                    return;
                }
                $alts = [];
                foreach ($session->getAddresses() as $address) {
                    if ($address == "172.18.0.1") continue;
                    $alts = array_merge($alts, AltsFinder::getSessionManager()->getAlts($address));
                }
                $alts = array_unique($alts);
                if (count($alts) > 1) {
                    $sender->sendMessage(TextFormat::RED . $session->getUsername() . TextFormat::WHITE . " has " . TextFormat::RED . count($alts) . TextFormat::WHITE . " alts: " . TextFormat::RED . join(', ', $alts));
                } else {
                    $sender->sendMessage(TextFormat::GREEN . $session->getUsername() . " dont have alts!");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Invalid args $args[0]");
            }
        }
    }
}