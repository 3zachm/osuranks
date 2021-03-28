<?php

require_once(__DIR__ . '/../../../../secure/.env');

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($_REDISAUTH);

function getRedis() {
    global $redis;
    return $redis;
}