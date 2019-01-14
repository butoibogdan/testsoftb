<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TransactionEntity;
use AppBundle\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route(service="custom_api_controller")
 */
class ApiControllerController extends Controller
{
    /** @var ApiService */
    protected $apiService;

    /**
     * ApiControllerController constructor.
     * @param ApiService $apiService
     */
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/api", name="api")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $request->request->all();

        return $this->json($data);
    }

    /**
     * @Route("api/get-one-transaction", name="api_get_one_transaction")
     * @return JsonResponse
     */
    public function getOneTransaction()
    {
        $transaction = $this->apiService->getOneTransaction();
        $output = [];
        if ($transaction !== null) {
            $output = [
                'transaction_id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getDate()->format('d-m-Y'),
            ];
        }
        return $this->json($output);
    }

    /**
     * @Route("api/get-transactions", name="api_get_transactions")
     * @return JsonResponse
     */
    public function getTransactions(): JsonResponse
    {
        $result = $this->apiService->getAllUserTransaction();

        return $this->json($result);

    }

    /**
     * @Route("api/add-transaction", name="api_add_transaction")
     * @return JsonResponse|null
     * @throws \InvalidArgumentException
     */
    public function addTransaction(): ?JsonResponse
    {
        $user = $this->apiService->addTransaction();

        $output = [];
        if ($user !== null) {
            /** @var TransactionEntity $lastTransaction */
            $lastTransaction = $user->getTransactions()->last();
            $output['transaction_id'] = $lastTransaction->getId();
            $output['amount'] = $lastTransaction->getAmount();
            $output['date'] = $lastTransaction->getDate()->format('d-m-Y');
        }

        return $this->json($output);
    }

    /**
     * @Route("api/update-transaction", name="api_update_transaction")
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function updateTransaction(): JsonResponse
    {
        $transaction = $this->apiService->updateTransaction();

        $output = [];
        if ($transaction !== null) {
            $output['transaction_id'] = $transaction->getId();
            $output['amount'] = $transaction->getAmount();
            $output['date'] = $transaction->getDate()->format('d-m-Y');
        }

        return $this->json($output);
    }

    /**
     * @Route("api/delete-transaction", name="api_delete_transaction")
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function deleteTransaction(): JsonResponse
    {
        $output = $this->apiService->deleteTransaction();

        return $this->json($output);
    }
}
