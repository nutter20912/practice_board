<?php

namespace Src\Controllers\Board;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteContext;
use Slim\Exception\HttpNotFoundException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Rakit\Validation\Validator;
use Src\Entities\Message;

class MessageController
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
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('em');
        $this->qb = $this->em->createQueryBuilder();
    }

    /**
     * index
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return resource page to redirect
     */
    public function index(Request $request, Response $response)
    {
        $data = $request->getQueryParams();

        $rules = [
            'page' => 'required|numeric',
        ];

        $validator = new Validator();
        $validation = $validator->make($data, $rules);
        $validation->validate();

        $page = ($validation->fails()) ? 1 : $data['page'];
        $limit = 5;

        $dql = $this->qb->select('m')
            ->from(Message::class, 'm')
            ->orderBy('m.id', 'DESC');
        $messages = $this->paginate($dql, $page, $limit);

        return $this->container->get('view')
            ->render($response, 'board/index.php', [
                'messages'  => $messages,
                'current'   => $page,
                'pages'     => ceil($messages->count() / $limit),
            ]);
    }

    /**
     * show message
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     *
     * @return resource page to redirect
     *
     * @throws HttpNotFoundException
     */
    public function show(Request $request, Response $response, $args)
    {
        $message = $this->em->getRepository(Message::class)
            ->find($args['message']);

        if (!$message) {
            throw new HttpNotFoundException($request);
        }

        return $this->container->get('view')
            ->render($response, 'board/message.php', [
                'message' => $message
            ]);
    }

    /**
     * store message
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return resource page to redirect
     */
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $rules = [
            'author' => 'required',
            'title' => 'required',
            'content' => 'required',
        ];

        $validator = new Validator();
        $validation = $validator->make($data, $rules);
        $validation->validate();

        if ($validation->fails()) {
            header("location:javascript://history.go()");
        }

        $comment = new Message();
        $comment->setAuthor($data['author']);
        $comment->setTitle($data['title']);
        $comment->setContent($data['content']);
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
     * update message
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     *
     * @return resource page to redirect
     *
     * @throws HttpNotFoundException
     */
    public function update(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $rules = [
            'content' => 'required',
        ];

        $validator = new Validator();
        $validation = $validator->make($data, $rules);
        $validation->validate();

        if ($validation->fails()) {
            header("location:javascript://history.go()");
        }

        $message = $this->em->getRepository(Message::class)
            ->find($args['message']);

        if (!$message) {
            throw new HttpNotFoundException($request);
        }

        $message->setContent($data['content']);
        $this->em->flush();

        $url = RouteContext::fromRequest($request)
            ->getRouteParser()
            ->urlFor('message.index');

        return $response->withHeader('Location', $url)
            ->withStatus(302);
    }

    /**
     * delete message
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     *
     * @return resource page to redirect
     *
     * @throws HttpNotFoundException
     */
    public function delete(Request $request, Response $response, $args)
    {
        $message = $this->em->getRepository(Message::class)
            ->find($args['message']);

        if (!$message) {
            throw new HttpNotFoundException($request);
        }

        $message->getComments()->removeElement($message);
        $this->em->remove($message);
        $this->em->flush();

        $url = RouteContext::fromRequest($request)
            ->getRouteParser()
            ->urlFor('message.index');

        return $response->withHeader('Location', $url)
            ->withStatus(302);
    }

    /**
     * paginate
     *
     * @param \Doctrine\ORM\QueryBuilder $dql
     * @param int $page
     * @param int $liimit
     *
     * @return Doctrine\ORM\Tools\Pagination\Paginator $paginator
     */
    private function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
