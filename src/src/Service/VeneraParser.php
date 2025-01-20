<?php

namespace App\Service;

use App\Util\Dto\ParserDto\ParseItemDto;
use App\Util\Dto\ParserDto\ParseItemPriceDto;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\Tools\Config\ConfigParserModel;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;
use App\Util\Constant;
use OAuthException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VeneraParser
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var ParserDecompose
     */
    private $parser;

    /**
     * @var ConfigParserModel
     */
    private $config;

    /**
     * @var CookieJar
     */
    public $cookie;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var LogService
     */
    private $log;

    /**
     * @var ParseItemDto
     */
    private $parseItemDto;

    public function __construct(Helper $helper, ParserDecompose $parserDecompose, LogService $log)
    {
        $this->helper = $helper;
        $this->parser = $parserDecompose;
        $this->config = $this->parser->init(Constant::PARSER_NAME, new \DateTime());
        $this->cookie = new CookieJar;
        $this->client = new Client(['cookies' => $this->cookie, 'base_uri' => $this->config->getUrl()]);
        $this->log = $log;
        $this->parseItemDto = new ParseItemDto();
    }

    /**
     * @param Crawler $crawler
     * @return bool
     * Проверка на авторизацию
     */
    private function isAuth(Crawler $crawler) : bool
    {
        $content = $crawler->filter(Constant::CLASSES['login']);
        return $this->config->getErrorMessage() !== $content->text();
    }

    /**
     * @return bool
     * @throws GuzzleException
     * Старая авторизация из БД
     */
    private function isAuthLast(): bool
    {
        $lastAuth = $this->parser->stream($this->config->getName());
        if (!$lastAuth) {
            $this->log->register(Logger::CRITICAL, Constant::LOG_AUTH_SESSION_ENDED);
            return false;
        }
        $this->cookie = CookieJar::fromArray([$lastAuth->getPrefix() => $lastAuth->getSession()], $lastAuth->getName());
        $crawler = $this->httpPrepareParse('', '', Constant::CLASSES['body']);
        $this->log->register(Logger::DEBUG, Constant::LOG_AUTH);
        return $this->isAuth($crawler);
    }

    public function reInitAuthParam(): void
    {
        $this->helper->initParam();
    }

    /**
     * @param string $url
     * @return string
     * Выбить текущий csrf сертификат, требующийся для текущей сессии при авторизации
     */
    private function getCsrf(string $url): string
    {
        try {
            $content = ($this->httpPrepareParse($url, '', Constant::CLASSES['body']))
                ->filter('input[name="_csrf_token"]')
                ->attr('value');
        } catch (GuzzleException $e) {
            $content = "";
        }

        return $content;
    }

    /**
     * @return bool
     * @throws GuzzleException
     * Новая сессия
     */
    private function isAuthNew(): bool
    {
        $paramWrite = $this->helper->getParam();
        if ($paramWrite->getEmail() !== null) {
            $this->config->setEmail($paramWrite->getEmail());
        }

        if ($paramWrite->getPassword() !== null) {
            $this->config->setPassword($paramWrite->getPassword());
        }

        try {
            $crawler = $this->httpPrepareParse(
                $this->config->getAuthPage(),
                '',
                Constant::CLASSES['body'],
                Constant::POST,
                [
                    Constant::AUTH_LOGIN => $this->config->getEmail(),
                    Constant::AUTH_PASS => $this->config->getPassword(),
                    Constant::AUTH_CSRF => $this->getCsrf($this->config->getAuthPage())
                ]
            );
        } catch (\Exception $e) {
            return false;
        }

        $this->parser->context($this->cookie);
        $this->log->register(Logger::DEBUG, Constant::LOG_AUTH);
        return $this->isAuth($crawler);
    }

    private function registerWarehouseCategories(): void
    {
        $this->httpPrepareParse($this->config->getUrl() . $this->config->getWarehouseCategories(), '', Constant::CLASSES['body']);
    }

    /**
     * @return bool
     * Авторизация
     */
    public function auth(): bool
    {
        try {
            $isAuth = $this->isAuthLast() || $this->isAuthNew();
            if (!$isAuth) {
                $this->log->register(Logger::CRITICAL, Constant::LOG_AUTH_CRITICAL);
                return false;
            }
            // append categories
            $this->registerWarehouseCategories();
            return true;
        } catch (\InvalidArgumentException | GuzzleException $exception) {
            $this->log->registerException(
                Logger::ERROR,
                $exception instanceof GuzzleException
                    ? Constant::LOG_AUTH_HTTP
                    : Constant::LOG_AUTH_CRAWLER,
                $exception
            );
        }
        $this->log->register(Logger::CRITICAL, Constant::LOG_AUTH_ERROR);
        return false;
    }

    /**
     * @param ParsePageItemDto $val
     * @return ParseItemDto
     * @throws GuzzleException
     * @throws OAuthException
     */
    private function itemParseCallback(ParsePageItemDto $val): ParseItemDto
    {
        $content = $this->httpPrepareParse($val->getUrl(), '', Constant::CLASSES['body']);
        if (!$this->isAuth($content)) {
            var_dump('not auth');
            $this->log->registerException(Logger::CRITICAL, Constant::LOG_AUTH_SESSION_ENDED, null, ['url' => $val->getUrl(), 'uuid' => $val->getUuid()]);
            throw new OAuthException('item');
        }

        $crawler = $this->helper->initDomCrawler($content->html());
        $this->log->register(Logger::DEBUG, Constant::LOG_PARSE, ['url' => $val->getUrl(), 'uuid' => $val->getUuid()]);
        $this->log->pointClear();

        $this->parseItemDto->clear();

        $this->parseItemDto->setOption($this->helper->productParsePrepare($crawler));
        $this->parseItemDto->setSpec($this->helper->specificationParsePrepare($crawler->filter(Constant::CLASSES['specification_item'])));
        $this->parseItemDto->setImg($this->helper->imageParsePrepare($crawler->filter(Constant::CLASSES['image_item'])));

        $this->parseItemDto->setPrice([]);
        $this->parseItemDto->getOption()->setUuid($val->getUuid());
        $this->parseItemDto->getOption()->setUrl($val->getUrl());

        foreach ($this->parseItemDto->getOption()->getPriceParseList() as $itmToParse) {
            $content = $this->httpPrepareParse($itmToParse, '', Constant::CLASSES['price_item']);
            $crawler = $this->helper->initDomCrawler($content->html());
            $price = $this->helper->priceParsePrepare($crawler);
            if ($price) {
                $this->parseItemDto->setPrice(array_merge($this->helper->priceParsePrepare($crawler), $this->parseItemDto->getPrice()));
            }
        }

        return $this->parseItemDto;
    }

    /**
     * @param ParsePageItemDto $val
     * @return ParseItemDto|null
     * @throws OAuthException
     */
    public function itemParse(ParsePageItemDto $val): ?ParseItemDto
    {
        try{
            return $this->itemParseCallback($val);
        } catch (\InvalidArgumentException | GuzzleException $exception) {
            $this->log->registerException(
                Logger::WARNING,
                $exception instanceof GuzzleException
                    ? Constant::LOG_ERROR_HTTP
                    : Constant::LOG_ERROR_CRAWLER,
                $exception,
                ['url' => $val->getUrl(), 'uuid' => $val->getUuid()]
            );
        }

        $this->log->pointClear();
        return null;
    }

    /**
     * @return int|null
     * @throws GuzzleException
     * Вывод кол-ва результатов товаров
     */
    private function parsePageCount(): ?int
    {
        $content = $this->httpPrepareParse($this->config->getUrl() . $this->config->getFilterPage(), '', Constant::CLASSES['page_counter']);
        if (preg_match('/\d+/', $content->text(), $matches)) {
            return intval($matches[0]) / $this->config->getCount();
        } else {
            return 0;
        }
    }

    /**
     * @param int $itemId
     * @return array|null
     * @throws GuzzleException
     */
    private function parsePageList(int $itemId): ?array
    {
        $content = $this->httpPrepareParse($this->config->getCatalogPage(), $itemId, Constant::CLASSES['page_catalog']);
        return $content->filter(Constant::CLASSES['page_catalog_url_item'])->each(function (Crawler $node, $i) {
            return $this->helper->perfectArrayItemList($node);
        });
    }

    /**
     * @return array|null
     */
    public function pageParse(): ?array
    {
        try{
            $pageCount = $this->parsePageCount();
        } catch (\InvalidArgumentException | GuzzleException $exception) {
            $this->log->registerException(
                Logger::CRITICAL,
                $exception instanceof GuzzleException
                    ? Constant::LOG_ERROR_HTTP
                    : Constant::LOG_ERROR_CRAWLER,
                $exception
            );
            return null;
        }

        $list = [];
        for ($i = 1; $i<= $pageCount; $i++) {
            try{
                $parse = $this->parsePageList($i);
            } catch (\InvalidArgumentException | GuzzleException $exception) {
                $getPreviewId = $i - 1;
                $this->log->registerException(
                    Logger::CRITICAL,
                    $exception instanceof GuzzleException
                        ? Constant::LOG_ERROR_HTTP
                        : Constant::LOG_ERROR_CRAWLER,
                    $exception,
                    ['pageId' => $i, 'pageCount' => $pageCount, 'lastPage' => $list[$getPreviewId]]
                );
                continue;
            }
            $list = array_merge($list, $parse);
        }
        $this->log->register(Logger::DEBUG, Constant::LOG_PARSE_PAGE, $list);
        return $list;
    }


    public function clear()
    {
        $this->log->pointClear();
    }

    /**
     * @param string $template
     * @param string $append
     * @param string $class
     * @param string $method
     * @param array $arrayForm
     * @return Crawler
     * @throws GuzzleException
     */
    private function httpPrepareParse(
        string $template,
        string $append,
        string $class,
        string $method = Constant::GET,
        array $arrayForm = []
    ): Crawler
    {
        return $this->helper->httpParse(
            $this->client,
            $this->cookie,
            $template,
            $append,
            $class,
            $method,
            $arrayForm
        );
    }

}
