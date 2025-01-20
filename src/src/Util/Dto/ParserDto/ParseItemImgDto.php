<?php


namespace App\Util\Dto\ParserDto;


class ParseItemImgDto
{
    /**
     * @var bool
     */
    private $type;

    /**
     * @var string
     */
    private $src;

    /**
     * @param bool $type
     * @return ParseItemImgDto
     */
    public function setType(bool $type): ParseItemImgDto
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isType(): bool
    {
        return $this->type;
    }

    /**
     * @param string $src
     * @return ParseItemImgDto
     */
    public function setSrc(string $src): ParseItemImgDto
    {
        $this->src = $src;
        return $this;
    }

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }
}
