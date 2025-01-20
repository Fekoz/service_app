<?php

namespace App\Service;

use App\Entity\Options;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemOptionDto;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Dto\WriteParamDto;
use App\Util\Tools\MasterEntity;
use App\Util\Validator\CollectionValidator;
use App\Util\Validator\ImageValidator;
use App\Util\Validator\PriceValidator;
use App\Util\Validator\ProductValidator;
use App\Util\Validator\SpecificationValidator;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\AbstractUnicodeString;

class Helper
{
    /**
     * @var EntityManagerInterface
     */
    public $entity;

    /**
     * @var SpecificationValidator
     */
    private $specificationValidator;

    /**
     * @var PriceValidator
     */
    private $priceValidator;

    /**
     * @var ImageValidator
     */
    private $imageValidator;

    /**
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @var CollectionValidator
     */
    private $collectionValidator;

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var \DOMDocument
     */
    private $domInit;

    /**
     * @var WriteParamDto
     */
    private $paramWrite;

    /**
     * @var ValidatorConfigDto
     */
    private $validatorConfigDto;

    public function __construct(EntityManagerInterface $entity, Queue $queue, SerializerInterface $serializer)
    {
        $this->entity = $entity;
        $this->specificationValidator = new SpecificationValidator;
        $this->priceValidator = new PriceValidator($queue, $serializer);
        $this->imageValidator = new ImageValidator;
        $this->productValidator = new ProductValidator;
        $this->collectionValidator = new CollectionValidator;
        $this->crawler = new Crawler;
        $this->domInit = new \DOMDocument('1.0', 'utf-8');
        $this->paramWrite = new WriteParamDto();
        $this->validatorConfigDto = new ValidatorConfigDto();
        $this->initParam();
    }

    /**
     * @param string $html
     * @return Crawler
     */
    public function initDomCrawler(string $html): Crawler
    {

        $this->crawler->clear();
        $this->domInit->loadHTML(\mb_convert_encoding($html, 'HTML', 'UTF-8'), LIBXML_NOERROR);
        $this->crawler->add($this->domInit);
        // Очищаем DOMDocument и освобождаем память
        $this->domInit->documentElement->parentNode->removeChild($this->domInit->documentElement);
        unset($this->domInit);

        // Создаем новый DOMDocument для будущего использования
        $this->domInit = new \DOMDocument('1.0', 'utf-8');
        return $this->crawler;
    }

    /**
     * @return WriteParamDto
     */
    public function getParam(): WriteParamDto
    {
        return $this->paramWrite;
    }

    public function initParam(): void
    {
        $options = $this->entity->getRepository(Options::class)->findAll();

        /**
         * @var $option Options
         */
        foreach ($options as $option) {
            switch ($option->getName()) {
                // Price
                case Constant::PARSER_DEFAULT_FACTOR:
                    $this->paramWrite->setFactor($option->getValue());
                    break;
                case Constant::PARSER_VENERA_EMAIL:
                    $this->paramWrite->setEmail($option->getValue());
                    break;
                case Constant::PARSER_VENERA_PASSWORD:
                    $this->paramWrite->setPassword($option->getValue());
                    break;
            }
        }
    }

    /**
     * @param array $array
     * @return object
     */
    public function convertObject(array $array): object
    {
        return (object) $array;
    }

    /**
     * @param string $text
     * @return string
     */
    public function helperValidate(string $text) :string
    {
        return \preg_replace('/\W/', '', $text);
    }

    /**
     * @param Client $client
     * @param CookieJar $cookie
     * @param string $template
     * @param string $append
     * @param string $class
     * @param string $method
     * @param array $arrayForm
     * @return Crawler
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpParse(
        Client $client,
        CookieJar $cookie,
        string $template,
        string $append,
        string $class,
        string $method = Constant::GET,
        array $arrayForm = []
    ): Crawler
    {
        $pageUrl = \str_replace(Constant::PARAM_REPLACE, $append, $template);
        $form['cookies'] = $cookie;
        if ($arrayForm) {
            $form['form_params'] = $arrayForm;
        }
        $response = $client->request($method, $pageUrl, $form);
        $crawler = $this->initDomCrawler($response->getBody()->getContents());
        return $crawler->filter($class);
    }

    /**
     * @param Crawler $content
     * @return ParsePageItemDto
     */
    public function perfectArrayItemList(Crawler $content): ParsePageItemDto
    {
        $item = $content->attr('href');
        return (new ParsePageItemDto)
            ->setUrl($item)
            ->setUuid($this->perfectGenerateUuid($item))
        ;
    }

