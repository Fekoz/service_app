<?php

namespace App\Repository;

use App\Entity\MarketMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MarketMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketMapping[]    findAll()
 * @method MarketMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketMapping::class);
    }

    // /**
    //  * @return MarketMapping[] Returns an array of MarketMapping objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MarketMapping
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
