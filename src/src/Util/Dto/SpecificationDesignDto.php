<?php


namespace App\Util\Dto;


class SpecificationDesignDto
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $collection;

    /**
     * @var string
     */
    private $design;

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getForm(): string
    {
        return $this->form;
    }

    public function setForm(string $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setCollection(string $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function getDesign(): string
    {
        return $this->design;
    }

    public function setDesign(string $design): self
    {
        $this->design = $design;

        return $this;
    }
}
