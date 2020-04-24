<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

     /**
      * @return Company[] Returns an array of Company objects
      */

//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOpenTickets()
//    {
//        return $this->createQueryBuilder('q')
//            ->leftJoin('q.tickets', 't')
//            ->where("t.status = 'beginnen'")
//            ->orWhere("t.status = 'bezig'")
//            ->getQuery()
//            ->getResult();
//    }

    public function findCompanys($searchString)
    {
        return $this->createQueryBuilder('q')
            ->Where('q.name LIKE :name')
            ->orWhere('q.billing_town LIKE :name')
            ->orWhere('q.email LIKE :name')
            ->orWhere('q.billing_address LIKE :name')
            ->orWhere('q.billing_zip LIKE :name')
            ->setParameter('name', '%'.$searchString.'%')
            ->getQuery()
            ->getResult();
    }

    public function findAllCompanies()
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.name', 'ASC')
            ->getQuery()
            ->getResult();
    }



//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
