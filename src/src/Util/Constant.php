<?php

namespace App\Util;

class Constant
{
    /**
     * @todo Внимание к константам!
     * @notice При изменении констант массивов необходимо делать миграцию
     * @Target migrations/Version20220105010017.php
     */
    const CARPETTI = 'carpetti';
    /**
     * VALIDATOR KEY NAME
     */
    const UUID = 'uuid';
    const ORIGINAL_URL = 'url';
    const NAME = 'name';
    const FULLPRICE = 'fullPrice';
    const COLLECTION = 'collection';
    const FACTOR = 'factor';
    const SPECIFICATION = 'specifications';
    const PRICE = 'prices';
    const IMAGE = 'images';
    const _PRICE = 'price';
    const _DIR = 'dir';
    const _URL = 'url';
    const _GET_PRICE = 'price';
    const _GET_DSIZE = 'defaultSize';
    const _DOWNLOAD_IMAGE = 'is_download_image';

    /**
     * @todo RepositoryMasterTrait
     */
    const MASTER_TRAIT_PARENT = 'productId';
    const MASTER_TRAIT_PARENT_COLLECTION = 'inProductId';
    const MASTER_TRAIT_UUID = 'uuid';
    const MASTER_TRAIT_ARTICLE = 'article';
    const MASTER_TRAIT_URL = 'url';

    /**
     * All
     */
    const _DEFAULT = 'default';

    const CONFIG_NAME = [
        // SERVICE LANE
        'App\Service\VeneraParser'              => 'venera.parser.param',
        'App\Service\ParserDecompose'           => 'venera.parser.param',
        'App\Service\Validator'                 => 'validator.parser.param',
        'App\Service\Bot'                       => 'bot.param',
        'App\Service\Queue'                     => 'queue.param',
        'App\Service\Sender'                    => 'sender.param',
        // SYSTEM SERVICE
        'App\Service\GarbageProduct'            => 'system.param',
        'App\Service\CategoryFilter'            => 'system.param',
        'App\Service\SheafCollection'           => 'system.param',
        'App\Service\MarketExport'              => 'system.param',
        'App\Service\MarketPlace'               => 'system.param',
        'App\Service\ParserRunCreator'          => 'system.param',
        'App\Service\OfferRead'                 => 'system.param',
        'App\Service\ReadingImage'              => 'system.param',
        // COMMAND LANE
        'App\Command\Garbage'                   => 'validator.parser.param',
        'App\Command\MarketImport'              => 'validator.parser.param',
        'App\Command\Runtime'                   => 'runtime.param',
        'App\Command\Alert'                     => 'alert.param',
        'App\Command\RuntimeUpdateItem'         => 'run.upd.param',
        'App\Command\RuntimeCreateItem'         => 'run.crt.param',
        'App\Command\RuntimeSendMarketPlace'    => 'run.smp.param',
        'App\Command\RuntimeCheckAttribute'     => 'run.check.attribute',
        'App\Command\Consumer'                  => 'consumer.param',
        'Consumer'                              => 'consumer.param',
        // NULL
        self::_DEFAULT                          => 'validator.parser.param',
    ];

    const BOT_TELEGRAM = 'telegram';

    /**
     * Validator
     */
    /**
     * SPECIFICATIONS
     */
    const SPEC_VALUE = 'value';
    const SPEC_PARSE_SYM = 'parse';

