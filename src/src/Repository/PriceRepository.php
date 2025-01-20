<?php

namespace App\Repository;

use App\Entity\Price;
use App\Repository\RepositoryTrait\MasterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Price|null find($id, $lockMode = null, $lockVersion = null)
 * @method Price|null findOneBy(array $criteria, array $orderBy = null)
 * @method Price[]    findAll()
 * @method Price[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceRepository extends ServiceEntityRepository
{
    use MasterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->_em;
    }

    public function getPriceOffset(int $offsetPrice, int $count, int $limit)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price > :price')
            ->andWhere('p.count > :count')
            ->setParameter('price', $offsetPrice)
            ->setParameter('count', $count)
            ->addOrderBy('p.count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getPriceCountOrder(int $limit)
    {
        return $this->createQueryBuilder('p')
            ->addOrderBy('p.count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMid(string $mid): ?Price
    {
        return $this->findOneBy(['mid' => $mid], ['id' => 'DESC']);
    }

    public function getPriceUuid(int $productId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.uuid')
            ->where('p.productId=:productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    public function getPriceMid(int $productId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.mid')
            ->where('p.productId=:productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    public function getPriceWithProductId(int $productId): array
    {
        return $this->findBy(['productId' => $productId], ['id' => 'DESC']);
    }

    public function isPriceMidAttempt(string $mid): bool
    {
        try {
            $count = $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->andWhere('p.mid = :mid')
                ->setParameter('mid', $mid)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            $count = 0;
        }

        return $count > 0;
    }

    public function hasNonZeroCountForProductId(int $productId): bool
    {
        try {
            $query = $this->createQueryBuilder('p')
                ->select('SUM(p.count)')
                ->where('p.productId = :productId')
                ->setParameter('productId', $productId)
                ->getQuery()
                ->getSingleScalarResult();
            return $query > 0;
        } catch (NoResultException | NonUniqueResultException $e) {

        }
        return false;
    }

    public function isUuidExists(string $uuid): bool
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        try {
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

}
