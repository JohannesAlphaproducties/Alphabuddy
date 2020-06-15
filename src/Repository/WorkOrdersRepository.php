<?php

namespace App\Repository;

use App\Entity\WorkOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WorkOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkOrders[]    findAll()
 * @method WorkOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkOrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkOrders::class);
    }

    public function findOpenWorkOrders()
    {
        return $this->createQueryBuilder('workOrder')
            ->addOrderBy('workOrder.time', 'DESC')
            ->where("workOrder.status NOT LIKE 'afgesloten'")
            ->getQuery()
            ->getResult();
    }

    public function findClosedWorkOrders()
    {
        return $this->createQueryBuilder('workOrder')
            ->where("workOrder.status = 'afgesloten'")
            ->getQuery()
            ->getResult();
    }

    public function findWorkOrdersCompany($id)
    {
        return $this->createQueryBuilder('workOrder')
            ->leftJoin('workOrder.company', 'w')
            ->Where('w.id = ' .$id)
            ->andWhere("workOrder.status NOT LIKE 'afgesloten'")
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return WorkOrders[] Returns an array of WorkOrders objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkOrders
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