    const SPEC_PREPARATION = 0;
    const SPEC_MATERIAL = 1;
    const SPEC_COLLECTION = 2;
    const SPEC_FORM = 3;
    const SPEC_COLOR = 4;
    const SPEC_DESIGN = 5;
    const SPEC_COUNTRY = 6;
    const SPEC_QUALITY = 7;
    const SPEC_COMPOSITION = 8;
    const SPEC_DENSITY = 9;
    const SPEC_WEIGHT = 10;
    const SPEC_PILE = 12;
    const SPEC_COLOUR = 13;
    const SPEC_STYLE = 14;
    const SPEC_TYPE = 15;
    const SPEC_OTHER = 16;
    const SPEC_PILE_TYPE = 17;
    const SPEC_CARPET_TYPE = 18;
    const SPEC_PREPARE_TYPE = 19;
    const SPEC_COUNTRY_NEW = 20;
    const SPEC_FACTURE = 21;
    const SPEC_WARP = 22;
    const SPEC_FABRIC = 23;
    const SPEC_COMPOUND = 24;
    const SPEC_VENDOR_NAME = 25;
    const SPEC_VENDOR_COLLECTION = 26;
    const CARPETTI_FABRIC = 'CARPETTI';
    const SPECIFICATIONS = [
        self::SPEC_PREPARATION          => [self::SPEC_VALUE => 'preparation',          self::SPEC_PARSE_SYM => 'Способ изготовления'],
        self::SPEC_MATERIAL             => [self::SPEC_VALUE => 'material',             self::SPEC_PARSE_SYM => 'Материал'],
        self::SPEC_COLLECTION           => [self::SPEC_VALUE => 'collection',           self::SPEC_PARSE_SYM => 'Коллекция'],
        self::SPEC_FORM                 => [self::SPEC_VALUE => 'form',                 self::SPEC_PARSE_SYM => 'Форма'],
        self::SPEC_COLOR                => [self::SPEC_VALUE => 'color',                self::SPEC_PARSE_SYM => 'Код цвета'],
        self::SPEC_DESIGN               => [self::SPEC_VALUE => 'design',               self::SPEC_PARSE_SYM => 'Код дизайна'],
        self::SPEC_COUNTRY              => [self::SPEC_VALUE => 'country',              self::SPEC_PARSE_SYM => 'Страна производства'],
        self::SPEC_QUALITY              => [self::SPEC_VALUE => 'quality',              self::SPEC_PARSE_SYM => 'Качество'],
        self::SPEC_COMPOSITION          => [self::SPEC_VALUE => 'composition',          self::SPEC_PARSE_SYM => 'Код состава'],
        self::SPEC_DENSITY              => [self::SPEC_VALUE => 'density',              self::SPEC_PARSE_SYM => 'Плотность'],
        self::SPEC_WEIGHT               => [self::SPEC_VALUE => 'weight',               self::SPEC_PARSE_SYM => 'Вес'],
        self::SPEC_PILE                 => [self::SPEC_VALUE => 'pile',                 self::SPEC_PARSE_SYM => 'Высота ворса'],
        self::SPEC_COLOUR               => [self::SPEC_VALUE => 'colour',               self::SPEC_PARSE_SYM => 'Цвет'],
        self::SPEC_STYLE                => [self::SPEC_VALUE => 'style',                self::SPEC_PARSE_SYM => 'Стиль'],
        self::SPEC_TYPE                 => [self::SPEC_VALUE => 'type',                 self::SPEC_PARSE_SYM => 'Качество1'],
        self::SPEC_PILE_TYPE            => [self::SPEC_VALUE => 'pile_type',            self::SPEC_PARSE_SYM => 'Тип ворса ковра'],
        self::SPEC_CARPET_TYPE          => [self::SPEC_VALUE => 'carpet_type',          self::SPEC_PARSE_SYM => 'Тип ковра'],
        self::SPEC_PREPARE_TYPE         => [self::SPEC_VALUE => 'prepare_type',         self::SPEC_PARSE_SYM => 'Тип изготавления'],
        self::SPEC_COUNTRY_NEW          => [self::SPEC_VALUE => 'country',              self::SPEC_PARSE_SYM => 'Страна'],
        self::SPEC_FACTURE              => [self::SPEC_VALUE => 'facture',              self::SPEC_PARSE_SYM => 'Фактура'],
        self::SPEC_WARP                 => [self::SPEC_VALUE => 'warp',                 self::SPEC_PARSE_SYM => 'Тип основы'],
        self::SPEC_FABRIC               => [self::SPEC_VALUE => 'fabric',               self::SPEC_PARSE_SYM => 'Фабрика'],
        self::SPEC_COMPOUND             => [self::SPEC_VALUE => 'compound',             self::SPEC_PARSE_SYM => 'Состав'],
        self::SPEC_VENDOR_NAME          => [self::SPEC_VALUE => 'vendor_name',          self::SPEC_PARSE_SYM => 'Вендор'],
        self::SPEC_VENDOR_COLLECTION    => [self::SPEC_VALUE => 'vendor_collection',    self::SPEC_PARSE_SYM => 'Коллекция на вендоре'],
        self::SPEC_OTHER                => [self::SPEC_VALUE => 'other',                self::SPEC_PARSE_SYM => null],
    ];

