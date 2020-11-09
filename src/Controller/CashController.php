<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Response\ApiResponse;
use App\Exceptions\ApiValidationException;
use App\Repository\CashRecordsRepository;
use App\Entity\CashRecords;
use App\Entity\User;

/**
 * CashController
 * @Route("/api/cash",name="api_cash_")
 */
class CashController
{
    const PAGINATOR_PER_PAGE = 5;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\HttpFoundation\Request $request
     */
    private $request;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * show cash
     *
     * @Route("/{id}", name="cash_show", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param \App\Entity\User $user
     *
     * @return App\Response\ApiResponse api response
     */
    public function show(User $user): JsonResponse
    {
        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $user->getCash(),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * add cash
     *
     * @Route("/{id}/add", requirements={"id"="\d+"})
     * @Method("PUT")
     *
     * @param int $id
     *
     * @return App\Response\ApiResponse api response
     */
    public function add($id): JsonResponse
    {
        $diff = (int)$this->request->get('cash');

        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try {
            $user = $this->changeCash($id, $diff);
            $this->addCashRecord($user, $diff);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $user->getCash(),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * sub cash
     *
     * @Route("/{id}/sub", requirements={"id"="\d+"})
     * @Method("PUT")
     *
     * @param int $id
     *
     * @return App\Response\ApiResponse api response
     */
    public function sub($id): JsonResponse
    {
        $diff = (int)$this->request->get('cash') * -1;

        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try {
            $user = $this->changeCash($id, $diff);
            $this->addCashRecord($user, $diff);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'cash' => $user->getCash(),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * cash records
     *
     * @Route("/{id}/records", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param \App\Entity\User $user
     * @param \App\Repository\CashRecordsRepository $cashRecordsRepository
     *
     * @return App\Response\ApiResponse api response
     */
    public function records(
        User $user,
        CashRecordsRepository $cashRecordsRepository
    ): JsonResponse {
        $currentPage = $this->request->get('page', 1);
        $condition = [
            'id' => $user->getId(),
            'start' => $this->request->get('start') . ' 00:00:00',
            'end' => $this->request->get('end') . ' 23:59:59',
        ];

        $query = $cashRecordsRepository
            ->getRecordsByDate(
                $condition,
                self::PAGINATOR_PER_PAGE,
                $currentPage
            );

        $totall = (new Paginator($query))
            ->setUseOutputWalkers(false)
            ->count();

        $records = $query
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        foreach ($records as &$record) {
            $record['created_at'] = $record['created_at']
                ->format('Y-m-d H:i:s');
        }

        return ApiResponse::success([
            'account' => $user->getAccount(),
            'pages'   => ceil($totall / self::PAGINATOR_PER_PAGE),
            'records' => $records,
        ], 200);
    }

    /**
     * @param int $id
     * @param int $diff
     *
     * @return \App\Entity\User $user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws App\Exceptions\ApiValidationException
     */
    private function changeCash($id, $diff): User
    {
        $user = $this->entityManager->find(
            User::class,
            $id,
            \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE
        );

        if (!$user) {
            throw new NotFoundHttpException('resource not found');
        } elseif ($diff < 0 && abs($diff) > $user->getCash()) {
            throw new ApiValidationException('over cash', 903);
        }

        $user->setCash($user->getCash() + $diff);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param \App\Entity\User $user
     * @param int $diff
     */
    private function addCashRecord(User $user, $diff): void
    {
        $cashRecords = new CashRecords();
        $cashRecords->setOperator($user->getAccount());
        $cashRecords->setCurrent($user->getCash() - $diff);
        $cashRecords->setDiff($diff);
        $cashRecords->setIp($this->request->getClientIp());
        $cashRecords->setUser($user);
        $this->entityManager->persist($cashRecords);
        $this->entityManager->flush();
    }
}
