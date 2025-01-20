<?php


namespace App\Util\Dto;


class SenderMarketExportDto
{
    /**
     * @var string
     */
    private $mid;

    /**
     * @var int
     */
    private $counterPkg;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $mid
     * @return SenderMarketExportDto
     */
    public function setMid(string $mid): SenderMarketExportDto
    {
        $this->mid = $mid;
        return $this;
    }

    /**
     * @return string
     */
    public function getMid(): string
    {
        return $this->mid;
    }

    /**
     * @param int $counterPkg
     * @return SenderMarketExportDto
     */
    public function setCounterPkg(int $counterPkg): SenderMarketExportDto
    {
        $this->counterPkg = $counterPkg;
        return $this;
    }

    /**
     * @return int
     */
    public function getCounterPkg(): int
    {
        return $this->counterPkg;
    }

    /**
     * @param string $name
     * @return SenderMarketExportDto
     */
    public function setName(string $name): SenderMarketExportDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
