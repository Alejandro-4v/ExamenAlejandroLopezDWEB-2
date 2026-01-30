<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findByFilters(
        ?bool $onlyfree,
        ?string $type,
        ?int $page,
        ?int $page_size,
        ?string $sort,
        ?string $order
    ): array {
        $qb = $this->createQueryBuilder('a');

        if ($onlyfree) {
            $qb->andWhere('a.clients_signed < a.max_participants');
        }

        if ($type) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        if ($sort === 'date') {
            $qb->orderBy('a.date_start', $order === 'desc' ? 'DESC' : 'ASC');
        }

        if ($page && $page_size) {
            $qb->setFirstResult(($page - 1) * $page_size)
                ->setMaxResults($page_size);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByFilters(?bool $onlyfree, ?string $type): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        if ($onlyfree) {
            $qb->andWhere('a.clients_signed < a.max_participants');
        }

        if ($type) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
