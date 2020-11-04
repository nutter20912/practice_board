<?php

namespace App\Controller\Board;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Board\Message;
use App\Repository\Board\MessageRepository;

class MessageController extends AbstractController
{
    public const PAGINATOR_PER_PAGE = 5;

    /**
     * @var \App\Repository\Board\MessageRepository $messageRepository
     */
    protected $messageRepository;

    /**
     * @param \App\Repository\Board\MessageRepository $messageRepository
     */
    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * message index
     *
     * @Route(
     *  "/board/message",
     *  name="board.message.index",
     *  methods={"GET"}
     * )
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function index(Request $request): Response
    {
        $page = $request->get('page', 1);

        $messages = $this->messageRepository->getMessagePaginator(
            $page,
            self::PAGINATOR_PER_PAGE
        );

        return $this->render('board/index.html.twig', [
            'messages'  => $messages,
            'current'   => $page,
            'pages'     => ceil($messages->count() / self::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * show message
     *
     * @Route(
     *  "/board/message/{id}",
     *  name="board.message.show",
     *  methods={"GET"}
     * )
     * @param \App\Entity\Board\Message $message
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function show(Message $message): Response
    {
        return $this->render('board/message.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * store message
     *
     * @Route(
     *  "/board/message",
     *  name="board.message.store",
     *  methods={"POST"}
     * )
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function store(Request $request): Response
    {
        $data = $request->get('message');

        $this->messageRepository->store($data);

        return $this->redirectToRoute('board.message.index');
    }

    /**
     * update message
     *
     * @Route(
     *  "/board/message/{id}",
     *  name="board.message.update",
     *  methods={"PUT"}
     * )
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Board\Message $message
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function update(
        Request $request,
        Message $message
    ): Response {
        $content = $request->get('content');
        $this->messageRepository->update($message, $content);

        return $this->redirectToRoute('board.message.index');
    }

    /**
     * delete message
     *
     * @Route(
     *  "/board/message/{id}",
     *  name="board.message.delete",
     *  methods={"DELETE"}
     * )
     * @param \App\Entity\Board\Message $message
     *
     * @return \Symfony\Component\HttpFoundation\Response page
     */
    public function delete(Message $message)
    {
        $this->messageRepository->delete($message);

        return $this->redirectToRoute('board.message.index');
    }
}
