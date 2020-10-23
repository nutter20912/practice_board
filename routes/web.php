<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;

$container = new Container();
$container->set('em', $entityManager);
$container->set('view', function () {
    return Twig::create(BASE_PATH . 'resources/views');
});
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->add(new MethodOverrideMiddleware());
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app));
$app->addErrorMiddleware(false, false, false);

$app->group('/board', function (RouteCollectorProxy $group) {
    $group->get('/message', Src\Controllers\Board\MessageController::class . ':index')
        ->setName('message.index');
    $group->post('/message', Src\Controllers\Board\MessageController::class . ':store')
        ->setName('message.store');
    $group->get('/message/{message:[0-9]+}', Src\Controllers\Board\MessageController::class . ':show')
        ->setName('message.show');
    $group->put('/message/{message}', Src\Controllers\Board\MessageController::class . ':update')
        ->setName('message.update');
    $group->delete('/message/{message:[0-9]+}', Src\Controllers\Board\MessageController::class . ':delete')
        ->setName('message.delete');
    $group->post('/message/{message:[0-9]+}/comment', Src\Controllers\Board\CommentController::class . ':store')
        ->setName('comment.store');
    $group->put('/message/{message:[0-9]+}/comment/{comment:[0-9]+}', Src\Controllers\Board\CommentController::class . ':update');
    $group->delete('/message/{message:[0-9]+}/comment/{comment:[0-9]+}', Src\Controllers\Board\CommentController::class . ':delete');
});

$app->run();
