<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Exceptions\ApiValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;
use App\Response\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CommentController
 * @Route("/api/board/message",name="api_board_")
 */
class CommentController
{
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
     * store comment
     *
     * @Route("/{id}/comment", name="comment_store")
     * @Method("POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Message $message
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return App\Response\ApiResponse api response
     *
     * @throws App\Exceptions\ApiValidationException
     */
    public function store(
        Request $request,
        Message $message,
        ValidatorInterface $validator
    ): JsonResponse {
        $comment = new Comment();
        $comment->setName($request->get('name', ''));
        $comment->setContent($request->get('content', ''));
        $comment->setMessage($message);
        $comment->setCreatedAt(new \DateTime("now"));
        $comment->setUpdatedAt(new \DateTime("now"));

        $errors = $validator->validate($comment);

        if (count($errors) > 0) {
            throw new ApiValidationException('params validate fail', 901);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }

    /**
     * update comment
     *
     * @Route("/{messageId}/comment/{id}", name="comment_update")
     * @Method("PUT")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Comment $comment
     * @param int $messageId
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return App\Response\ApiResponse api response
     *
     * @throws App\Exceptions\ApiValidationException
     */
    public function update(
        Request $request,
        Comment $comment,
        $messageId,
        ValidatorInterface $validator
    ): JsonResponse {
        if ($comment->getMessageId() != $messageId) {
            throw new NotFoundHttpException('resource not found');
        }

        $comment->setContent($request->get('content'));
        $errors = $validator->validate($comment);

        if (count($errors) > 0) {
            throw new ApiValidationException('params validate fail', 901);
        }

        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }

    /**
     * delete comment
     *
     * @Route("/{messageId}/comment/{id}", name="comment_delete")
     * @Method("DELETE")
     *
     * @param \App\Entity\Comment $comment
     * @param int $messageId
     *
     * @return App\Response\ApiResponse api response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
     */
    public function delete(Comment $comment, $messageId): JsonResponse
    {
        if ($comment->getMessageId() != $messageId) {
            throw new NotFoundHttpException('resource not found');
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return ApiResponse::success(null, 200);
    }
}
