<?php

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\CreteriaCollections;
use App\Entity\Price;
use App\Entity\Product;
use App\Util\Dto\CollectionCCRepoDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Collection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection[]    findAll()
 * @method Collection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collection::class);
    }

    /**
     * @param int $in
     * @param int $out
     * @return Collection|null
     */
    public function getOnce(int $in, int $out): ?Collection
    {
        return $this->findOneBy(['inProductId' => $in, 'outProductId' => $out]);
    }

    /**
     * @param int $in
     * @return Collection[]|null
     */
    public function getList(int $in): ?array
    {
        return $this->findBy(['inProductId' => $in]);
    }

    public function dropAll()
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $in
     */
    public function dropWithList(int $in)
    {
        $this->createQueryBuilder('c')
            ->where('c.inProductId=:inProductId')
            ->setParameter('inProductId', $in)
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function getLastCollection(int $out): CollectionCCRepoDTO
    {
        $collect = new CollectionCCRepoDTO();

        try {
            $collectionCode = $this->createQueryBuilder('c')
                ->select('DISTINCT c.code, r.id, r.name')
                ->where('c.outProductId=:outProductId')
                ->leftJoin(CreteriaCollections::class, 'r', Join::WITH, 'c.code = r.code')
                ->setParameter('outProductId', $out)
                ->andWhere('c.code is not NULL')
                ->getQuery()
                ->getResult()
            ;

            if (count($collectionCode) == 1) {
                $code = $collectionCode[0]['code'] ?? null;
                $id = $collectionCode[0]['id'] ?? null;
                $name = $collectionCode[0]['name'] ?? null;
                if (null !== $code && null !== $id && null !== $name) {
                    return $collect
                        ->setCode($code)
                        ->setId($id)
                        ->setName($name)
                        ;
                }
            }

        } catch (\Exception $e) {

        }

        return $collect;
    }

    public function getMidListWithCollectionCode(string $collectionCode): array
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder
            ->select('DISTINCT p.mid')
            ->from(Collection::class, 'c')
            ->join(Product::class, 'pr', 'WITH', 'c.inProductId = pr.id OR c.outProductId = pr.id')
            ->join(Price::class, 'p', 'WITH', 'pr.id = p.productId')
            ->where('c.code = :collectionCode')
            ->setParameter('collectionCode', $collectionCode);

        $results = $queryBuilder->getQuery()->getResult();

        // Обработка результата и возврат списка Price.mid
        $midList = array_column($results, 'mid');

        return $midList;
    }

    public function getIdWithCollectionCode(string $collectionCode): array
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder
            ->select('DISTINCT p.id')
            ->from(Collection::class, 'c')
            ->join(Product::class, 'pr', 'WITH', 'c.inProductId = pr.id OR c.outProductId = pr.id')
            ->join(Price::class, 'p', 'WITH', 'pr.id = p.productId')
            ->where('c.code = :collectionCode')
            ->setParameter('collectionCode', $collectionCode);

        $results = $queryBuilder->getQuery()->getResult();

        return array_column($results, 'id');
    }

}
