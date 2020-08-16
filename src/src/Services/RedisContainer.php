<?php
namespace App\Services;


use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisContainer
{

    private $redis;

    public function __construct($dsn) {
        $this->redis = RedisAdapter::createConnection($dsn);
    }

    public function getRedis() {
        return $this->redis;
    }
}