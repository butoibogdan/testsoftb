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
     * @return mixed
     */
    public function userTransacation($userToken, array $filters = [])
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

        return $qb->getQuery()
            ->useResultCache(true, 3600)
            ->getResult();
    }
}