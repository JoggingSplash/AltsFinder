<?php

namespace splash\altsfinder\session;

use splash\altsfinder\AltsFinder;
use splash\altsfinder\utils\DeviceTranslator;
use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;

class SessionManager {
    private static SessionManager $instance;
    /** @var Session[] $sessions */
    private static array $sessions = [];

    public function __construct() {
        self::$instance = $this;
        $this->init();
    }

    public function init(): void
    {
        $sqlData = AltsFinder::getProvider()->getUsers();
        foreach ($sqlData as $i => $sqlDatum) {
            $addresses = explode(',', $sqlDatum['addresses']);
            self::$sessions[$sqlDatum['username']] = new Session($sqlDatum['username'], $addresses, $sqlDatum['lastaddress'], $sqlDatum['deviceos']);
            unset($addresses);
        }
    }

    /**
     * @return SessionManager
     */
    public static function getInstance(): SessionManager {
        return self::$instance;
    }

    /**
     * @return Session[]
     */
    public static function getSessions(): array {
        return self::$sessions;
    }

    public function getSession(string $username): ?Session  {
        return self::$sessions[$username] ?? null;
    }

    #[Pure(true)] public function getSessionByPrefix(string $name): ?Session {
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach (array_keys(self::$sessions) as $session) {
            if (stripos($session, $name) === 0) {
                $curDelta = strlen($session) - strlen($name);
                if ($curDelta < $delta) {
                    $found = $session;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }
        return self::$sessions[$found] ?? null;
    }

    public function registerPlayer(string $username, string $address, int $deviceOS): void {
        if (!isset(self::$sessions[$username])){
            self::$sessions[$username] = $session = new Session($username, [$address], $address, $deviceOS);
            AltsFinder::getProvider()->savePlayer($session->getUsername(), $session->getAddresses(), $session->getLastAddress(), $session->getDeviceOS());
        } else {
            $session = $this->getSession($username);
            if ($session instanceof Session) {
                if ($address !== "172.18.0.1") {
                    $session->addAddress($address);
                    $session->setLastAddress($address);
                    AltsFinder::getProvider()->savePlayer($session->getUsername(), $session->getAddresses(), $session->getLastAddress(), $session->getDeviceOS());
                }
            }
        }
    }

    public function getAlts(string $ip, ?string $username = null): array{
        $alts = [];
        foreach (self::$sessions as $session) {
            if ($username !== null && $session->getUsername() == $username) continue;
            if (in_array($ip, $session->getAddresses())){
                $color = $session->isOnline() ? TextFormat::GREEN : TextFormat::RED;
                $alts[$session->getUsername()] = $color . $session->getUsername() . "[" . DeviceTranslator::toString($session->getDeviceOS()) . "]";
            }
        }
        return array_values($alts);
    }

}