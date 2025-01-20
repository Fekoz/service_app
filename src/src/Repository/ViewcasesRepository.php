<?php

namespace App\Repository;

use App\Entity\Viewcases;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Viewcases|null find($id, $lockMode = null, $lockVersion = null)
 * @method Viewcases|null findOneBy(array $criteria, array $orderBy = null)
 * @method Viewcases[]    findAll()
 * @method Viewcases[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewcasesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Viewcase::class);
    }
}
