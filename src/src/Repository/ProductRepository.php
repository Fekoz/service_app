<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\RepositoryTrait\MasterTrait;
use App\Util\Constant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    use MasterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->_em;
    }

    /**
     * @param string $article
     * @return Product|null
     */
    public function findByArticle(string $article): ?Product
    {
        return $this->findOneBy([Constant::MASTER_TRAIT_ARTICLE => $article]);
    }

    /**
     * @param string $uuid
     * @return Product|null
     */
    public function findByUuid(string $uuid): ?Product
    {
        return $this->findOneBy([Constant::MASTER_TRAIT_UUID => $uuid]);
    }

    /**
     * @param string $url
     * @return Product|null
     */
    public function findByUrl(string $url): ?Product
    {
        return $this->findOneBy([Constant::MASTER_TRAIT_URL => $url]);
    }

    /**
     * @param \DateTime $date
     * @return Product|null
     */
    public function findByUpdate(\DateTime $date): ?Product
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.updateAt < :date')
            ->andWhere('i.isGlobalUpdateLock = false')
            ->orWhere('i.isGlobalUpdateLock is NULL')
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $qb[0] ?? null;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getCount() {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|null
     */
    public function getArrayList(int $limit, int $offset): ?array
    {
        return $this->createQueryBuilder('i')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('i.id', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function getUpdateLastItemList(\DateTime $time, \DateInterval $away, int $limit): \Generator
    {
        $time->sub($away);
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->select('p.id')
            ->where('p.updateAt < :time')
            ->setParameter('time', $time)
            ->setMaxResults($limit)
        ;

        try {
            $results = $queryBuilder->getQuery()->toIterable();

            if (empty($results)) {
                return null;
            }

            foreach ($results as $result) {
                yield $result['id'];
            }
        } catch (NoResultException $exception) {
            return null;
        }
    }

    public function getCountUpdateLastItems(\DateTime $time, \DateInterval $away): int
    {
        $time->sub($away);
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->select('COUNT(p.id)')
            ->where('p.updateAt < :time')
            ->setParameter('time', $time);

        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NoResultException | \Exception $exception) {
            return 0;
        }
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

    public function isArticleExists(string $article): bool
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.article = :article')
            ->setParameter('article', $article);

        try {
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPointerItemList(int $limit, int $offset): \Generator
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('i.id', 'asc')
        ;

        try {
            $results = $queryBuilder->getQuery()->toIterable();

            if (empty($results)) {
                return null;
            }

            foreach ($results as $result) {
                yield $result;
            }
        } catch (NoResultException $exception) {
            return null;
        }
    }

    public function getMaxId(): int
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('COUNT(i.id) as max_id');

        try {
            $result = $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            $result = 0;
        }

        return (int) $result;
    }

}
