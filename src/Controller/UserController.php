<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Response\ApiResponse;
use App\Entity\User;
use App\Service\CashService;

/**
 * UserController
 * @Route("/api/user",name="api_user_")
 */
class UserController
{
    /**
     * show user
     *
     * @Route("/{account}", name="user_show")
     * @Method("GET")
     *
     * @param \App\Entity\User $user
     * @param \App\Service\CashService $cashService
     *
     * @return App\Response\ApiResponse api response
     */
    public function show(User $user, CashService $cashService): JsonResponse
    {
        return ApiResponse::success([
            'id' => $user->getId(),
            'account' => $user->getAccount(),
            'cash' => $cashService->getCash($user),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }
}
