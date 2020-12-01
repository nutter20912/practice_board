<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Response\ApiResponse;
use App\Entity\CashRecords;
use App\Entity\User;
use App\Service\CashService;
use Symfony\Component\HttpFoundation\Request;

/**
 * CashController
 * @Route("/api/cash",name="api_cash_")
 */
class CashController
{
    const PAGINATOR_PER_PAGE = 5;

    /**
     * show cash
     *
     * @Route("/{id}", name="cash_show", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param \App\Service\CashService $cashService
     * @param \App\Entity\User $user
     *
     * @return App\Response\ApiResponse api response
     */
    public function show(CashService $cashService, User $user): JsonResponse
    {
        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $cashService->getCash($user),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * add cash
     *
     * @Route("/{id}/add", requirements={"id"="\d+"})
     * @Method("PUT")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\CashService $cashService
     * @param \App\Entity\User $user
     *
     * @return App\Response\ApiResponse api response
     */
    public function add(
        Request $request,
        CashService $cashService,
        User $user
    ): JsonResponse {
        $diff = $cashService->cashFormat((float)$request->get('cash'));
        $cash = $cashService->changeCash($user, $diff);
        $cashRecord = $cashService->addCashRecord($request, $user, $cash, $diff);

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $cash,
            'updatedAt' => $cashRecord->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * sub cash
     *
     * @Route("/{id}/sub", requirements={"id"="\d+"})
     * @Method("PUT")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\CashService $cashService
     * @param \App\Entity\User $user
     *
     * @return App\Response\ApiResponse api response
     */
    public function sub(
        Request $request,
        CashService $cashService,
        User $user
    ): JsonResponse {
        $diff = $cashService->cashFormat((float)$request->get('cash') * -1);
        $cash = $cashService->changeCash($user, $diff);
        $cashRecord = $cashService->addCashRecord($request, $user, $cash, $diff);

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $cash,
            'updatedAt' => $cashRecord->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * cash records
     *
     * @Route("/{id}/records", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     *
     * @return App\Response\ApiResponse api response
     */
    public function records(
        EntityManagerInterface $entityManager,
        Request $request,
        User $user
    ): JsonResponse {
        /** @var CashRecordsRepository */
        $repository =  $entityManager->getRepository(CashRecords::class);
        $repository
            ->setPageLimit(self::PAGINATOR_PER_PAGE)
            ->setCurrentPage($request->get('page', 1));

        $condition = [
            'user_id = :id' => $user->getId(),
            'created_at > :start' => $request->get('start') . ' 00:00:00',
            'created_at < :end' => $request->get('end') . ' 23:59:59',
        ];

        $records = $repository->getRecordsByDate($condition);

        foreach ($records as &$record) {
            $record['created_at'] = $record['created_at']
                ->format('Y-m-d H:i:s');
        }

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'pages'   => $repository->getRecordsPages($condition),
            'records' => $records,
        ], 200);
    }
}
