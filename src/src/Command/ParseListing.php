<?php

namespace App\Command;

use App\Entity\Product;
use App\Service\Bot;
use App\Service\Validator;
use App\Service\VeneraParser;
use App\Util\Constant;
use App\Util\Dto\ListingCreateDto;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\VarDumper\VarDumper;

class ParseListing extends Command
{
    const COMMAND = 'app:parseListing';
    const DESC = 'parser';

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
     * @var Bot
     */
    private $bot;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Validator $validator, VeneraParser $parser, EntityManagerInterface $entity, Bot $bot)
    {
        $this->validator = $validator;
        $this->parser = $parser;
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, ['SKIP_NULL_VALUES' => true])];
        $this->serializer = new Serializer($normalizers, $encoders);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
            ->setDefinition(
                new InputDefinition([
                    new InputOption('unq', 'u', InputOption::VALUE_REQUIRED),
                    new InputOption('type', 't', InputOption::VALUE_REQUIRED),
                ])
            );
    }

    /**
     * @param string $uuid
     * @return string
     */
    private function update(string $uuid): string
    {
        /**
         * @var $currentItem Product
         */
        $currentItem = $this->entity->getRepository(Product::class)->findByUuid($uuid);

        if (!$currentItem) {
            return 'Not found';
        }

        if (!$this->parser->auth() || null === $currentItem->getUuid() && null === $currentItem->getOriginalUrl()) {
            return 'Fail Auth with command line';
        }

        try {
            $item = $this->parser->itemParse(
                (new ParsePageItemDto)
                    ->setUuid($currentItem->getUuid())
                    ->setUrl($currentItem->getOriginalUrl())
            );
        }catch (\Exception $e) {
            $this->bot->message(null, \sprintf('Ошибка в Обновлении [%s, %s], %s - %s', $currentItem->getOriginalUrl(), $currentItem->getArticle(), $e->getLine(), $e->getMessage()));
            return 'Exception: ' . $e->getMessage();
        }

        if (!$item) {
            $this->bot->message(null, \sprintf('Ошибка в Обновлении [%s, %s], %s', $currentItem->getOriginalUrl(), $currentItem->getArticle(), 'Error to append, object nullable'));
            return 'Error parse';
        }

        $this->bot->message(null, "[Обновление для ". $currentItem->getOriginalUrl() ."]" . $this->serializer->serialize($this->validator->veneraView($item), JsonEncoder::FORMAT,  [AbstractObjectNormalizer::SKIP_NULL_VALUES => true, 'json_encode_options' => JSON_UNESCAPED_UNICODE]));
        $this->validator->veneraImport($item, new \DateTime());

        return 'Success';
    }

    /**
     * @param string $url
     * @return string
     */
    private function create(string $url): string
    {
        if (!$this->parser->auth()) {
            return 'Fail Auth with command line';
        }

        $list = $this->parser->pageParse();
        if (null === $list) {
            return 'list parse error';
        }

        $append = 'Not found';
        foreach ($list as $val) {
            if (!$val instanceof ParsePageItemDto) {
                continue;
            }

            if ($val->getUrl() !== $url || \strpos($val->getUrl(), $url) === false) {
                continue;
            }

            try {
                $item = $this->parser->itemParse($val);
            } catch (\OAuthException $e) {
                $this->bot->message(null, \sprintf('Ошибка в Создании [%s], %s - %s', $url, $e->getLine(), $e->getMessage()));
                $append = 'Exception: ' . $e->getMessage();
                break;
            }

            if (null === $item) {
                $this->bot->message(null, \sprintf('Ошибка в Создании [%s], %s', $url, 'Error to append, object nullable'));
                $append = 'Error to append, object nullable';
                break;
            }

            $this->bot->message(null, "[Создание для ". $url ."]" . $this->serializer->serialize($this->validator->veneraView($item), JsonEncoder::FORMAT,  [AbstractObjectNormalizer::SKIP_NULL_VALUES => true, 'json_encode_options' => JSON_UNESCAPED_UNICODE]));
            $this->validator->veneraImport($item, new \DateTime());
            $append = 'Success';
        }

        return $append;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        switch ($input->getOption('type')) {
            case 'url':
                $output->writeln('URL start');
                $output->writeln($this->create($input->getOption('unq')));
                break;
            case 'uid':
                $output->writeln('UID start');
                $output->writeln($this->update($input->getOption('unq')));
                break;
        }

        return Command::SUCCESS;
    }
}
