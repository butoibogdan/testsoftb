<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public const ALLOW_FILTER_COLUMNS = [
        'amount',
        'date',
    ];

    /**
     * @param $userToken
     * @param array $filters
     * @param array $limit
     * @return mixed
     */
    public function userTransaction($userToken, array $filters = [], array $limit = [])
    {
        $qb = $this->createQueryBuilder('user')
            ->select('user.id', 'user.cnp', 'user.name', 'trans.id', 'trans.amount')
            ->join('user.transactions', 'trans')
            ->where('user.apiToken = :userToken');

        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if (\in_array($column, self::ALLOW_FILTER_COLUMNS, true)) {
                    $query = $column === 'date' ? $column . ' LIKE :' . $column : $column . ' = :' . $column;
                    $qb->andWhere('trans.' . $query);
                    $qb->setParameter($column, $column === 'date' ? '%' . $value . '%' : $value);
                }
            }
        }

        $qb->setParameter('userToken', $userToken);

        if (empty($limit)) {
            $qb->setFirstResult($limit[0])
                ->setMaxResults($limit[1]);
        }

        return $qb->getQuery()
            ->useResultCache(true, 3600)
            ->getResult();
    }
}