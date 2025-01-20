<?php

namespace App\Repository;

use App\Entity\Presents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Presents|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presents|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presents[]    findAll()
 * @method Presents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presents::class);
    }

    // /**
    //  * @return Presents[] Returns an array of Presents objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Presents
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
