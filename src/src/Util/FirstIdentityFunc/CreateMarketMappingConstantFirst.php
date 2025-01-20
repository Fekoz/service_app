<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\DirectoryMarketplaceMappingConst;
use App\Entity\Field;
use App\Service\Bot;
use App\Util\Constant;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreateMarketMappingConstantFirst implements FirstIdentityInterface
{
    const WARP = [
        'Резиновая'=> '970946917',
        'Хлопок'=> '970991392',
        'Джут'=> '970991393',
        'Войлок'=> '970946914',
        'Латексная'=> '970946915',
        'Полипропилен'=> '970946913',
        'Термопластичная резина'=> '970946913',
        'Шелк'=> '971148693',
        'войлок с тпр вкраплениями'=> '970946914',
        'Хлопок/PES'=> '970991392',
        'Не указано'=> '61791',
    ];

    const STYLE = [
        'Современный'=>'970789952',
        'Кремлевская дорожка'=>'971076353',
        'Картина'=>'971076356',
        'Мечеть'=>'970710049',
        'Детский'=>'971076348',
        'Однотонный'=>'491120177',
        'Классический'=>'491120175',
        'Не указано'=> '970789952',
    ];

    const COUNTRY = [
        'Бельгия'=>'90318',
        'Китай'=>'90296',
        'Индия'=>'90312',
        'Австралия'=>'90329',
        'Турция'=>'90305',
        'Россия'=>'90295',
        'Иран'=>'90357',
        'Беларусь'=>'90324',
        'Беларусь-Нидерланды'=>'90325',
        'Азербайджан'=>'90404'
    ];

    const CARPET_TYPE = [
        'Ковровая дорожка'=>'93662',
        'Ковер'=>'93655'
    ];

    const WARP2 = [
        'Хлопок'=>'62174',
        'Джут'=>'61818',
        'Войлок'=>'61791',
        'Полипропилен'=>'62030',
        'Термопластичная резина'=>'62119',
        'Шелк'=>'62194',
        'Хлопок/PES'=>'62174',
        'Резиновая'=>'62059',
        'Латексная'=>'61906',
        'войлок с тпр вкраплениями'=>'62350',
    ];

    const FABRIC = [
        'DINARSU'=>'5539077',
        'IPEK MEKIK'=>'971411320',
        'CARINA RUGS'=>'971342691',
        'NURTEKS'=>'970740181',
        'MILAT'=>'971002732',
        'RAGOLLE'=>'115872323',
        'Carpet Hall'=>'971390841',
        'SIRMA'=>'971351274',
        'DEKORA'=>'970982066',
        'ERKAPLAN'=>'971344611',
        'KARMEN HALI'=>'971151811',
        'MERINOS'=>'5592005',
        'OSTA'=>'5538443',
        'IDEAL'=>'970964521',
        'MERINOS TUFTING'=>'5592005',
        'DURKAR'=>'971344307',
        'YUNSER'=>'971353550',
        'Рекос'=>'970855750',
        'ARDA'=>'970965449',
        'EFOR'=>'970746156',
        'CARPETTI'=>'971981412',
        'Не указано'=>'971981412',
    ];

    const FORM = [
        'Дорожка'=>'43079',
        'Круг'=>'43080',
        'Прямоугольник'=>'43084',
        'Овал'=>'43082'
    ];

    const COLOUR = [
        'LIGHT BLUE'=>'971001201',
        'MULTICOLOR'=>'369939085',
        'Серый'=>'61576',
        'MULTICOLOR 4'=>'369939085',
        'MULTICOLOR 3'=>'369939085',
        'PURPLE'=>'61586',
        'MULTICOLOR 2'=>'369939085',
        'Черно-белый'=>'61607',
        'Синий'=>'61581',
        'Золото'=>'61582',
        'Многоцветный'=>'369939085',
        'BLUE'=>'61581',
        'Кремовый'=>'258411648',
        'MULTICOLOR 8'=>'369939085',
        'MULTICOLOR 7'=>'369939085',
        'Зеленый'=>'61583',
        'Оранжевый'=>'61585',
        'Красный'=>'61579',
        'Фиолетовый'=>'61586',
        'MULTICOLOR 9'=>'369939085',
        'LIGHT CREAM'=>'258411648',
        'Коричневый'=>'61575',
        'MULTICOLOR 6'=>'369939085',
        'Черный'=>'61574',
        'Розовый'=>'61580',
        'Терракотовый'=>'61603',
        'Бежевый'=>'61573',
        'Белый'=>'61571',
        'Желтый'=>'61578',
        'default'=>'369939085',
        'Не указано'=>'369939085',
        'Голубой'=>'61584',
        'Бирюзовый'=>'61595',
    ];

    const BRAND = [
        'ERKAPLAN'=> '972067247',
        'ALPIN'=> '972067248',
        'YUNSER'=> '972067250',
        'TURCIA'=> '972067259',
        'CARPET HALL'=> '972067260',
        'OSTA'=> '972067261',
        'ШКУРЫ'=> '972067263',
        'ARDA'=> '972067264',
        'EFOR'=> '972067266',
        'MERINOS TUFTING'=> '972067273',
        'SIRMA'=> '972067279',
        'VALENTIS'=> '972067280',
        'DEKORA'=> '972067280',
        'DINARSU'=> '972067281',
        'ALGAN'=> '972067293',
        'INDIA'=> '972067284',
        'RAGOLLE'=> '972067285',
        'DORUK'=> '972067288',
        'ISMEN'=> '972067289',
        'ARTEMIS'=> '972067289',
        'NURTEKS'=> '972067290',
        'MERINOS'=> '972067294',
        'QINGHAI TIBETAN SHEEP CARPETS'=> '972067295',
        'IDEAL'=> '972067297',
        'LYSANDRA HALI'=> '972067298',
        'GALAXY'=> '972067299',
        'IRAN'=> '972067301',
        'СТЕНД'=> '972067303',
        'IPEK MEKIK'=> '972067304',
        'VITEBSK'=> '972067305',
        'KARMEN HALI'=> '972067306',
        'РЕКОС'=> '972067307',
        'CARINA RUGS'=> '972067308',
        'DURKAR'=> '972067309',
        'MILAT'=> '972067310',
        'CARPETTI'=>'971981412',
        'Не указано'=>'971981412',
        'НЕ УКАЗАНО'=>'971981412',
    ];

    private static function append(array $list, string $name): array
    {
        $arr = [];
        foreach($list as $k => $v) {
            $arr[] = (new DirectoryMarketplaceMappingConst())
                ->setType($name)
                ->setValue($k)
                ->setIdMap($v)
            ;
        }

        return $arr;
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
        if (count($entity->getRepository(DirectoryMarketplaceMappingConst::class)->findAll()) === 0) {
            $output->writeln('Создание Константных значений для маппинга');
            $append = [];
            $append = array_merge($append, self::append(self::WARP, Constant::MAPPING_NAME_WARP));
            $append = array_merge($append, self::append(self::STYLE, Constant::MAPPING_NAME_STYLE));
            $append = array_merge($append, self::append(self::COUNTRY, Constant::MAPPING_NAME_COUNTRY));
            $append = array_merge($append, self::append(self::CARPET_TYPE, Constant::MAPPING_NAME_CARPET_TYPE));
            $append = array_merge($append, self::append(self::WARP2, Constant::MAPPING_NAME_WARP2));
            $append = array_merge($append, self::append(self::FABRIC, Constant::MAPPING_NAME_FABRIC));
            $append = array_merge($append, self::append(self::FORM, Constant::MAPPING_NAME_FORM));
            $append = array_merge($append, self::append(self::COLOUR, Constant::MAPPING_NAME_COLOUR));
            $append = array_merge($append, self::append(self::BRAND, Constant::MAPPING_NAME_BRAND));

            foreach($append as $val) {
                $entity->persist($val);
            }

            $entity->flush();
            $entity->clear();
        }
    }
}
