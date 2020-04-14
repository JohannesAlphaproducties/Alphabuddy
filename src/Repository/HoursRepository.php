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

    public function findPersonalHoursWeek($user, $monday, $friday)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->select('q.hours')
            ->where('q.date BETWEEN :monday AND :friday')
            ->setParameter('user', $user)
            ->setParameter('monday', $monday)
            ->setParameter('friday', $friday)
            ->getQuery();
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
