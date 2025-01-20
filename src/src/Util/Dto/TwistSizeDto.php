<?php


namespace App\Util\Dto;


class TwistSizeDto
{
    const DEFAULT_PACKAGE = 1;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return TwistSizeDto
     */
    public function setLength(int $length): TwistSizeDto
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return TwistSizeDto
     */
    public function setWidth(int $width): TwistSizeDto
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return TwistSizeDto
     */
    public function setHeight(int $height): TwistSizeDto
    {
        $this->height = $height;
        return $this;
    }


}
