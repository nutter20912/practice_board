<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Message;
use App\Exceptions\ApiValidationException;
use App\Response\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * MessageController
 * @Route("/api/board",name="api_board_")
 */
class MessageController
{
    const PAGINATOR_PER_PAGE = 5;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * message index
     *
     * @Route("/message", name="message_index")
     * @Method("GET")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return App\Response\ApiResponse api response
     */
    public function index(Request $request): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Message::class);
        $repository
            ->setPageLimit(self::PAGINATOR_PER_PAGE)
            ->setCurrentPage($request->get('page', 1));

        $messages = $repository->getMessagePaginator();

        foreach ($messages as &$message) {
            $message['comments'] = $this->entityManager
                ->createQueryBuilder()
                ->select('c')
                ->from(Comment::class, 'c')
                ->where('c.message_id = :id')
                ->setParameter('id', $message['id'])
                ->getQuery()
                ->getArrayResult();
        }

        return ApiResponse::success([
            'pages'   => $repository->getMessagePages(),
            'messages' => $messages,
        ], 200);
    }

    /**
     * store message
     *
     * @Route("/message", name="message_store")
     * @Method("POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return App\Response\ApiResponse api response
     *
     * @throws App\Exceptions\ApiValidationException
     */
    public function store(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $message = new Message();
        $message->setAuthor($request->get('author', ''));
        $message->setTitle($request->get('title', ''));
        $message->setContent($request->get('content', ''));
        $message->setCreatedAt(new \DateTime("now"));
        $message->setUpdatedAt(new \DateTime("now"));
        $errors = $validator->validate($message);

        if (count($errors) > 0) {
            throw new ApiValidationException('params validate fail', 901);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }

    /**
     * show message
     *
     * @Route("/message/{id}", name="message_show", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param \App\Entity\Message $message
     *
     * @return App\Response\ApiResponse api response
     */
    public function show(Message $message): JsonResponse
    {
        return ApiResponse::success([
            'author' => $message->getAuthor(),
            'title' => $message->getTitle(),
            'content' => $message->getContent(),
        ], 200);
    }

    /**
     * update message
     *
     * @Route("/message/{id}", name="message_update", requirements={"id"="\d+"})
     * @Method("PUT")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Message $message
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return App\Response\ApiResponse api response
     *
     * @throws App\Exceptions\ApiValidationException
     */
    public function update(
        Request $request,
        Message $message,
        ValidatorInterface $validator
    ): JsonResponse {
        $message->setContent($request->get('content', ''));
        $errors = $validator->validate($message);

        if (count($errors) > 0) {
            throw new ApiValidationException('params validate fail', 901);
        }

        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }

    /**
     * delete message
     *
     * @Route("/message/{id}", name="message_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     *
     * @param \App\Entity\Message $message
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function delete(Message $message): JsonResponse
    {
        $message->getComment()->removeElement($message);
        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }
}