    /**
     * PRICES
     */
    const STORAGE_0 = 0;
    const STORAGE_4 = 4;
    const STORAGE_7 = 7;
    const STORAGE_4_2 = 5;
    const STORAGES = [
        self::STORAGE_0 => 'Все склады',
        self::STORAGE_4 => 'Мытищи',
        self::STORAGE_7 => 'Ленинский',
        self::STORAGE_4_2 => 'Рулон',
    ];

    const PRICES_VALUE = 'value';
    const PRICES_PARSE_SYM = 'parse';

    const CENTIMETER_FACTOR = 100;

    const PRICES_MM = 0;
    const PRICES_CM = 1;
    const PRICES_M = 2;
    const PRICES_G = 3;
    const PRICES_KG = 4;
    const PRICES_T = 5;
    const PRICES_MM_M2 = 6;
    const PRICES_CM_M2 = 7;
    const PRICES_M_M2 = 8;
    const PRICES_G_M2 = 9;
    const PRICES_KG_M2 = 10;
    const PRICES_T_M2 = 11;

    const METRES = [
        self::PRICES_MM => [self::PRICES_VALUE => 'millimeters', self::PRICES_PARSE_SYM => 'мм'],
        self::PRICES_CM => [self::PRICES_VALUE => 'centimeters', self::PRICES_PARSE_SYM => 'см'],
    ];

    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_PILE_TRUE = "С ворсом";
    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_PILE_FALSE = "Без ворса";

    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_CARPET_TRUE = "Ковровая дорожка";
    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_CARPET_FALSE = "Ковер";

    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_PREPARE_TRUE = "Ручной";
    const CARPET_SPEC_DYNAMIC_PARAM_TYPE_PREPARE_FALSE = "Машинный";



    /**
     * ARTICLE FORMATTER
     */
    const ARTICLE_FORMATTER = [
        ' '=>'-',   'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'G',
        'Д'=>'D',	'Е'=>'E',	'Ж'=>'J',	'З'=>'Z',	'И'=>'I',
        'Й'=>'Y',	'К'=>'K',	'Л'=>'L',	'М'=>'M',	'Н'=>'N',
        'О'=>'O',	'П'=>'P',	'Р'=>'R',	'С'=>'S',	'Т'=>'T',
        'У'=>'U',	'Ф'=>'F',	'Х'=>'H',	'Ц'=>'TS',	'Ч'=>'CH',
        'Ш'=>'SH',	'Щ'=>'SCH',	'Ъ'=>'',	'Ы'=>'YI',	'Ь'=>'',
        'Э'=>'E',	'Ю'=>'YU',	'Я'=>'YA',	'а'=>'a',	'б'=>'b',
        'в'=>'v',	'г'=>'g',	'д'=>'d',	'е'=>'e',	'ж'=>'j',
        'з'=>'z',	'и'=>'i',	'й'=>'y',	'к'=>'k',	'л'=>'l',
        'м'=>'m',	'н'=>'n',	'о'=>'o',	'п'=>'p',	'р'=>'r',
        'с'=>'s',	'т'=>'t',	'у'=>'u',	'ф'=>'f',	'х'=>'h',
        'ц'=>'ts',	'ч'=>'ch',	'ш'=>'sh',	'щ'=>'sch',	'ъ'=>'y',
        'ы'=>'yi',	'ь'=>'',	'э'=>'e',	'ю'=>'yu',	'я'=>'ya',
        '/'=>'',    '\''=>'',   '"'=>'',    '.'=>'-',   '&'=>'.',
    ];

