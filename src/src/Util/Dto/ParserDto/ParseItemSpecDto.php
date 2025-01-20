<?php


namespace App\Util\Dto\ParserDto;


class ParseItemSpecDto
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $name
     * @return ParseItemSpecDto
     */
    public function setName(string $name): ParseItemSpecDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $value
     * @return ParseItemSpecDto
     */
    public function setValue(string $value): ParseItemSpecDto
    {
        $this->value = $value;
        return $this;
    }
}
