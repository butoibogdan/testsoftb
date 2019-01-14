<?php

namespace AppBundle\Service;

use AppBundle\Entity\TransactionEntity;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiService
{
    public const ID = 'api_transaction_service';

    /** @var Logger */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /** @var RequestStack */
    private $requestStack;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param RequestStack $requestStack
     * @return ApiService
     */
    public function setRequestStack(RequestStack $requestStack): ApiService
    {
        $this->requestStack = $requestStack;
        return $this;
    }

    /**
     * @return Registry
     */
    public function getDoctrine(): Registry
    {
        return $this->doctrine;
    }

    /**
     * @param Registry $doctrine
     * @return ApiService
     */
    public function setDoctrine(Registry $doctrine): ApiService
    {
        $this->doctrine = $doctrine;
        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     * @return ApiService
     */
    public function setLogger(Logger $logger): ApiService
    {
        $this->logger = $logger;
        return $this;
    }

    public function getAllUserTransaction()
    {
        $request = $this->getRequest()->request;
        $userToken = $this->getRequest()->headers->get('X-AUTH-TOKEN');
        $filters = $request->all();

        $limit = [];

        if ($request->get('offset') !== null &&
            $request->get('limit') !== null) {
            $limit = [$request->get('offset'), $request->get('limit')];
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->userTransaction(
                $userToken,
                $filters,
                $limit
            );

        return $user;
    }

    /**
     * @return User|null
     * @throws \InvalidArgumentException
     */
    public function addTransaction(): ?User
    {
        $userToken = $this->getRequest()->headers->get('X-AUTH-TOKEN');
        $request = $this->getRequest()->request;

        $em = $this->getDoctrine()->getManager();

        $transaction = (new TransactionEntity())
            ->setAmount($request->get('amount'))
            ->setDate(new \DateTime());

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['apiToken' => $userToken]);

        if ($user !== null) {
            $user->addTransactions($transaction);
            $em->persist($user);
            $em->flush();
        }

        return $user;
    }

    /**
     * @return TransactionEntity|null
     */
    public function getOneTransaction(): ?TransactionEntity
    {
        $userToken = $this->getRequest()->headers->get('X-AUTH-TOKEN');
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['apiToken' => $userToken]);
        $request = $this->getRequest()->request;

        return $this->getDoctrine()
            ->getRepository(TransactionEntity::class)
            ->findOneBy([
                'user' => $user->getId(),
                'id' => $request->get('transaction_id')
            ]);
    }

    /**
     * @return TransactionEntity|null
     */
    public function updateTransaction(): ?TransactionEntity
    {
        $em = $this->getDoctrine()->getManager();
        $transaction = $this->getOneTransaction();
        if ($transaction !== null) {
            $transaction->setAmount($this->getRequest()->request->get('amount'));
        }
        $em->persist($transaction);
        $em->flush();


        return $transaction;
    }

    /**
     * @return bool
     */
    public function deleteTransaction(): bool
    {
        $em = $this->getDoctrine()->getManager();
        $transaction = $this->getOneTransaction();

        try {
            $em->remove($transaction);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }
}