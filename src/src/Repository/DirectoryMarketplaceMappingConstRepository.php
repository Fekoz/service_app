<?php

namespace App\Repository;

use App\Entity\DirectoryMarketplaceMappingConst;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DirectoryMarketplaceMappingConst|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryMarketplaceMappingConst|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryMarketplaceMappingConst[]    findAll()
 * @method DirectoryMarketplaceMappingConst[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryMarketplaceMappingConstRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryMarketplaceMappingConst::class);
    }

    // /**
    //  * @return DirectoryMarketplaceMappingConst[] Returns an array of DirectoryMarketplaceMappingConst objects
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
    public function findOneBySomeField($value): ?DirectoryMarketplaceMappingConst
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
