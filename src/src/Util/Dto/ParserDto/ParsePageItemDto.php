<?php


namespace App\Util\Dto\ParserDto;


class ParsePageItemDto
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $uuid
     * @return ParsePageItemDto
     */
    public function setUuid(string $uuid): ParsePageItemDto
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @param string $url
     * @return ParsePageItemDto
     */
    public function setUrl(string $url): ParsePageItemDto
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function clear(): void
    {
        $this->uuid = '';
        $this->url = '';
    }
}
