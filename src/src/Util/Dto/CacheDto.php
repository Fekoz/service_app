<?php


namespace App\Util\Dto;


class CacheDto
{
    /**
     * @var integer
     */
    private $type;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param mixed $result
     * @return CacheDto
     */
    public function setResult($result): CacheDto
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $type
     * @return CacheDto
     */
    public function setType(int $type): CacheDto
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}
