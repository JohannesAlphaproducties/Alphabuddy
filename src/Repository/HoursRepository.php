<?php

namespace App\Repository;

use App\Entity\Hours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Hours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hours[]    findAll()
 * @method Hours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hours::class);
    }

    public function findPersonalHours($user, $monday, $friday)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->select('SUM(q.hours) AS total')
            ->andWhere('q.date BETWEEN :monday AND :friday')
            ->setParameter('user', $user)
            ->setParameter('monday', $monday)
            ->setParameter('friday', $friday)
            ->getQuery();
    }

    public function findUserHours($user, $start20, $end20, $startDay, $endDay)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.date BETWEEN :start20 AND :end20')
            //allen van 8 tot 5 ophalen
            ->andWhere('TIME(q.date) BETWEEN :startDay AND :endDay')
            ->select('DATE(q.date) AS datum, TIME(q.date) as dateTime ,SUM(q.hours) AS sumDayHours, q.hours AS hoursValue')
            ->groupBy('datum')
            ->setParameter('user', $user)
            ->setParameter('start20', $start20)
            ->setParameter('end20', $end20)
            ->setParameter('startDay', $startDay)
            ->setParameter('endDay', $endDay)
            ->getQuery()->getResult();
    }

    public function findHoursWeek($user, $monday, $friday)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.date BETWEEN :monday AND :friday')
            ->setParameter('user', $user)
            ->setParameter('monday', $monday)
            ->setParameter('friday', $friday)
            ->getQuery()->getResult();
    }



    // /**
    //  * @return Hours[] Returns an array of Hours objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hours
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
