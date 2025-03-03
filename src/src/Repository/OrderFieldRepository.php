<?php

namespace App\Repository;

use App\Entity\OrderField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderField|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderField|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderField[]    findAll()
 * @method OrderField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderField::class);
    }

    // /**
    //  * @return OrderField[] Returns an array of OrderField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderField
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
