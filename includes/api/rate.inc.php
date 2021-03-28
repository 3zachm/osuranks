<?php

class RateLimiter {
    private $redis;
    private $max_requests;
    private $period;
    private $total_requests;

    function __construct($redis, $max_requests, $period) {
        $this->redis = $redis;
        $this->max_requests = $max_requests;
        $this->period = $period;
        $this->total_requests = 0;
    }

    function getMaxRequests() {
        return $this->max_requests;
    }

    function getPeriod() {
        return $this->period;
    }

    function getTotalRequests() {
        return $this->total_requests;
    }

    function hit() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $user_ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $user_ip_address = $_SERVER['REMOTE_ADDR'];
        }
        if (!$this->redis->exists($user_ip_address)) {
            $this->redis->set($user_ip_address, 1);
            $this->redis->expire($user_ip_address, $this->period);
            $this->total_requests = 1;
        }
        else {
            $this->redis->INCR($user_ip_address);
            $this->total_requests = $this->redis->get($user_ip_address);
        }
        if ($this->total_requests > $this->max_requests) {
            return false;
        }
        else {
            return true;
        }
    }
}

class GlobalRateLimiter {
    private $redis;
    private $redisEntry;
    private $max_requests;
    private $period;
    private $total_requests;

    function __construct($redis, $redisEntry, $max_requests, $period) {
        $this->redis = $redis;
        $this->redisEntry = $redisEntry;
        $this->max_requests = $max_requests;
        $this->period = $period;
        $this->total_requests = 0;
    }
    function getMaxRequests() {
        return $this->max_requests;
    }

    function getPeriod() {
        return $this->period;
    }

    function getTotalRequests() {
        return $this->total_requests;
    }

    function hit() {
        if (!$this->redis->exists($this->redisEntry)) {
            $this->redis->set($this->redisEntry, 1);
            $this->redis->expire($this->redisEntry, $this->period);
            $this->total_requests = 1;
        }
        else {
            $this->redis->INCR($this->redisEntry);
            $this->total_requests = $this->redis->get($this->redisEntry);
        }
        if ($this->total_requests > $this->max_requests) {
            return false;
        }
        else {
            return true;
        }
    }
}