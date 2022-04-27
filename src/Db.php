<?php declare(strict_types = 1);

namespace App;

/**
 * Class Db
 * Lightweight DB wrapper
 * @package App
 */
class Db
{
    private $host;
    private $user;
    private $pass;
    private $name;
    private $port;
    private ?\mysqli $connection;

    /**
     * Db constructor.
     * @param $host
     * @param $user
     * @param $pass
     * @param $name
     * @param $port
     */
    public function __construct($host, $user, $pass, $name, $port)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        $this->port = $port;
    }

    /**
     * @return \mysqli
     */
    public function getConnection(): \mysqli
    {
        return $this->connection ?? $this->connection = new \mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->name,
            $this->port
        );
    }
}
