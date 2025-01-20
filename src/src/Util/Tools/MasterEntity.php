<?php

namespace App\Util\Tools;

use App\Entity\Images;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Specification;

class MasterEntity
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var array
     */
    private $price;

    /**
     * @var array
     */
    private $specification;

    /**
     * @var array
     */
    private $image;

    /**
     * @var array
     */
    private $collection;

    public function __construct()
    {
        $this->price = [];
        $this->specification = [];
        $this->image = [];
        $this->collection = [];
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param array $specification
     */
    public function setSpecification(array $specification): void
    {
        $this->specification = $specification;
    }

    /**
     * @param Specification $specification
     */
    public function setSpecificationOne(Specification $specification): void
    {
        $this->specification[] = $specification;
    }

    /**
     * @return array
     */
    public function getSpecification(): array
    {
        return $this->specification;
    }

    public function getSpecificationOne(int $id): ?Specification
    {
        return $this->specification[$id] ?? null;
    }

    /**
     * @param array $price
     */
    public function setPrice(array $price): void
    {
        $this->price = $price;
    }

    /**
     * @param Price $price
     */
    public function setPriceOne(Price $price): void
    {
        $this->price[] = $price;
    }

    /**
     * @return array
     */
    public function getPrice(): array
    {
        return $this->price;
    }

    /**
     * @param int $id
     * @return Price|null
     */
    public function getPriceOne(int $id): ?Price
    {
        return $this->price[$id] ?? null;
    }

    /**
     * @param Images $image
     */
    public function setImageOne(Images $image): void
    {
        $this->image[] = $image;
    }

    /**
     * @param array $image
     */
    public function setImage(array $image): void
    {
        $this->image = $image;
    }

    /**
     * @return array
     */
    public function getImage(): array
    {
        return $this->image;
    }

    /**
     * @param int $id
     * @return Images|null
     */
    public function getImageOne(int $id): ?Images
    {
        return $this->image[$id] ?? null;
    }

    /**
     * @param array $collection
     */
    public function setCollection(array $collection): void
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

}
