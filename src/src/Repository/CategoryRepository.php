<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getSpecificationsName()
    {
        return $this->_em->createQuery('SELECT DISTINCT s.name FROM App\Entity\Specification s')->getResult();
    }

    public function getSpecificationsWithValue(string $value)
    {
        return $this->_em->createQuery('SELECT DISTINCT s.value FROM App\Entity\Specification s WHERE s.name = :name')
            ->setParameter('name', $value)
            ->getResult();
    }

    public function getPriceHeight()
    {
        return $this->_em->createQuery('SELECT DISTINCT p.height FROM App\Entity\Price p')->getResult();
    }

    public function getPriceWidth()
    {
        return $this->_em->createQuery('SELECT DISTINCT p.width FROM App\Entity\Price p')->getResult();
    }

    public function getPriceMin()
    {
        return $this->_em->createQuery('SELECT DISTINCT MIN(p.price) as min FROM App\Entity\Price p')->getResult();
    }

    public function getPriceMax()
    {
        return $this->_em->createQuery('SELECT DISTINCT MAX(p.price) as max FROM App\Entity\Price p')->getResult();
    }

    public function getAllActive()
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('c.id', 'desc')
            ->getQuery()
            ->getResult();
    }
}
