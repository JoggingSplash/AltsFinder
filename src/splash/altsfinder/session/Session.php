<?php

namespace splash\altsfinder\session;

use pocketmine\player\Player;
use pocketmine\Server;

class Session
{

    private string $username;
    private array $addresses;
    private string $lastAddress;
    private int $deviceOS;

    /**
     * @param string $username
     * @param array $addresses
     * @param string $lastAddress
     * @param int $deviceOS
     */
    public function __construct(string $username, array $addresses, string $lastAddress, int $deviceOS)
    {
        $this->username = $username;
        $this->addresses = $addresses;
        $this->lastAddress = $lastAddress;
        $this->deviceOS = $deviceOS;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    public function addAddress(string $address): void
    {
        if ($address === "172.18.0.1"){
            return;
        }
        if (!in_array($address, $this->addresses)) {
            $this->addresses[] = $address;
        }
    }

    /**
     * @param string $lastAddress
     */
    public function setLastAddress(string $lastAddress): void
    {
        if ($lastAddress === "172.18.0.1"){
            return;
        }
        $this->lastAddress = $lastAddress;
    }
    /**
     * @return string
     */
    public function getLastAddress(): string
    {
        return $this->lastAddress;
    }

    /**
     * @return int
     */
    public function getDeviceOS(): int
    {
        return $this->deviceOS;
    }

    public function isOnline(): bool
    {
        return Server::getInstance()->getPlayerExact($this->getUsername()) instanceof Player;
    }
}