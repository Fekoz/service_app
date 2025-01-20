<?php

namespace App\Repository;

use App\Entity\CreteriaCollections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreteriaCollections|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreteriaCollections|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreteriaCollections[]    findAll()
 * @method CreteriaCollections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreteriaCollectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreteriaCollections::class);
    }

    /**
     * @param string $code
     * @return CreteriaCollections|null
     */
    public function getOnce(string $code): ?CreteriaCollections
    {
        return $this->findOneBy(['code' => $code]);
    }


}
