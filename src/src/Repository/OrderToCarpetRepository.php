<?php

namespace App\Repository;

use App\Entity\OrderToCarpet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderToCarpet|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderToCarpet|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderToCarpet[]    findAll()
 * @method OrderToCarpet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderToCarpetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderToCarpet::class);
    }

    // /**
    //  * @return OrderToCarpet[] Returns an array of OrderToCarpet objects
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
    public function findOneBySomeField($value): ?OrderToCarpet
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
