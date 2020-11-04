<?php

namespace App\EventSubscriber;

use App\Exceptions\ApiValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Response\ApiResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 0],
            ],
        ];
    }

    public function processException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(
                ApiResponse::fail([
                    'Code' => $exception->getStatusCode(),
                    'Message' => $exception->getMessage(),
                ], $exception->getStatusCode())
            );
        } elseif ($exception instanceof ApiValidationException) {
            $event->setResponse(
                ApiResponse::fail([
                    'Code' => $exception->getCode(),
                    'Message' => $exception->getMessage(),
                ], 400)
            );
        }
    }
}
