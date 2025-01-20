<?php

namespace App\Service;

use App\Entity\Collection;
use App\Entity\CreteriaCollections;
use App\Entity\Specification;
use App\Repository\CollectionRepository;
use App\Repository\CreteriaCollectionsRepository;
use App\Repository\SpecificationRepository;
use App\Util\Constant;
use App\Util\Dto\SpecificationDesignDto;
use App\Util\Dto\WriteSheafCollectionDto;
use App\Util\SystemServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SheafCollection implements SystemServiceInterface
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
     * @var SpecificationRepository
     */
    private $repo;

    /**
     * @var array
     */
    private $writeList;

    /**
     * @var array
     */
    private $ketCodeList;

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->repo = $this->entity->getRepository(Specification::class);
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->writeList = [];
        $this->ketCodeList = [];
        $this->date = new \DateTime();
    }

    private function getCode(string $form, string $collection, $design): string
    {
        return \md5($form . "_" . $collection . "_" . $design);
    }

    private function getName(string $form, string $collection, $design): string
    {
        return $form . ", " . $collection . ", " . $design;
    }

    private function atWrite(int $in, int $out, SpecificationDesignDto $specDto)
    {
        $isWrite = true;

        $keyCode = $this->getCode($specDto->getForm(), $specDto->getCollection(), $specDto->getDesign());
        $description = $this->getName($specDto->getForm(), $specDto->getCollection(), $specDto->getDesign());

        foreach ($this->writeList as $item) {
            if ($item instanceof WriteSheafCollectionDto && $item->getIn() === $in && $item->getOut() === $out) {
                $isWrite = false;
            }

            if (!isset($this->ketCodeList[$keyCode])) {
                $cc = new CreteriaCollections();
                $cc->setCode($keyCode);
                $cc->setDescription($description);
                $cc->setName($description);
                $cc->setDateUpdate($this->date);
                $cc->setDateCreate($this->date);
                $this->ketCodeList[$keyCode] = $cc;
            }
        }

        if ($isWrite === true) {
            $this->writeList[] = (new WriteSheafCollectionDto())
                ->setIn($in)
                ->setOut($out)
                ->setKeyCode($keyCode)
            ;
        }
    }

    private function read(int $limit = 0, int $offset = 0)
    {
        $list = $this->repo->getSpecWithName('design', $limit, $offset);
        foreach ($list as $val) {
            $specDynamic = $this->repo->getSpecWithNameAndValue('design', $val['value']);
            $instance = [];
            foreach ($specDynamic as $spec) {
                $design = $spec['design'] ?? 'none';
                if (isset($spec['productId']) && isset($spec['form']) && isset($spec['collection']) && $design) {
                    $instance[] = (new SpecificationDesignDto())
                        ->setProductId($spec['productId'])
                        ->setCollection($spec['form'])
                        ->setForm($spec['collection'])
                        ->setDesign($design)
                    ;
                }
            }

            foreach($instance as $item) {
                foreach($instance as $specification) {
                    if($item->getForm() === $specification->getForm() && $item->getCollection() === $specification->getCollection() && $item->getProductId() !== $specification->getProductId()) {
                        $this->atWrite($item->getProductId(), $specification->getProductId(), $specification);
                    }
                }
            }
        }
        $this->entity->flush();
        $this->entity->clear();
    }

    private function write()
    {
        $date = new \DateTime();

        /**
         * @var $item CreteriaCollections
         */
        foreach ($this->ketCodeList as $key => $item) {
            /**
             * @var $cc CreteriaCollections
             */
            $cc = $this->entity->getRepository(CreteriaCollections::class)->getOnce($item->getCode());
            if (null === $cc) {
                $this->entity->persist($item);
            }
        }
        $this->entity->flush();
        $this->entity->clear();


        /**
         * @var $item WriteSheafCollectionDto
         */
        foreach ($this->writeList as $key => $item) {
            $this->entity->getRepository(Collection::class)->dropWithList($item->getIn());
            $collection = new Collection($item->getIn(), $item->getOut(), $item->getKeyCode());
            $collection->setDateUpdate($date);
            $collection->setDateCreate($date);
            $this->entity->persist($collection);
            if ($key % 200 === 0) {
                $this->entity->flush();
            }
        }
        $this->entity->flush();
        $this->entity->clear();
    }

    public function import(array $list = [])
    {
        return;
    }

    public function run()
    {
        $count = $this->repo->getCountSpecWithName('design');

        if ($count > $this->param->maxItemCollection) {
            $ic = round($count / $this->param->maxItemCollection, 0 ,PHP_ROUND_HALF_UP);
            for ($i = 0; $i <= $ic; $i++) {
                $this->read($this->param->maxItemCollection, $i * $this->param->maxItemCollection);
            }
            $this->write();
            $this->bot->message(null,'Обновил массив зависимостей продуктов ковров.');
            return;
        }

        $this->read($this->param->maxItemCollection);
        $this->write();
        $this->bot->message(null, 'Обновил зависимости продуктов ковров.');
    }

}