    /**
     * @param string $item
     * @return string
     */
    public function perfectGenerateUuid(string $item): string {
        return \md5($item . Constant::CARPETTI);
    }

    /**
     * @todo VENERA PARSER START
     */

    /**
     * @param Crawler $specification
     * @return array
     */
    public function specificationParsePrepare(Crawler $specification): array
    {
        return $this->specificationValidator->prepare($specification);
    }

    /**
     * @param Crawler $price
     * @return array
     */
    public function priceParsePrepare(Crawler $price): array
    {
        return $this->priceValidator->prepare($price);
    }

    /**
     * @param Crawler $image
     * @return array
     */
    public function imageParsePrepare(Crawler $image): array
    {
        return $this->imageValidator->prepare($image);
    }

    /**
     * @param Crawler $price
     * @return ParseItemOptionDto
     */
    public function productParsePrepare(Crawler $price): ParseItemOptionDto
    {
        return $this->productValidator->prepare($price);
    }

    /**
     * @param Crawler $collection
     * @return array
     */
    public function collectionParsePrepare(Crawler $collection): array
    {
        $itm = [];
        foreach ($this->collectionValidator->prepare($collection) as $value) {
            if ($value) {
                $itm[] = $this->perfectGenerateUuid($value);
            }
        }
        return $itm;
    }

    /**
     * @param MasterEntity $master
     * @param array $item
     * @return MasterEntity
     */
    public function specificationValidate(
        MasterEntity $master,
        array $item): MasterEntity
    {
        $this->specificationValidator->import($master);
        /**
         * @description validatorConfigDto
         */
        $this->validatorConfigDto->clear();

        $this->specificationValidator->formalize($item);
        return $this->specificationValidator->export();
    }


    /**
     * @param MasterEntity $master
     * @param array $item
     * @param float|int $factor
     * @param string $uuid
     * @return MasterEntity
     */
    public function priceValidate(
        MasterEntity $master,
        array $item,
        float $factor = Constant::PRICE_FACTOR,
        string $uuid = ''): MasterEntity
    {
        $this->priceValidator->import($master);
        /**
         * @description validatorConfigDto
         */
        $this->validatorConfigDto->clear();
        $this->validatorConfigDto->setFactor($factor);

        $this->priceValidator->setConfig(
            $this->validatorConfigDto
        );

        $this->priceValidator->formalize($item, $uuid);
        return $this->priceValidator->export();
    }


    /**
     * @param MasterEntity $master
     * @param array $item
     * @param string $dir
     * @param string $uuid
     * @param bool $isDownload
     * @return MasterEntity
     */
    public function imageValidate(
        MasterEntity $master,
        array $item,
        string $dir = Constant::DEFAULT_DIR,
        string $uuid = Constant::DEFAULT_UUID,
        bool $isDownload = Constant::IS_DOWNLOAD_IMAGE): MasterEntity
    {
        $this->imageValidator->import($master);
        /**
         * @description validatorConfigDto
         */
        $this->validatorConfigDto->clear();
        $this->validatorConfigDto->setUuid($uuid);
        $this->validatorConfigDto->setDir($dir);
        $this->validatorConfigDto->setIsDownloadImage($isDownload);

        $this->imageValidator->setConfig(
            $this->validatorConfigDto
        );

        $this->imageValidator->formalize($item);
        return $this->imageValidator->export();
    }

    /**
     * @param MasterEntity $master
     * @param int $price
     * @param float|int $factor
     * @param string $uuid
     * @param string $url
     * @return MasterEntity
     */
    public function productValidate(
        MasterEntity $master,
        int $price = 0,
        float $factor = Constant::PRICE_FACTOR,
        string $uuid = Constant::DEFAULT_UUID,
        string $url = ''): MasterEntity
    {
        $this->productValidator->import($master);
        /**
         * @description validatorConfigDto
         */
        $this->validatorConfigDto->clear();
        $this->validatorConfigDto->setPrice($price);
        $this->validatorConfigDto->setFactor($factor);
        $this->validatorConfigDto->setUuid($uuid);
        $this->validatorConfigDto->setOriginalUrl($url);

        $this->productValidator->setConfig(
            $this->validatorConfigDto
        );

        $this->productValidator->formalize([]);
        return $this->productValidator->export();
    }

    /**
     * @param MasterEntity $master
     * @param array $item
     * @return MasterEntity
     */
    public function collectionValidate(MasterEntity $master, array $item = []): MasterEntity
    {
        $this->collectionValidator->import($master);
        /**
         * @description validatorConfigDto
         */
        $this->validatorConfigDto->clear();
        $this->collectionValidator->formalize($item);
        return $this->collectionValidator->export();
    }

}
