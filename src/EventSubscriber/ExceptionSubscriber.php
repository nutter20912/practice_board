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
            return $event->setResponse(
                ApiResponse::fail(
                    $exception->getStatusCode(),
                    $exception->getMessage(),
                    $exception->getStatusCode()
                )
            );
        }

        if ($exception instanceof ApiValidationException) {
            return $event->setResponse(
                ApiResponse::fail(
                    $exception->getCode(),
                    $exception->getMessage(),
                    400
                )
            );
        }

        return;
    }
}
