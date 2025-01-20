<?php

namespace App\Repository;

use App\Entity\MarketMappingProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MarketMappingProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketMappingProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketMappingProperty[]    findAll()
 * @method MarketMappingProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketMappingPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketMappingProperty::class);
    }

    // /**
    //  * @return MarketMappingProperty[] Returns an array of MarketMappingProperty objects
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
    public function findOneBySomeField($value): ?MarketMappingProperty
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
