<?php

namespace App\Controller\Board;

use App\Entity\Board\Comment;
use App\Entity\Board\Message;
use App\Repository\Board\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @var \App\Repository\Board\CommentRepository $commentRepository
     */
    protected $commentRepository;

    /**
     * @param \App\Repository\Board\CommentRepository $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * store comment
     *
     * @Route(
     *  "/board/message/{id}/comment",
     *  name="board.comment.store",
     *  methods={"POST"}
     * )
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Board\Message $message
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function store(
        Request $request,
        Message $message
    ): Response {
        $data = [
            'name'      => $request->get('name'),
            'comment'   => $request->get('comment'),
            'message'   => $message,
        ];

        $this->commentRepository->store($data);

        return $this->redirectToRoute('board.message.index');
    }

    /**
     * update comment
     *
     * @Route(
     *  "/board/message/{messageId}/comment/{id}",
     *  name="board.comment.update",
     *  methods={"PUT"}
     * )
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Board\Comment $comment
     * @param int $messageId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse json
     */
    public function update(
        Request $request,
        Comment $comment,
        int $messageId
    ): Response {
        if ($comment->getMessageId() == $messageId) {
            $this->commentRepository->update(
                $comment,
                $request->get('content')
            );

            return new JsonResponse(['status' => 200], 200);
        }

        return new JsonResponse(['status' => 400], 400);
    }

    /**
     * delete comment
     *
     * @Route(
     *  "/board/message/{messageId}/comment/{id}",
     *  name="board.comment.delete",
     *  methods={"DELETE"}
     * )
     * @param \App\Entity\Board\Comment $comment
     * @param int $messageId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse json
     */
    public function delete(
        Comment $comment,
        int $messageId
    ): Response {
        if ($comment->getMessageId() == $messageId) {
            $this->commentRepository->delete($comment);
            return new JsonResponse(['status' => 200], 200);
        }

        return new JsonResponse(['status' => 400], 400);
    }
}
