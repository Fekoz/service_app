<?php

namespace App\Repository;

use App\Entity\DirectoryStorage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DirectoryStorage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryStorage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryStorage[]    findAll()
 * @method DirectoryStorage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryStorageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryStorage::class);
    }

    // /**
    //  * @return DirectoryStorage[] Returns an array of DirectoryStorage objects
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
    public function findOneBySomeField($value): ?DirectoryStorage
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
