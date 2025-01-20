<?php

namespace App\Repository;

use App\Entity\Price;
use App\Entity\Specification;
use App\Repository\RepositoryTrait\MasterTrait;
use App\Util\Constant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Specification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Specification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Specification[]    findAll()
 * @method Specification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecificationRepository extends ServiceEntityRepository
{
    use MasterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specification::class);
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->_em;
    }

    public function getRegionAndPrice(string $city, int $limit)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin(Price::class, 'p', Join::WITH, 's.productId = p.productId')
            ->andWhere('s.value=:value')
            ->andWhere('s.name=:name')
            ->addOrderBy('p.count', 'DESC')
            ->setParameter('value', $city)
            ->setParameter('name', Constant::SPECIFICATIONS[Constant::SPEC_COUNTRY][Constant::SPEC_VALUE])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getCountSpecWithName(string $name)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where('s.name=:name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $name
     * @param int $limit
     * @param int $offset
     * @return int|mixed|string
     */
    public function getSpecWithName(string $name, int $limit = 0, int $offset = 0)
    {
        return $this->createQueryBuilder('s')
            ->select('s.value')
            ->where('s.name=:name')
            ->setParameter('name', $name)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @param string $value
     * @return int|mixed|string
     */
    public function getSpecWithNameAndValue(string $name, string $value)
    {
        return $this->createQueryBuilder('s')
            ->select('s.productId, t.value as form, n.value as collection, d.value as design')
            ->leftJoin(Specification::class, 't', Join::WITH, "t.name = 'form' and t.productId = s.productId")
            ->leftJoin(Specification::class, 'n', Join::WITH, "n.name = 'collection' and n.productId = s.productId")
            ->leftJoin(Specification::class, 'd', Join::WITH, "d.name = 'design' and d.productId = s.productId")
            ->where('s.name=:name')
            ->setParameter('name', $name)
            ->andWhere('s.value=:value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult();
    }

}