    /**
     * Project dir
     */
    const FILE_DIR = '/upload/';
    const IMPORT_DIR = '/import/';
    const EXPORT_DIR = '/export/';
    const DEFAULT_DIR = '/srv/app';
    const XLSX_DIR = 'xlsx/';

    /**
     * Venera parser AUTH
     */
    const AUTH = 'auth';
    const AUTH_LOGIN = '_username';
    const AUTH_PASS = '_password';
    const AUTH_CSRF = '_csrf_token';

    /**
     * Venera parser
     */
    const PARAM_REPLACE = '{PARAM}';
    const PARSER_NAME = 'venera';
    const IMAGE_TRY_DOWNLOAD = 5;
    const PRICE_FACTOR = 1;
    const DEFAULT_UUID = 'uuid_0000';
    const TYPE_IMAGE_BIG = true;
    const TYPE_IMAGE_MINI = false;
    const IMAGE_BIG_NAME = 'big/';
    const IMAGE_MINI_NAME = 'preview/';
    const IS_DOWNLOAD_IMAGE = true;

    const POST = 'POST';
    const GET = 'GET';
    const CLASSES = [
        'body' => 'body',
        'login' => '.h-header-lk .h-lk-main-link span',
        'crt_on_page' => '.h-login-form input:first-child',
        'page_counter' => '.h-catalog-sec .h-cont .h-catalog-top .h-catalog-data .h-c-found',
        'page_catalog' => '.h-catalog-sec .h-cont .h-catalog-list .h-catalog-card',
        'page_catalog_url_item' => '.h-cc-info a:first-child',
        'specification_item' => '.h-ip-info',
        'price_item' => '.h-ip-main .h-row4',
        'image_item' => '.h-ip-main .h-ip-imageBox'
    ];

    const WEIGHT_OF_PACKING = 0.15;
    const NULL_IMAGE = 'http://external.carpetti.vip/export/image/carpetti-logo.png';

    /**
     * LOGGER
     */
    const LOG_FILE = '/import/logger/app.log';
    const LOG_COLLECTION = 'main';
    const LOG_KEY_CONSOLE = 'cli';

    const LOG_ERROR = 0;
    const LOG_ERROR_HTTP = 1;
    const LOG_ERROR_CRAWLER = 2;
    const LOG_PARSE = 3;
    const LOG_AUTH = 4;
    const LOG_AUTH_RETRY = 5;
    const LOG_AUTH_ERROR = 6;
    const LOG_AUTH_CRITICAL = 7;
    const LOG_PARSE_PAGE = 8;
    const LOG_AUTH_HTTP = 9;
    const LOG_AUTH_CRAWLER = 10;
    const LOG_AUTH_SESSION_ENDED = 11;
    const LOG_DB_EM_ERROR = 12;
    const LOG_PREPARE_CARPET_TO_MARKET = 13;
    const LOG_PREPARE_CARPET_TO_MARKET_FIND = 14;
    const LOG_APPEND_MP = 15;

    const LOG = [
        self::LOG_ERROR         => 'CODE_ERROR',
        self::LOG_ERROR_HTTP    => 'HTTP_RETURN_ERROR',
        self::LOG_ERROR_CRAWLER => 'CRAWLER_RETURN_ERROR',
        self::LOG_PARSE         => 'PARSE_SUCCESSES',
        self::LOG_AUTH          => 'AUTH_SUCCESSES',
        self::LOG_AUTH_RETRY    => 'AUTH_WARNING',
        self::LOG_AUTH_ERROR    => 'AUTH_ERROR',
        self::LOG_AUTH_CRITICAL => 'AUTH_NOT_CORRECT_LOGIN_PASS_AND_SESSION',
        self::LOG_PARSE_PAGE    => 'PAGES_PARSE_SUCCESSES',
        self::LOG_AUTH_HTTP     => 'AUTH_ERROR_HTTP',
        self::LOG_AUTH_CRAWLER  => 'AUTH_ERROR_CRAWLER',
        self::LOG_AUTH_SESSION_ENDED => 'AUTH_SESSION_ENDED_NEW_SESSION_CREATE',
        self::LOG_DB_EM_ERROR   => 'DBAL_QUERY_ERROR',
        self::LOG_PREPARE_CARPET_TO_MARKET => 'PREPARE_CARPET_TO_MARKET',
        self::LOG_PREPARE_CARPET_TO_MARKET_FIND => 'PREPARE_CARPET_TO_MARKET_FIND',
        self::LOG_APPEND_MP => 'PREPARE_APPEND_MP',
    ];

