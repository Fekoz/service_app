<?php

namespace App\Service\Explode;

use App\Entity\Images;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Specification;
use App\Repository\ProductRepository;
use App\Util\Constant;
use App\Util\PersistUtil;
use App\Util\Tools\MasterEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Persist;
use Doctrine\ORM\Mapping\Entity;

abstract class PersistExplode
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var array
     */
    protected $specification;

    /**
     * @var array
     */
    protected $price;

    /**
     * @var array
     */
    protected $image;

    /**
     * @var \DateTime
     */
    protected $dateNow;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var EntityManagerInterface
     */
    protected $entity;

    /**
     * @var PersistUtil
     */
    protected $util;

    abstract public function getUpdateItem(\DateTime $time, \DateInterval $away): ?Product;

    abstract public function touch(Product $item, \DateTime $date);

    abstract public function write(MasterEntity $item, \DateTime $date): bool;

    abstract public function clear();

    protected function __construct()
    {
        $this->util = new PersistUtil();
    }

    protected function create()
    {
        $this->product->setDateCreate($this->dateNow);
        $this->product->setDateUpdate($this->dateNow);
        $this->entity->persist($this->product);
        $this->entity->flush();

        $this->price = $this->util->priceRebuild($this->price);
        $this->util->appendPersist($this->entity, $this->specification, $this->product->getId());
        $this->util->appendPersist($this->entity, $this->price, $this->product->getId());
        $this->util->appendPersist($this->entity, $this->image, $this->product->getId());
        $this->entity->flush();
    }

    protected function update(Product $product)
    {
        if ($product->isGlobalUpdateLock()) {
            return;
        }

        $product = $this->util->defaultValidateLocker($product);
        $this->entity->persist($product);
        $this->entity->flush();

        /**
         * @var $repo ProductRepository
         */
        $repo = $this->entity->getRepository(Product::class);
        $master = $repo->masterToProduct($product);

        /**
         * @description Product
         */
        $product = $master->getProduct();
        $this->updateToProduct($product);

        /**
         * @description Price
         */
        $price = $master->getPrice();
        $this->updateToPrice($price, $product->getId(), $product->isPriceLock());

        /**
         * @description Specification
         */
        $specification = $master->getSpecification();
        if (!$product->isSpecificationLock()) {
            $this->updateToSpecification($specification, $product->getId());
        }

        /**
         * @description Image
         */
        $image = $master->getImage();
        if (!$product->isImagesLock()) {
            $this->updateToImages($image, $product->getId());
        }

        /**
         * @description Change Product Active
         */
        $this->changeActiveWithPriceList($product);
        $this->entity->clear();
    }

    /**
     * @description Product
     */
    private function updateToProduct(Product $product)
    {
        if (!$product->isProductLock()) {
            $this->product->setActive(true);
            $this->product->isActive() === $product->isActive() ?: $product->setActive($this->product->isActive());
            $this->product->getName() === $product->getName() ?: $product->setName($this->product->getName());
            $this->product->getFactor() === $product->getFactor() ?: $product->setFactor($this->product->getFactor());
            $product->setFactorFull($this->product->getFactorFull());
            $this->product->getFullPrice() === $product->getFullPrice() ?: $product->setFullPrice($this->product->getFullPrice());
        }

        /**
         * @description uuid change
         */
        if ($this->product->getUuid() !== $product->getUuid()) {
            if(false === $this->entity->getRepository(Product::class)->isUuidExists($this->product->getUuid())) {
                $product->setUuid($this->product->getUuid());
            }
        }

        /**
         * @description article change
         */
        if ($this->product->getArticle() !== $product->getArticle()) {
            if(false === $this->entity->getRepository(Product::class)->isArticleExists($this->product->getArticle())) {
                $product->setArticle($this->product->getArticle());
            }
        }

        $product->setDateUpdate($this->dateNow);
        $this->entity->persist($product);
        $this->entity->flush();
    }

    /**
     * @description Price
     */
    private function updateToPrice(array $price, int $productId, bool $isLock)
    {
        $this->price = $this->util->priceRebuild($this->price);
        $this->util->compare($productId, $this->price, $price, $this->entity, $isLock);
        $this->entity->flush();
    }

    /**
     * @description Specification
     */
    private function updateToSpecification(array $specification, int $productId)
    {
        $specification = $this->util->removeDuplicates($this->entity, $specification);
        $this->entity->flush();

        $this->util->compare($productId, $this->specification, $specification, $this->entity, false);
        $this->entity->flush();
    }

    /**
     * @description Images
     */
    private function updateToImages(array $images, int $productId)
    {
        $images = $this->util->dropImagesToDoubles($images, $this->entity);
        $this->util->compare($productId, $this->image, $images, $this->entity, false);
        $this->entity->flush();
    }

    private function changeActiveWithPriceList(Product $product)
    {
        $product->setActive(
            $this->entity->getRepository(Price::class)->hasNonZeroCountForProductId(
                $product->getId()
            )
        );
        $this->entity->persist($product);
        $this->entity->flush();
    }

}
