<?php

namespace App\Repository\RepositoryTrait;

use App\Entity\Collection;
use App\Entity\Images;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Specification;
use App\Util\Tools\MasterEntity;
use App\Util\Constant;

trait MasterTrait
{
    /**
     * @param Product $product
     * @return MasterEntity
     */
    public function masterToProduct(Product $product): MasterEntity
    {
        $parentId = [Constant::MASTER_TRAIT_PARENT => $product->getId()];
        $master = new MasterEntity;
        $master->setProduct($product);
        $master->setSpecification($this->getEm()->getRepository(Specification::class)->findBy($parentId));
        $master->setPrice($this->getEm()->getRepository(Price::class)->findBy($parentId));
        $master->setImage($this->getEm()->getRepository(Images::class)->findBy($parentId));
        return $master;
    }

    /**
     * @param int $id
     * @return MasterEntity
     */
    public function master(int $id): MasterEntity
    {
        $parentId = [Constant::MASTER_TRAIT_PARENT => $id];
        $master = new MasterEntity;
        $master->setProduct($this->getEm()->getRepository(Product::class)->find($id));
        $master->setSpecification($this->getEm()->getRepository(Specification::class)->findBy($parentId));
        $master->setPrice($this->getEm()->getRepository(Price::class)->findBy($parentId));
        $master->setImage($this->getEm()->getRepository(Images::class)->findBy($parentId));
        return $master;
    }

    /**
     * @param Price $price
     * @return MasterEntity
     */
    public function masterToPriceOne(Price $price): MasterEntity
    {
        $parentId = [Constant::MASTER_TRAIT_PARENT => $price->getProductId()];
        $master = new MasterEntity;
        $master->setProduct($this->getEm()->getRepository(Product::class)->find($price->getProductId()));
        $master->setSpecification($this->getEm()->getRepository(Specification::class)->findBy($parentId));
        $master->setPriceOne($price);
        $master->setImage($this->getEm()->getRepository(Images::class)->findBy($parentId));
        return $master;
    }

    /**
     * @param Specification $specification
     * @return MasterEntity
     */
    public function masterToSpecification(Specification $specification): MasterEntity
    {
        $parentId = [Constant::MASTER_TRAIT_PARENT => $specification->getProductId()];
        $master = new MasterEntity;
        $master->setProduct($this->getEm()->getRepository(Product::class)->find($specification->getProductId()));
        $master->setSpecification($this->getEm()->getRepository(Specification::class)->findBy($parentId));
        $master->setPrice($this->getEm()->getRepository(Price::class)->findBy($parentId));
        $master->setImage($this->getEm()->getRepository(Images::class)->findBy($parentId));
        return $master;
    }

    /**
     * @param Product $product
     * @return MasterEntity
     */
    public function masterToProductWithCollection(Product $product): MasterEntity
    {
        $parentId = [Constant::MASTER_TRAIT_PARENT => $product->getId()];
        $toCollect = [Constant::MASTER_TRAIT_PARENT_COLLECTION => $product->getId()];
        $master = new MasterEntity;
        $master->setProduct($product);
        $master->setSpecification($this->getEm()->getRepository(Specification::class)->findBy($parentId));
        $master->setPrice($this->getEm()->getRepository(Price::class)->findBy($parentId));
        $master->setImage($this->getEm()->getRepository(Images::class)->findBy($parentId));
        $master->setCollection($this->getEm()->getRepository(Collection::class)->findBy($toCollect));
        return $master;
    }

}
