<?php


namespace App\Util\Dto;


use App\Service\Bot;
use App\Service\Persist;
use App\Service\Queue;
use App\Service\Sender as SenderService;
use App\Service\Validator;
use App\Service\VeneraParser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;

class SenderUtilOptionDto
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var SenderService|null
     */
    private $senderService;

    /**
     * @var Queue|null
     */
    private $queue;

    /**
     * @var Bot|null
     */
    private $bot;

    /**
     * @var EntityManager|null
     */
    private $entityManager;

    /**
     * @var Validator|null
     */
    private $validator;

    /**
     * @var VeneraParser|null
     */
    private $parser;

    /**
     * @var Persist|null
     */
    private $persist;

    /**
     * @var int
     */
    private $limit;

    /**
     * @return SenderService|null
     */
    public function getSenderService(): ?SenderService
    {
        return $this->senderService;
    }

    /**
     * @param SenderService|null $senderService
     * @return SenderUtilOptionDto
     */
    public function setSenderService(?SenderService $senderService): SenderUtilOptionDto
    {
        $this->senderService = $senderService;
        return $this;
    }

    /**
     * @return Queue|null
     */
    public function getQueue(): ?Queue
    {
        return $this->queue;
    }

    /**
     * @param Queue|null $queue
     * @return SenderUtilOptionDto
     */
    public function setQueue(?Queue $queue): SenderUtilOptionDto
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @return EntityManager|null
     */
    public function getEntityManager(): ?EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager|null $entityManager
     * @return SenderUtilOptionDto
     */
    public function setEntityManager(?EntityManager $entityManager): SenderUtilOptionDto
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return Validator|null
     */
    public function getValidator(): ?Validator
    {
        return $this->validator;
    }

    /**
     * @param Validator|null $validator
     * @return SenderUtilOptionDto
     */
    public function setValidator(?Validator $validator): SenderUtilOptionDto
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return VeneraParser|null
     */
    public function getParser(): ?VeneraParser
    {
        return $this->parser;
    }

    /**
     * @param VeneraParser|null $parser
     * @return SenderUtilOptionDto
     */
    public function setParser(?VeneraParser $parser): SenderUtilOptionDto
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * @return Persist|null
     */
    public function getPersist(): ?Persist
    {
        return $this->persist;
    }

    /**
     * @param Persist|null $persist
     * @return SenderUtilOptionDto
     */
    public function setPersist(?Persist $persist): SenderUtilOptionDto
    {
        $this->persist = $persist;
        return $this;
    }

    /**
     * @return Bot|null
     */
    public function getBot(): ?Bot
    {
        return $this->bot;
    }

    /**
     * @param Bot|null $bot
     * @return SenderUtilOptionDto
     */
    public function setBot(?Bot $bot): SenderUtilOptionDto
    {
        $this->bot = $bot;
        return $this;
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * @param Serializer $serializer
     * @return SenderUtilOptionDto
     */
    public function setSerializer(Serializer $serializer): SenderUtilOptionDto
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @param int $limit
     * @return SenderUtilOptionDto
     */
    public function setLimit(int $limit): SenderUtilOptionDto
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
