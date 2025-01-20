<?php


namespace App\Service;

use App\Entity\Product;
use App\Util\Constant;
use App\Util\Dto\ListingCreateDto;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\SystemServiceRunCreatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Bot;

class ParserRunCreator implements SystemServiceRunCreatorInterface
{
    const MAX_TRY = 5;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var VeneraParser
     */
    private $parser;

    /**
     * @var EntityManagerInterface
     */
    public $entity;

    /**
     * @var object
     */
    private $param;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var int
     */
    private $try;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $appendCount;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Validator $validator, VeneraParser $parser, Bot $bot)
    {
        $this->validator = $validator;
        $this->parser = $parser;
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->try = 0;
        $this->date = new \DateTime();
        $this->appendCount = 0;
    }

    private function append(ParsePageItemDto $value): void
    {
        if (null === $value || null === $value->getUrl() || null === $value->getUuid() || $this->try >= self::MAX_TRY) {
            return;
        }

        try {
            $item = $this->parser->itemParse($value);
        } catch (\OAuthException $e) {
            $this->try++;
            $this->append($value);
            return;
        }

        if (null === $item) {
            $this->try++;
            $this->append($value);
            return;
        }

        $this->validator->veneraImport($item, $this->date);
        $this->appendCount++;
    }

    public function run()
    {
        if (!$this->parser->auth()) {
            return;
        }

        $list = $this->parser->pageParse();
        if (null === $list) {
            return;
        }

        foreach ($list as $item) {
            if (!$item instanceof ParsePageItemDto) {
                continue;
            }

            $this->try = 0;
            if (!$item->getUrl() || !$item->getUuid()) {
                continue;
            }

            /**
             * @var $product Product
             */
            $product = $this->entity->getRepository(Product::class)->findOneBy(['originalUrl' => $item->getUrl()]);
            if ($product === null || null === $product->getOriginalUrl()) {
                $this->append($item);
                $this->parser->clear();
            }
        }

        if ($this->appendCount > 0) {
            $this->bot->message(null, \sprintf('Добавил [%d] ковров в каталог.', $this->appendCount));
        }

    }

}
