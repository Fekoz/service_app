<?php

namespace App\Repository;

use App\Entity\Showcases;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Showcases|null find($id, $lockMode = null, $lockVersion = null)
 * @method Showcases|null findOneBy(array $criteria, array $orderBy = null)
 * @method Showcases[]    findAll()
 * @method Showcases[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowcasesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Showcases::class);
    }

    // /**
    //  * @return Showcases[] Returns an array of Showcases objects
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
    public function findOneBySomeField($value): ?Showcases
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
