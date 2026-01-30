<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function countBookingsInWeek(\App\Entity\Client $client): int
    {
        $monday = new \DateTime('monday this week 00:00:00');
        $sunday = new \DateTime('sunday this week 23:59:59');

        return (int) $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->innerJoin('b.activity', 'a')
            ->andWhere('b.client_id = :client')
            ->andWhere('a.date_start BETWEEN :start AND :end')
            ->setParameter('client', $client)
            ->setParameter('start', $monday)
            ->setParameter('end', $sunday)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
