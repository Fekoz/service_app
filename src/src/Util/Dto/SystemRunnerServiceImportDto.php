<?php


namespace App\Util\Dto;


class SystemRunnerServiceImportDto
{
    /**
     * @var string
     */
    private $article;

    /**
     * @var \DateTime | null
     */
    private $last;

    /**
     * @param string $article
     * @return SystemRunnerServiceImportDto
     */
    public function setArticle(string $article): SystemRunnerServiceImportDto
    {
        $this->article = $article;
        return $this;
    }

    /**
     * @return string
     */
    public function getArticle(): string
    {
        return $this->article;
    }

    /**
     * @return \DateTime|null
     */
    public function getLast(): ?\DateTime
    {
        return $this->last;
    }

    /**
     * @param \DateTime|null $last
     * @return SystemRunnerServiceImportDto
     */
    public function setLast(?\DateTime $last): SystemRunnerServiceImportDto
    {
        $this->last = $last;
        return $this;
    }

}
