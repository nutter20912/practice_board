<?php

namespace App\Support;

class RedisLock
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @param \Redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     * @param int $ttl
     *
     * @return array
     */
    public function lock($key, $ttl = 5000)
    {
        $token = uniqid();
        $lock = false;

        while (true) {
            if ($this->setLock($key, $token, $ttl)) {
                $lock = [
                    'key' => $key,
                    'token' => $token,
                ];
                break;
            }

            usleep(10);
        }

        return $lock;
    }

    /**
     * @param array $lock
     *
     * @return bool
     */
    public function unlock($lock)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return false
            end
        ';

        return $this->redis->eval($script, [$lock['key'], $lock['token']], 1);
    }

    /**
     * @param string $key
     * @param string $token
     * @param int $ttl
     *
     * @return bool
     */
    private function setLock($key, $token, $ttl)
    {
        return $this->redis->set($key, $token, ['nx', 'px' => $ttl]);
    }
}
