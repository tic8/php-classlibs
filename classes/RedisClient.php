<?php

namespace app\common;

class RedisClient
{

    public $redis = null;

    public function __construct(){
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1',6379);
    }
    
    public function __call($name, $params)
    {
        return call_user_func_array([$this->redis, $name], $params);
    }
    
    
}