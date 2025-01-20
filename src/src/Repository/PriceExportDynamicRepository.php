<?php

namespace App\Repository;

use App\Entity\PriceExportDynamic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceExportDynamic|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceExportDynamic|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceExportDynamic[]    findAll()
 * @method PriceExportDynamic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceExportDynamicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceExportDynamic::class);
    }

}
