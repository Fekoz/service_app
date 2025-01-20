<?php

namespace App\Repository;

use App\Entity\DirectoryMeter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DirectoryMeter|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryMeter|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryMeter[]    findAll()
 * @method DirectoryMeter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryMeterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryMeter::class);
    }

    // /**
    //  * @return DirectoryMeter[] Returns an array of DirectoryMeter objects
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
    public function findOneBySomeField($value): ?DirectoryMeter
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
