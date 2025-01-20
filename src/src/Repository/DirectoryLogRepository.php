<?php

namespace App\Repository;

use App\Entity\DirectoryLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DirectoryLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryLog[]    findAll()
 * @method DirectoryLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryLog::class);
    }

    // /**
    //  * @return DirectoryLog[] Returns an array of DirectoryLog objects
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
    public function findOneBySomeField($value): ?DirectoryLog
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
