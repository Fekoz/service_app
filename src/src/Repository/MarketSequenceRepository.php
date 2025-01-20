<?php

namespace App\Repository;

use App\Entity\MarketSequence;
use App\Entity\Price;
use App\Entity\Product;
use App\Repository\RepositoryTrait\MasterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MarketSequence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketSequence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketSequence[]    findAll()
 * @method MarketSequence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketSequenceRepository extends ServiceEntityRepository
{
    use MasterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketSequence::class);
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->_em;
    }

    /**
     * @param int $id
     * @return MarketSequence|null
     */
    public function getId(int $id): ?MarketSequence
    {
        return $this->find($id);
    }

    /**
     * @param string $mid
     * @return MarketSequence|null
     */
    public function getMid(string $mid): ?MarketSequence
    {
        return $this->findOneBy(['mid' => $mid]);
    }

    /**
     * @return int|null
     */
    public function getLatestId(): ?int
    {
        try {
            return $this->createQueryBuilder('e')
                ->select('MAX(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    public function getProductIdToMid(string $mid): ?int
    {
        try {
            return $this->createQueryBuilder('s')
                ->select('m.id')
                ->leftJoin(Price::class, 'p', Join::WITH, 's.mid = p.mid')
                ->leftJoin(Product::class, 'm', Join::WITH, 'm.id = p.productId')
                ->where('s.mid=:mid')
                ->setParameter('mid', $mid)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    public function getPriceToMid(string $mid)
    {
        return $this->createQueryBuilder('s')
            ->select('p')
            ->leftJoin(Price::class, 'p', Join::WITH, 's.mid = p.mid')
            ->where('s.mid=:mid')
            ->setParameter('mid', $mid)
            ->getQuery()
            ->getResult();
    }
}
