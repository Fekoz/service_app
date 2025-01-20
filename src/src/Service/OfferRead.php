<?php

namespace App\Service;

use App\Entity\Options;
use App\Entity\Price;
use App\Entity\Specification;
use App\Entity\Uri;
use App\Util\Constant;
use App\Util\Dto\OfferReadMarketOffer;
use App\Util\Dto\OfferReadMarketResponse;
use App\Util\SystemServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Header;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class OfferRead implements SystemServiceInterface
{
    const SIZE = 100;
    const YANDEX_MARKET = 1;
    const NAME_AUTH_TOKEN = 'market.api.token';
    const NAME_AUTH_CLIENT = 'market.api.client';

    /**
     * @var object
     */
    private $param;

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var Uri
     */
    private $repo;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $auth;

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->repo = $this->entity->getRepository(Uri::class);
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->http = new Client();
        $this->url = $this->param->market_api_endpoint . "/" . $this->param->market_api_market_id . "/" .  $this->param->market_api_endpoint_file;
        $this->date = new \DateTime();
    }

    public function import(array $list = [])
    {
        return;
    }

    private function decode(string $json): object
    {
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));

        return (object) $serializer->decode($json, 'json');
    }

    private function decodeMarket(string $json): OfferReadMarketResponse
    {
        $response = new OfferReadMarketResponse();
        $obj = $this->decode($json);
        if (is_array($obj->offers)) {
            foreach ($obj->offers as $value) {
                $value = (object) $value;
                $offer = new OfferReadMarketOffer();
                $offer->setPrice($value->price);
                $offer->setId($value->id);
                $offer->setUrl($value->url);
                $response->addOffers($offer);
            }
            $pager = (object) $obj->pager;

            $response->setCurrent($pager->currentPage);
            $response->setSize($pager->pageSize);
            $response->setTotal($pager->total);
        }

        return $response;
    }

    private function handlerMarket(int $page = 1, int $size = 1): ?ResponseInterface
    {
        try {
            return $this->http->request('GET', $this->url . \sprintf("?page=%d&pageSize=%d", $page, $size), [
                'headers' => [
                    'Authorization' => $this->auth,
                ],
            ]);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    private function deactivateAll(): void
    {
        $allList = $this->repo->findAll();
        foreach ($allList as $value) {
            /**
             * @var $value Uri
             */
            $toPersist = $value->setActive(false);
            $this->entity->persist($toPersist);
        }
        $this->entity->flush();
        $this->entity->clear();
    }

    private function process(string $uid, string $url, string $prefix = '', int $type): void
    {
        $uid = str_replace($prefix, '', $uid);

        if (false === $this->entity->getRepository(Price::class)->isPriceMidAttempt($uid)) {
            return;
        }
        /**
         * @var $uri Uri
         */
        $em = $this->repo->findOneBy(['uid' => $uid, 'type' => $type]);
        if ($em === null || $em->getId() === null) {
            $em = new Uri();
            $em->setUid($uid);
            $em->setType($type);
            $em->setDateCreate($this->date);
        }

        $em->setActive(true);
        $em->setUri($url);
        $em->setDateUpdate($this->date);
        $this->entity->persist($em);
        $this->entity->flush();
        $this->entity->clear();
    }

    private function putOrReadParams()
    {
        $market_api_oauth_token = $this->entity->getRepository(Options::class)->findOneBy(['name'=>self::NAME_AUTH_TOKEN]);
        if (!$market_api_oauth_token) {
            $market_api_oauth_token = new Options();
            $market_api_oauth_token->setName(self::NAME_AUTH_TOKEN);
            $market_api_oauth_token->setInfo('Токен Ядекс АПИ');
            $market_api_oauth_token->setValue($this->param->market_api_oauth_token);
            $market_api_oauth_token->setDateUpdate($this->date);
            $market_api_oauth_token->setDateCreate($this->date);
            $this->entity->persist($market_api_oauth_token);
            $this->entity->flush();
        }

        $market_api_client_id = $this->entity->getRepository(Options::class)->findOneBy(['name'=>self::NAME_AUTH_CLIENT]);
        if (!$market_api_client_id) {
            $market_api_client_id = new Options();
            $market_api_client_id->setName(self::NAME_AUTH_CLIENT);
            $market_api_client_id->setInfo('ClientID Ядекс АПИ');
            $market_api_client_id->setValue($this->param->market_api_client_id);
            $market_api_client_id->setDateUpdate($this->date);
            $market_api_client_id->setDateCreate($this->date);
            $this->entity->persist($market_api_client_id);
            $this->entity->flush();
        }

        $this->auth = \sprintf("OAuth oauth_token=\"%s\", oauth_client_id=\"%s\"", $market_api_oauth_token->getValue(), $market_api_client_id->getValue());
    }

    public function run()
    {
        $this->putOrReadParams();
        $this->deactivateAll();

        $res = $this->handlerMarket();
        if ($res === null || $res->getStatusCode() !== 200) {
            $this->bot->message(null,'Ошибка при Парсинге Маркета. Токен недействительный.');
            return;
        }

        $response = $this->decodeMarket($res->getBody()->getContents());

        for($i = 0; $i <= \intval(\ceil($response->getTotal()/self::SIZE)); $i++) {
            $query = $this->handlerMarket($i, self::SIZE);
            if ($query === null || $query->getStatusCode() !== 200) {
                continue;
            }
            $list = $this->decodeMarket($query->getBody()->getContents());
            foreach ($list->getOffers() as $value) {
                $this->process($value->getId(), $value->getUrl(), $this->param->marketSkuidPrefix, self::YANDEX_MARKET);
            }
        }
    }
}
