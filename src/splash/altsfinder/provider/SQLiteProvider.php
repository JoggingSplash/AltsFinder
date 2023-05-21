<?php

namespace splash\altsfinder\provider;

use splash\altsfinder\AltsFinder;

class SQLiteProvider {

    private \SQLite3 $db;

    public function __construct() {
        $this->db = new \SQLite3(AltsFinder::getInstance()->getDataFolder() . 'logs.db');
        $this->db->exec("CREATE TABLE IF NOT EXISTS users(username TEXT PRIMARY KEY, addresses INT, lastaddress TEXT, deviceos INT);");
    }

    /**
     * @return \SQLite3
     */
    public function getDb(): \SQLite3
    {
        return $this->db;
    }

    public function getUsers(): array {
        $users = [];
        $data = $this->db->query("SELECT * FROM users;");
        while($result = $data->fetchArray(SQLITE3_ASSOC)){
            $users[] = $result;
        }
        return $users;
    }

    public function savePlayer(string $username, array $addresses, string $lastAddress, int $deviceOS): void {
        $addresses_ = implode(",", $addresses);
        $this->db->exec("INSERT OR REPLACE INTO users(username, addresses, lastaddress, deviceos) VALUES ('$username', '$addresses_', '$lastAddress', $deviceOS);");
    }

}