<?php

namespace App\Util\Dto;

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;

class MarketYmlDtoOfferSimple extends OfferSimple
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $productArticle;

    /**
     * @var string
     */
    private $productUuid;

    /**
     * @var string
     */
    private $priceUuid;

    /**
     * @var string
     */
    private $priceMid;

    /**
     * @var ?float
     */
    private $packingHeight;

    /**
     * @var ?float
     */
    private $packingWidth;

    /**
     * @var ?float
     */
    private $packingSize;

    /**
     * @var ?int
     */
    private $productH;

    /**
     * @var ?int
     */
    private $productW;

    /**
     * @var bool
     */
    private $isCircle;

    /**
     * @var int
     */
    private $priceCount;

    /**
     * @var array
     */
    private $productParams;

    /**
     * @var string
     */
    private $productEan;

    /**
     * @var TwistSizeDto
     */
    private $statDimension;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string | null
     */
    private $collectionCode;

    /**
     * @var int | null
     */
    private $collectionId;

    /**
     * @var string | null
     */
    private $collectionName;

    /**
     * @var Array<string>
     */
    private $tags;

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductArticle(): string
    {
        return $this->productArticle;
    }

    public function setProductArticle(string $productArticle): self
    {
        $this->productArticle = $productArticle;

        return $this;
    }

    public function getProductUuid(): string
    {
        return $this->productUuid;
    }

    public function setProductUuid(string $productUuid): self
    {
        $this->productUuid = $productUuid;

        return $this;
    }

    public function getPriceUuid(): string
    {
        return $this->priceUuid;
    }

    public function setPriceUuid(string $priceUuid): self
    {
        $this->priceUuid = $priceUuid;

        return $this;
    }

    public function getPriceMid(): string
    {
        return $this->priceMid;
    }

    public function setPriceMid(string $priceMid): self
    {
        $this->priceMid = $priceMid;

        return $this;
    }

    public function getPackingHeight(): ?float
    {
        return $this->packingHeight;
    }

    public function setPackingHeight(?float $packingHeight): self
    {
        $this->packingHeight = $packingHeight;

        return $this;
    }

    public function getPackingWidth(): ?float
    {
        return $this->packingWidth;
    }

    public function setPackingWidth(?float $packingWidth): self
    {
        $this->packingWidth = $packingWidth;

        return $this;
    }

    public function getPackingSize(): ?float
    {
        return $this->packingSize;
    }

    public function setPackingSize(?float $packingSize): self
    {
        $this->packingSize = $packingSize;

        return $this;
    }

    public function getProductW(): ?int
    {
        return $this->productW;
    }

    public function setProductW(?int $productW): self
    {
        $this->productW = $productW;

        return $this;
    }

    public function getProductH(): ?int
    {
        return $this->productH;
    }

    public function setProductH(?int $productH): self
    {
        $this->productH = $productH;

        return $this;
    }

    public function isCircle(): bool
    {
        return $this->isCircle;
    }

    public function setCircle(bool $isCircle): self
    {
        $this->isCircle = $isCircle;

        return $this;
    }

    public function getPriceCount(): int
    {
        return $this->priceCount;
    }

    public function setPriceCount(int $priceCount): self
    {
        $this->priceCount = $priceCount;

        return $this;
    }

    public function getProductParams(): ?array
    {
        return $this->productParams;
    }

    public function setProductParams(?array $productParams): self
    {
        $this->productParams = $productParams;

        return $this;
    }

    public function getProductEan(): ?string
    {
        return $this->productEan;
    }

    public function setProductEan(?string $productEan): self
    {
        $this->productEan = $productEan;

        return $this;
    }

    public function getStatDimension(): TwistSizeDto
    {
        return $this->statDimension;
    }

    public function setStatDimension(TwistSizeDto $statDimension): self
    {
        $this->statDimension = $statDimension;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setCollectionCode(?string $collectionCode): self
    {
        $this->collectionCode = $collectionCode;

        return $this;
    }

    public function getCollectionCode(): ?string
    {
        return $this->collectionCode;
    }

    public function setCollectionId(?int $collectionId): self
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    public function getCollectionId(): ?int
    {
        return $this->collectionId;
    }

    public function setCollectionName(?string $collectionName): self
    {
        $this->collectionName = $collectionName;

        return $this;
    }

    public function getCollectionName(): ?string
    {
        return $this->collectionName;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): MarketYmlDtoOfferSimple
    {
        $this->tags = $tags;
        return $this;
    }
}
