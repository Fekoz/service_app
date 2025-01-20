<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\Options;
use App\Service\Bot;
use App\Util\Constant;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CreateOptionsFunctionFirst implements FirstIdentityInterface
{
    private static function appendOption(string $name, string $value, string $info)
    {
        return (new Options())
            ->setName($name)
            ->setValue($value)
            ->setInfo($info)
            ->setDateUpdate(new \DateTime())
            ->setDateCreate(new \DateTime())
            ;
    }

    public static function run(
        OutputInterface $output,
        EntityManagerInterface $entity,
        object $param,
        object $paramParse,
        object $parserAuth,
        SerializerInterface $serializer,
        Bot $bot
    ): void
    {
        $allOption = $entity->getRepository(Options::class)->findAll();

        $output->writeln('Вывод текущего справочника Опций в дамп');
        $message = '';
        foreach ($allOption as $em) {
            /**
             * @var $em Options
             */
            if ($em->getName() === 'parser.venera.password') {
                $em->setValue('Указано в CRM');
            }
            $message .=  "\r\n" . 'Название: ' . $em->getName() . "\r\n" . 'Значение: ' . $em->getValue() . "\r\n" . 'Информация: ' . $em->getInfo() . "\r\n\r\n";
        }
        $bot->message(null, $message);

        $output->writeln('Удаление текущего справочника Опций');
        foreach ($allOption as $em) {
            $entity->remove($em);
        }
        $entity->flush();
        $entity->clear();

        if (count($entity->getRepository(Options::class)->findAll()) === 0) {
            $output->writeln('Создание нового справочника Опций');
            $configPrice = (object) $param->price;
            $configPriceOld = (object) $param->priceOld;

            $entity->persist(self::appendOption(Constant::PARSER_VENERA_EMAIL, $parserAuth->email, 'Email на сайте поставщика'));
            $entity->persist(self::appendOption(Constant::PARSER_VENERA_PASSWORD, $parserAuth->password, 'Пароль на сайте поставщика'));
            $entity->persist(self::appendOption(Constant::PARSER_DEFAULT_FACTOR, $paramParse->default_factor, 'Надбавка в % при парсинге поставщика.'));

            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_FACTOR, $configPrice->factor, '% Надбавки суммы на цену товара из каталога. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_MARKUP, $configPrice->markup, 'Доп. Надбавка по верх суммы (статичная). [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_RANDOM, true === $configPrice->isRandom ? 'true' : 'false', 'Флаг рандомной надбаки к цене. Где строка true - да, иначе - нет. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_RNDMIN, $configPrice->minRnd, 'Мин. параметр разброса рандомной надбавки. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_RNDMAX, $configPrice->maxRnd, 'Макс. параметр разброса рандомной надбавки. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_FACTOR_OZON, $configPrice->ozon_factor, 'Коэф. цены для площадки Ozon. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_FACTOR_YANDEX, $configPrice->yandex_factor, 'Коэф. цены для площадки Yandex. [Цена со скидкой]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_FACTOR_WILDBERRIES, $configPrice->wildberries_factor, 'Коэф. цены для площадки Wildberries. [Цена со скидкой]'));

            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_FACTOR, $configPriceOld->factor, '% Надбавки суммы на цену товара из каталога. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_MARKUP, $configPriceOld->markup, 'Доп. Надбавка по верх суммы (статичная). [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_RANDOM, true === $configPriceOld->isRandom ? 'true' : 'false', 'Флаг рандомной надбаки к цене. Где строка true - да, иначе - нет. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_RNDMIN, $configPriceOld->minRnd, 'Мин. параметр разброса рандомной надбавки. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_RNDMAX, $configPriceOld->maxRnd, 'Макс. параметр разброса рандомной надбавки. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_FACTOR_OZON, $configPriceOld->ozon_factor, 'Коэф. цены для площадки Ozon. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_FACTOR_YANDEX, $configPriceOld->yandex_factor, 'Коэф. цены для площадки Yandex. [Цена без скидки]'));
            $entity->persist(self::appendOption(Constant::DEFAULT_PRICE_OLD_FACTOR_WILDBERRIES, $configPriceOld->wildberries_factor, 'Коэф. цены для площадки Wildberries. [Цена без скидки]'));

            $entity->flush();
            $entity->clear();

        }
    }
}
