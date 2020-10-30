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

    public function findUserHours($user, $start20, $end20)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.date BETWEEN :start20 AND :end20')
            //allen van 8 tot 5 ophalen
            ->select('DATE(q.date) AS datum, TIME(q.date) as dateTime ,SUM(q.hours) AS sumDayHours, q.hours AS hoursValue')
            ->groupBy('datum')
            ->setParameter('user', $user)
            ->setParameter('start20', $start20)
            ->setParameter('end20', $end20)
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

    public function findHoursMonth($user)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->select('MONTH(q.date) AS datum')
            ->groupBy('datum')
            ->setParameter('user', $user)
            ->getQuery()->getResult();
    }

    public function findHoursMonthUser($user, $month)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->select('q.hours AS hour, q.date AS date')
            ->where(':user = q.user')
            ->andWhere('MONTH(q.date) = :month')
            ->setParameter('user', $user)
            ->setParameter('month', $month)
            ->getQuery()->getResult();
    }

    public function findHoursMonthUserExcel($user, $month)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->select('q.hours AS hour, q.date AS date, SUM(q.hours) AS sumDayHours')
            ->where(':user = q.user')
            ->andWhere('MONTH(q.date) = :month')
            ->groupBy('date')
            ->setParameter('user', $user)
            ->setParameter('month', $month)
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
