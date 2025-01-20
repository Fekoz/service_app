<?php


namespace App\Util\Dto;


class WriteSheafCollectionDto
{
    /**
     * @var int
     */
    private $in;

    /**
     * @var int
     */
    private $out;

    /**
     * @var string
     */
    private $keyCode;

    public function getIn(): int
    {
        return $this->in;
    }

    public function setIn(int $in): self
    {
        $this->in = $in;

        return $this;
    }

    public function getOut(): int
    {
        return $this->out;
    }

    public function setOut(int $out): self
    {
        $this->out = $out;

        return $this;
    }

    public function getKeyCode(): string
    {
        return $this->keyCode;
    }

    public function setKeyCode(string $keyCode): self
    {
        $this->keyCode = $keyCode;

        return $this;
    }
}
