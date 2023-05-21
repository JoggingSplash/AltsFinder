<?php

namespace splash\altsfinder;

use splash\altsfinder\command\CheckAltsCommand;
use splash\altsfinder\provider\SQLiteProvider;
use splash\altsfinder\session\SessionManager;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\plugin\PluginBase;

class AltsFinder extends PluginBase {

    private static AltsFinder $instance;
    private static SQLiteProvider $provider;
    private static SessionManager $sessionManager;

    protected function onLoad(): void{
        self::$instance = $this;
    }

    protected function onEnable(): void {
        self::$provider = new SQLiteProvider();
        self::$sessionManager = new SessionManager();
        $this->getServer()->getCommandMap()->register("alts", new CheckAltsCommand());
        $this->getServer()->getPluginManager()->registerEvent(PlayerPreLoginEvent::class, function (PlayerPreLoginEvent $event){
            if (!$event->isCancelled()){
                self::getSessionManager()->registerPlayer($event->getPlayerInfo()->getUsername(), $event->getIp(), $event->getPlayerInfo()->getExtraData()['DeviceOS']);
            }
        }, EventPriority::HIGHEST, $this);
    }

    /**
     * @return AltsFinder
     */
    public static function getInstance(): AltsFinder
    {
        return self::$instance;
    }

    /**
     * @return SessionManager
     */
    public static function getSessionManager(): SessionManager
    {
        return self::$sessionManager;
    }

    /**
     * @return SQLiteProvider
     */
    public static function getProvider(): SQLiteProvider
    {
        return self::$provider;
    }
}