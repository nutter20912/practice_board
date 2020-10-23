<?php

namespace Src\Controllers\Board;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Src\Entities\Comment;
use Src\Entities\Message;

class CommentController
{
    /**
     * container
     * 
     * @var \Psr\Container\ContainerInterface $container
     */
    protected $container;

    /**
     * EntityManager
     * 
     * @var \Doctrine\ORM\EntityManager $em
     */
    protected $em;

    /**
     * QueryBuilder
     * 
     * @var \Doctrine\ORM\QueryBuilder $qb;
     */
    protected $qb;

    /**
     * store comment
     *
     * @param \Psr\Http\Message\ServerRequestInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('em');
        $this->qb = $this->em->createQueryBuilder();
    }

    /**
     * store comment
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * 
     * @return resource redirect to message.index
     * 
     * @throws HttpNotFoundException
     */
    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $message = $this->em->getRepository(Message::class)
            ->find($args['message']);

        if (!$message) {
            throw new HttpNotFoundException($request);
        }

        $comment = new Comment();
        $comment->setName($data['name']);
        $comment->setComment($data['comment']);
        $comment->setMessage($message);
        $comment->setCreatedAt();
        $this->em->persist($comment);
        $this->em->flush();

        $url = RouteContext::fromRequest($request)
            ->getRouteParser()
            ->urlFor('message.index');

        return $response->withHeader('Location', $url)
            ->withStatus(302);
    }

    /**
     * update comment
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * 
     * @return string update result
     */
    public function update(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $comment = $this->em->getRepository(Comment::class)
            ->find($args['comment']);

        if (!$comment) {
            $response->getBody()->write(json_encode(['status' => 400]));

            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($comment->getMessageId() == $args['message']) {
            $comment->setComment($data['comment']);
            $this->em->flush();
            $response->getBody()->write(json_encode(['status' => 200]));

            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['status' => 400]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * delete comment
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * 
     * @return string delete result
     */
    public function delete(Request $request, Response $response, $args)
    {
        $comment = $this->em->getRepository(Comment::class)
            ->find($args['comment']);

        if (!$comment) {
            $response->getBody()
                ->write(json_encode(['status' => 400]));

            return $response->withHeader('Content-Type', 'application/json');
        }

        $this->em->remove($comment);
        $this->em->flush();
        $response->getBody()
            ->write(json_encode(['status' => 200]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
