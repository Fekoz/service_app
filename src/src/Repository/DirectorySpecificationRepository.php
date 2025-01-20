<?php

namespace App\Repository;

use App\Entity\DirectorySpecification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DirectorySpecification|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectorySpecification|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectorySpecification[]    findAll()
 * @method DirectorySpecification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectorySpecificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectorySpecification::class);
    }

    // /**
    //  * @return DirectorySpecification[] Returns an array of DirectorySpecification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DirectorySpecification
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