    // BOT CONST
    const BOT_SYSTEM_MESSAGES = [
        'wait' => '[Ожидание] Обновлять нечего. Сон %s сек.',
        'success' => '[Обновление] Ковер с артикулом %s обновлен.',
        'error' => '[Критическая ошибка] Не могу авторизоваться на сайте поставщика!',
        'marketImport' => '[Обновление списка] Файл каталога и карточек для Яндекс.Маркета обновлен. По необходимости требуется загрузить. Скачать файлы:',
        'avitoImport' => '[Обновление списка] Файлы каталога для Авито сформированы. Скачать файлы:',
        'errorUpdate' => '[Ошибка обновления] Не могу обновить %s, он деактивирован. Ссылка на ковер у поставщика: %s',
        'dropMarket' => '[Деактивация на маркете] Товар на поставщике недоступен и удален из общей базы. Требуется ручное удаление из маркета. Маркет ID: %s-%s',
        'newOrder' => '[Новый заказ][%s] зарегистрирован. Клиент %s. Номер заказа: %s',
    ];

    const RENAME_SPECIFICATIONS = [
        ['name' => '50% вискоза/ 50% акрил', 'value' => 'Вискоза Акрил'],
        ['name' => 'Полиэстер & ПП', 'value' => 'Полиэстер и ПП'],
        ['name' => 'на войлочн.основе', 'value' => 'Войлочная основа'],
        ['name' => 'синтетический джут', 'value' => 'Синтетический джут'],
        ['name' => 'на гелиевой.основе', 'value' => 'Гелиевая основа'],
        ['name' => 'тканный машин.', 'value' => 'Тканный и машинный'],
        ['name' => 'шкура животных', 'value' => 'Животная Шкура'],
        ['name' => 'Покрытие Тафтинг', 'value' => 'Тафтинг'],
        ['name' => 'Коммерч', 'value' => 'Коммерческий'],
    ];

    const TYPE_SENDER_MESSAGE = [
        0 => 'Прочее',
        1 => 'Подбор ковра',
        2 => 'Заказ с подбором',
        3 => 'Заказ ковра',
    ];


    const PARSER_VENERA_EMAIL = 'parser.venera.email';
    const PARSER_VENERA_PASSWORD = 'parser.venera.password';
    const PARSER_DEFAULT_FACTOR = 'parser.default.factor';

    const DEFAULT_PRICE_FACTOR = 'price.factor';
    const DEFAULT_PRICE_MARKUP = 'price.markup';
    const DEFAULT_PRICE_RANDOM = 'price.random';
    const DEFAULT_PRICE_RNDMIN = 'price.random.min';
    const DEFAULT_PRICE_RNDMAX = 'price.random.max';
    const DEFAULT_PRICE_FACTOR_OZON = 'price.ozon_factor';
    const DEFAULT_PRICE_FACTOR_YANDEX = 'price.yandex_factor';
    const DEFAULT_PRICE_FACTOR_WILDBERRIES = 'price.wildberries_factor';

