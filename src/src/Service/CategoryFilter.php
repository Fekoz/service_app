<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Util\Constant;
use App\Util\SystemServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CategoryFilter implements SystemServiceInterface
{
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
     * @var CategoryRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->repo = $this->entity->getRepository(Category::class);
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
    }

    private function getSpecKey(string $name): ?string
    {
        $spec = 'auto';
        foreach (Constant::SPECIFICATIONS as $val) {
            if($val[Constant::SPEC_VALUE] === $name) {
                $spec = $val[Constant::SPEC_PARSE_SYM];
            }
        }
        return $spec;
    }

    private function specStart(\DateTimeInterface $date): void
    {
        $specifications = $this->repo->getSpecificationsName();
        foreach ($specifications as $value) {
            $list = $this->repo->getSpecificationsWithValue($value['name']);
            if (count($list) >= $this->param->maxItemFilter) {
                continue;
            }
            foreach ($list as $item) {
                /**
                 * @var $specParam Category
                 */
                $specParam = $this->repo->findOneBy([
                    'key' => $value['name'],
                    'value' => $item['value']
                ]);

                if (!isset($value['name'])) {
                    continue;
                }

                if ($value['name'] === Constant::SPECIFICATIONS[Constant::SPEC_OTHER] || null === $this->getSpecKey($value['name'])) {
                    continue;
                }

                if(!$specParam || empty($specParam)) {
                    $specParam = new Category(
                        $value['name'],
                        'specification',
                        $item['value'],
                        true,
                        $this->getSpecKey($value['name'])
                    );
                    $specParam->setDateUpdate($date);
                    $specParam->setDateCreate($date);
                } else {
                    $specParam
                        ->setActive(true)
                        ->setType('specification')
                        ->setName($this->getSpecKey($value['name']))
                        ->setDateUpdate($date);
                }

                $this->entity->persist($specParam);
            }
        }

        $this->entity->flush();
        $this->entity->clear();
    }

    private function paramPriceField(\DateTimeInterface $date, array $item, string $type, string $typeTranslate)
    {
        foreach ($item as $value) {
            /**
             * @var $item Category
             */
            $param = $this->repo->findOneBy([
                'key' => $type,
                'value' => $value[$type]
            ]);
            if(!$param || empty($param)) {
                $param = new Category(
                    $type,
                    'price',
                    $value[$type],
                    true,
                    $typeTranslate
                );
                $param->setDateUpdate($date);
                $param->setDateCreate($date);
            } else {
                $param
                    ->setActive(true)
                    ->setType('price')
                    ->setName($typeTranslate)
                    ->setDateUpdate($date);
            }
            $this->entity->persist($param);
        }
    }

    private function priceStart(\DateTimeInterface $date): void
    {
        $this->paramPriceField($date, $this->repo->getPriceHeight(), 'height', 'Высота ковра');
        $this->paramPriceField($date, $this->repo->getPriceWidth(), 'width', 'Ширина ковра');
        $this->paramPriceField($date, $this->repo->getPriceMin(), 'min', 'Минимальная цена');
        $this->paramPriceField($date, $this->repo->getPriceMax(), 'max', 'Максимальная цена');
        $this->entity->flush();
        $this->entity->clear();
    }

    public function import(array $list = [])
    {
        return;
    }

    public function run()
    {
        $allList = $this->repo->findAll();
        foreach ($allList as $value) {
            /**
             * @var $value Category
             */
            $toPersist = $value->setActive(false);
            $this->entity->persist($toPersist);
        }
        $this->entity->flush();
        $this->entity->clear();
        $this->specStart(new \DateTime());
        $this->priceStart(new \DateTime());
        $this->bot->message(null, 'Собрал категории для фильтра.');
    }
}
