<?php

namespace App\Repository;

use App\Entity\Parse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parse[]    findAll()
 * @method Parse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parse::class);
    }

    // /**
    //  * @return Parse[] Returns an array of Parse objects
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
    public function findOneBySomeField($value): ?Parse
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
