<?php

namespace App\Util\Tools\Config;

class ConfigParserModel
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $count;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $authPage;

    /**
     * @var string
     */
    private $filterPage;

    /**
     * @var string
     */
    private $catalogPage;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var bool
     */
    private $isFake;

    /**
     * @var string
     */
    private $fakeName;

    /**
     * @var string
     */
    private $fakeKey;

    /**
     * @var string
     */
    private $fakeValue;

    /**
     * @var string
     */
    private $warehouseCategories;

    /**
     * @param string $email
     * @return ConfigParserModel
     */
    public function setEmail(string $email): ConfigParserModel
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return ConfigParserModel
     */
    public function setPassword(string $password): ConfigParserModel
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param int $count
     * @return ConfigParserModel
     */
    public function setCount(int $count): ConfigParserModel
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param string $name
     * @return ConfigParserModel
     */
    public function setName(string $name): ConfigParserModel
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $url
     * @return ConfigParserModel
     */
    public function setUrl(string $url): ConfigParserModel
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $authPage
     * @return ConfigParserModel
     */
    public function setAuthPage(string $authPage): ConfigParserModel
    {
        $this->authPage = $authPage;
        return $this;
    }

    /**
     * @param string $filterPage
     * @return ConfigParserModel
     */
    public function setFilterPage(string $filterPage): ConfigParserModel
    {
        $this->filterPage = $filterPage;
        return $this;
    }

    /**
     * @param string $catalogPage
     * @return ConfigParserModel
     */
    public function setCatalogPage(string $catalogPage): ConfigParserModel
    {
        $this->catalogPage = $catalogPage;
        return $this;
    }

    /**
     * @param string $errorMessage
     * @return ConfigParserModel
     */
    public function setErrorMessage(string $errorMessage): ConfigParserModel
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @param int $limit
     * @return ConfigParserModel
     */
    public function setLimit(int $limit): ConfigParserModel
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param bool $isFake
     * @return ConfigParserModel
     */
    public function setIsFake(bool $isFake): ConfigParserModel
    {
        $this->isFake = $isFake;
        return $this;
    }

    /**
     * @param string $fakeName
     * @return ConfigParserModel
     */
    public function setFakeName(string $fakeName): ConfigParserModel
    {
        $this->fakeName = $fakeName;
        return $this;
    }

    /**
     * @param string $fakeKey
     * @return ConfigParserModel
     */
    public function setFakeKey(string $fakeKey): ConfigParserModel
    {
        $this->fakeKey = $fakeKey;
        return $this;
    }

    /**
     * @param string $fakeValue
     * @return ConfigParserModel
     */
    public function setFakeValue(string $fakeValue): ConfigParserModel
    {
        $this->fakeValue = $fakeValue;
        return $this;
    }

    /**
     * @param string $warehouseCategories
     * @return ConfigParserModel
     */
    public function setWarehouseCategories(string $warehouseCategories): ConfigParserModel
    {
        $this->warehouseCategories = $warehouseCategories;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

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
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getAuthPage(): string
    {
        return $this->authPage;
    }

    /**
     * @return string
     */
    public function getFilterPage(): string
    {
        return $this->filterPage;
    }

    /**
     * @return string
     */
    public function getCatalogPage(): string
    {
        return $this->catalogPage;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function isFake(): bool
    {
        return $this->isFake;
    }

    /**
     * @return string
     */
    public function getFakeName(): string
    {
        return $this->fakeName;
    }

    /**
     * @return string
     */
    public function getFakeKey(): string
    {
        return $this->fakeKey;
    }

    /**
     * @return string
     */
    public function getFakeValue(): string
    {
        return $this->fakeValue;
    }

    /**
     * @return string
     */
    public function getWarehouseCategories(): string
    {
        return $this->warehouseCategories;
    }

}
