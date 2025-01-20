<?php

namespace App\Repository;

use App\Entity\StopList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StopList|null find($id, $lockMode = null, $lockVersion = null)
 * @method StopList|null findOneBy(array $criteria, array $orderBy = null)
 * @method StopList[]    findAll()
 * @method StopList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StopListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StopList::class);
    }

    // /**
    //  * @return StopList[] Returns an array of StopList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StopList
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
