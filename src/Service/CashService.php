<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CashRecords;
use App\Entity\User;
use App\Exceptions\ApiValidationException;

class CashService
{
    const REDIS_CASH_PREFIX = 'cash:';
    const REDIS_CASH_EXPIRE = 3600;

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @param \Redis
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, \Redis $redis)
    {
        $this->entityManager = $entityManager;
        $this->redis = $redis;
    }

    /**
     * @param \App\Entity\User $user
     *
     * @return int
     */
    public function getCash(User $user)
    {
        $expire = self::REDIS_CASH_EXPIRE;
        $script = <<<EOT
            local key = KEYS[1]
            local cash = ARGV[1]

            if redis.call('GET', key) == false then
                redis.call('SETNX', key, cash);
                redis.call('EXPIRE', key, $expire);
                return cash;
            end

            return redis.call('GET', key);
        EOT;

        return $this->redis->eval(
            $script,
            [$this->getRedisKey($user->getId()), $user->getCash()],
            1
        );
    }

    /**
     * @param \App\Entity\User $user
     * @param int $diff
     *
     * @return int
     *
     * @throws App\Exceptions\ApiValidationException
     */
    public function changeCash(User $user, $diff)
    {
        if ($diff == 0) {
            throw new ApiValidationException('error cash', 902);
        }

        if (!$cash = $this->setCash($user, $diff)) {
            throw new ApiValidationException('over cash', 903);
        }

        return $cash;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     * @param int $cash
     * @param int $diff
     *
     * @return \App\Entity\CashRecords
     */
    public function addCashRecord(
        Request $request,
        User $user,
        $cash,
        $diff
    ): CashRecords {
        $cashRecord = new CashRecords();
        $cashRecord->setOperator($user->getAccount());
        $cashRecord->setCurrent($cash);
        $cashRecord->setDiff($diff);
        $cashRecord->setIp($request->getClientIp());
        $cashRecord->setUser($user);
        $this->entityManager->persist($cashRecord);
        $this->entityManager->flush();

        return $cashRecord;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getRedisKey($key)
    {
        return self::REDIS_CASH_PREFIX . $key;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     *
     * @return int
     */
    private function setCash(User $user, $diff)
    {
        $jobListName = self::REDIS_CASH_PREFIX . 'list';
        $expire = self::REDIS_CASH_EXPIRE;
        $script = <<<EOT
            local key = KEYS[1]
            local cash = ARGV[1]
            local diff = ARGV[2]

            if redis.call('GET', key) == false then
                redis.call('SETNX', key, cash);
                redis.call('EXPIRE', key, $expire);
            end

            if redis.call('GET', key) + diff > 0 then
                redis.call('SADD', '{$jobListName}', key)
                redis.call('EXPIRE', key, $expire)

                return redis.call('INCRBY', key, diff);
            end

            return false;
        EOT;

        return $this->redis->eval($script, [
            $this->getRedisKey($user->getId()),
            $user->getCash(),
            $diff,
        ], 1);
    }

    /**
     * @return bool
     */
    public function updateCashList()
    {
        /** @var UserRepository */
        $repository = $this->entityManager->getRepository(User::class);
        $jobListName = self::REDIS_CASH_PREFIX . 'list';

        if ($this->redis->sCard($jobListName) == 0) {
            return false;
        }

        while ($key = $this->redis->sPop($jobListName)) {
            $cash = $this->redis->get($key);
            $userId = $this->getUserId($key);
            $repository->updateCash($userId, $cash);
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getUserId($redisKey)
    {
        return str_replace(self::REDIS_CASH_PREFIX, '', $redisKey);
    }
}