    const DEFAULT_PRICE_OLD_FACTOR = 'price.old.factor';
    const DEFAULT_PRICE_OLD_MARKUP = 'price.old.markup';
    const DEFAULT_PRICE_OLD_RANDOM = 'price.old.random';
    const DEFAULT_PRICE_OLD_RNDMIN = 'price.old.random.min';
    const DEFAULT_PRICE_OLD_RNDMAX = 'price.old.random.max';
    const DEFAULT_PRICE_OLD_FACTOR_OZON = 'price.old.ozon_factor';
    const DEFAULT_PRICE_OLD_FACTOR_YANDEX = 'price.old.yandex_factor';
    const DEFAULT_PRICE_OLD_FACTOR_WILDBERRIES = 'price.old.wildberries_factor';

    // QUEUE CONST
    const QUEUE_DURABLE = true;
    const QUEUE_PASSIVE = false;
    const QUEUE_EXCLUSIVE = false;
    const QUEUE_AUTO_DELETE = false;
    const QUEUE_NO_LOCAL = false;
    const QUEUE_NO_ACK = false;
    const QUEUE_NO_WAIT = false;


    // RUNTIME CONF
    const RUNTIME_ITEM_UNIDENTITY = 'none';
    const RUNTIME_ITEM_SKIP = 'skip';
    const RUNTIME_RETURN_CODE = 1;
    const RUNTIME_TELEGRAM_BOT = 'telegram';
    const RUNTIME_GROUP = null;

    // MARKETPLACE
    const MARKETPLACE_OZON_OTHER = 1;
    const MARKETPLACE_OZON_STYLE = 2;
    const MARKETPLACE_OZON_COUNTRY = 3;
    const MARKETPLACE_OZON_TYPE_CARPET = 4;
    const MARKETPLACE_OZON_TYPE_BASE = 5;
    const MARKETPLACE_OZON_FABRIC = 6;
    const MARKETPLACE_OZON_FORM = 7;
    const MARKETPLACE_OZON_COLOR = 8;
    const MARKETPLACE_OZON_BRAND = 9;

    const MARKETPLACE_FORMAT = [
        // ozon
        self::MARKETPLACE_OZON_OTHER => ['Озон прочее', '/srv/app/import/json/Прочее.json'],
        self::MARKETPLACE_OZON_STYLE => ['Озон стили', '/srv/app/import/json/Стили.json'],
        self::MARKETPLACE_OZON_COUNTRY => ['Озон страны', '/srv/app/import/json/Страны.json'],
        self::MARKETPLACE_OZON_TYPE_CARPET => ['Озон типы ковра', '/srv/app/import/json/Тип-Ковра.json'],
        self::MARKETPLACE_OZON_TYPE_BASE => ['Озон типы основы', '/srv/app/import/json/Тип-Основы.json'],
        self::MARKETPLACE_OZON_FABRIC => ['Озон фабрики', '/srv/app/import/json/Фабрики.json'],
        self::MARKETPLACE_OZON_FORM => ['Озон формы', '/srv/app/import/json/Форма.json'],
        self::MARKETPLACE_OZON_COLOR => ['Озон цвета', '/srv/app/import/json/Цвета.json'],
        self::MARKETPLACE_OZON_BRAND => ['Озон бренды', '/srv/app/import/json/Бренды.json'],
    ];

    const DECODE_CACHE_TYPE_DEFAULT = 0;
    const DECODE_CACHE_TYPE_ARRAY = 1;
    const DECODE_CACHE_TYPE_OBJECT = 2;
    const DECODE_CACHE_TYPE_INT = 3;
    const DECODE_CACHE_TYPE_STRING = 4;
    const DECODE_CACHE_TYPE_FLOAT = 5;
    const DECODE_CACHE_TYPE_BOOL = 6;

    const MAPPING_NAME_WARP = 'warp';
    const MAPPING_NAME_STYLE = 'style';
    const MAPPING_NAME_COUNTRY = 'country';
    const MAPPING_NAME_CARPET_TYPE = 'carpet_type';
    const MAPPING_NAME_WARP2 = 'warp2';
    const MAPPING_NAME_FABRIC = 'fabric';
    const MAPPING_NAME_FORM = 'form';
    const MAPPING_NAME_COLOUR = 'colour';
    const MAPPING_NAME_BRAND = 'vendor_name';
}
