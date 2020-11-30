<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CashRecords;
use App\Entity\User;
use App\Exceptions\ApiValidationException;

class CashService
{
    const REDIS_USER_DATABASE = 0;
    const REDIS_CASH_RECORD_DATABASE = 1;
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
        $key = $this->getRedisKey($user->getId());

        if ($this->redis->setNx($key, $user->getCash())) {
            $this->redis->expire($key, self::REDIS_CASH_EXPIRE);
            return $user->getCash();
        }

        return $this->redis->get($key);
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

        if (!$cash = $this->setRedisCash($user, $diff)) {
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
        $this->setRedisRecord($cashRecord);

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
     * @param \App\Entity\User $user
     *
     * @return int|bool
     */
    private function setRedisCash(User $user, $diff)
    {
        $cash = $this->getCash($user);

        if ($cash + $diff < 0) {
            return false;
        }

        return $this->redis->incrBy($this->getRedisKey($user->getId()), $diff);
    }

    /**
     * @param \App\Entity\CashRecords
     *
     * @return bool
     */
    private function setRedisRecord(CashRecords $cashRecord)
    {
        $this->redis->select(self::REDIS_CASH_RECORD_DATABASE);
        $res = $this->redis->rPush(
            self::REDIS_CASH_PREFIX . 'record',
            json_encode([
                'userId' => $cashRecord->getUser()->getId(),
                'recordId' => $cashRecord->getId(),
                'diff' => $cashRecord->getDiff()
            ])
        );
        $this->redis->select(self::REDIS_USER_DATABASE);

        return $res;
    }

    /**
     * @return bool
     */
    public function updateCashList()
    {
        /** @var UserRepository */
        $repository = $this->entityManager->getRepository(User::class);

        $this->redis->select(self::REDIS_CASH_RECORD_DATABASE);
        $jobListName = self::REDIS_CASH_PREFIX . 'record';

        if ($this->redis->lLen($jobListName) == 0) {
            return false;
        }

        $errorMsg = '';

        while ($res = $this->redis->lPop($jobListName)) {
            $record = json_decode($res, true);

            if (!$user = $repository->find($record['userId'])) {
                $errorMsg .= $res . PHP_EOL;
                continue;
            }

            $user->setCash($user->getCash() + $record['diff']);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        if ($errorMsg) {
            throw new \Exception($errorMsg);
        }

        return true;
    }
}
