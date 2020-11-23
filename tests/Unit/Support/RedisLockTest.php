<?php

namespace App\Tests\Unit\Support;

use App\Support\RedisLock;
use PHPUnit\Framework\TestCase;

class RedisLockTest extends TestCase
{
    public function testLock()
    {
        //arrange
        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('set')
            ->will($this->onConsecutiveCalls(false, true));
        $key = 'test';

        //act
        $redisLock = new RedisLock($redisMcok);
        $response = $redisLock->lock($key);

        //assert
        $this->assertIsArray($response);
    }

    public function testUnlockSuccess()
    {
        //arrange
        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('eval')
            ->willReturn(true);
        $lock = [
            'key' => 'test',
            'token' => 'token',
        ];

        //act
        $redisLock = new RedisLock($redisMcok);
        $response = $redisLock->unlock($lock);

        //assert
        $this->assertTrue($response);
    }

    public function testUnlockFailed()
    {
        //arrange
        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('eval')
            ->willReturn(false);
        $lock = [
            'key' => 'test',
            'token' => 'token',
        ];

        //act
        $redisLock = new RedisLock($redisMcok);
        $response = $redisLock->unlock($lock);

        //assert
        $this->assertFalse($response);
    }
}
