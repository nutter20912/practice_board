<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ExceptionSubscriber;
use App\Exceptions\ApiValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ExceptionSubscriberTest extends TestCase
{
    /**
     * 測試驗證 events 格式
     */
    public function testGetEventsFormat(): void
    {
        // act
        $subscriber = new ExceptionSubscriber();
        $events = $subscriber->getSubscribedEvents();

        // assert
        $this->assertIsArray($events);

        foreach ($events as $key => $event) {
            $this->assertEquals(KernelEvents::EXCEPTION, $key);

            array_walk($event, function ($event, $subscriber) {
                $this->IsTrue(method_exists($subscriber, $event[0]));
            }, $subscriber);
        }
    }

    /**
     * 測試無相符 exception
     */
    public function testNothingException(): void
    {
        // arrange
        /** @var MockObject&ExceptionEvent */
        $eventMock = $this
            ->getMockBuilder(ExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        // act
        $subscriber = new ExceptionSubscriber();
        $response = $subscriber->processException($eventMock);

        // assert
        $this->assertNull($response);
    }

    /**
     * 測試處理 http exception response
     */
    public function testHandleHttpException(): void
    {
        // arrange
        /** @var MockObject&HttpExceptionInterface */
        $httpException = $this
            ->getMockBuilder(HttpExceptionInterface::class)
            ->getMock();
        $httpException
            ->method('getStatusCode')
            ->willReturn(404);

        /** @var MockObject&ExceptionEvent */
        $eventMock = $this
            ->getMockBuilder(ExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock
            ->method('getThrowable')
            ->willReturn($httpException);
        $eventMock->method('setResponse');

        // act
        $subscriber = new ExceptionSubscriber();
        $response = $subscriber->processException($eventMock);

        // assert
        $this->assertNull($response);
    }

    /**
     * 測試處理 custom ApiValidation response
     */
    public function testApiValidationException(): void
    {
        // arrange
        /** @var MockObject&ApiValidationException */
        $httpException = $this
            ->getMockBuilder(ApiValidationException::class)
            ->getMock();

        /** @var MockObject&ExceptionEvent */
        $eventMock = $this
            ->getMockBuilder(ExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock
            ->method('getThrowable')
            ->willReturn($httpException);
        $eventMock->method('setResponse');

        // act
        $subscriber = new ExceptionSubscriber();
        $response = $subscriber->processException($eventMock);

        // assert
        $this->assertNull($response);
    }
}
