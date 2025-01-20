<?php


namespace App\Util;


use App\Entity\Images;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Specification;
use Doctrine\ORM\EntityManagerInterface;

class PersistUtil
{
    /**
     * @param array $specification
     * @param array $price
     * @param array $image
     * @return bool
     */
    public function getCountArray(array $specification, array $price, array $image): bool
    {
        return
            \count($specification) >= 1
            && \count($price) >= 1
            && \count($image) >= 1;
    }

    /**
     * @param EntityManagerInterface $em
     * @param $item
     * @param int $id
     */
    public function appendPersist(EntityManagerInterface $em, $item, int $id)
    {
        foreach ($item as $value) {
            $value->setProductId($id);
            $em->persist($value);
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param $item
     */
    public function removePersist(EntityManagerInterface $em, $item)
    {
        foreach ($item as $value) {
            $em->remove($value);
        }
    }

    /**
     * @param array $images
     * @param string $kernel_dir
     * @deprecated not used
     */
    public function dropImages(array $images, string $kernel_dir)
    {
        foreach ($images as $value) {
            /**
             * @var $value Images
             */
            if ($value) {
                try {
                    @\unlink($kernel_dir . $value->getDir() . $value->getFilename());
                } catch (\Exception $exception) {
                    dump('Not image load to drop');
                }
            }
        }
    }

    /**
     * @param array $price
     * @return array
     */
    public function priceRebuild(array $price): array
    {
        $priceDo = $price;
        $tmp = [];
        /**
         * @var $v Price
         */
        foreach($price as $k => $v) {
            if (!isset($tmp[$v->getUuid()])) {
                $tmp[$v->getUuid()] = true;
                continue;
            }
            /**
             * @var $value Price
             */
            foreach ($priceDo as $value) {
                if ($v->getUuid() === $value->getUuid()) {
                    $priceItem = $value;
                    $allCount = $priceItem->getCount() + $v->getCount();
                    $priceItem->setCount($allCount);
                }
            }
            unset($price[$k]);
        }
        return $price;
    }

    /**
     * @param Product $product
     * @return Product|null
     */
    public function defaultValidateLocker(Product $product): ?Product
    {
        null !== $product->isGlobalUpdateLock() ?: $product->setGlobalUpdateLock(false);
        null !== $product->isProductLock() ?: $product->setProductLock(false);
        null !== $product->isPriceLock() ?: $product->setPriceLock(false);
        null !== $product->isSpecificationLock() ?: $product->setSpecificationLock(false);
        null !== $product->isImagesLock() ?: $product->setImagesLock(false);
        null !== $product->isAttributeLock() ?: $product->setAttributeLock(false);
        return $product;
    }

    /**
     * @param EntityManagerInterface $em
     * @param array $arr
     * @return array
     */
    public function removeDuplicates(EntityManagerInterface $em, array $arr): array
    {
        $unique = [];

        foreach ($arr as $entity) {
            if ($entity instanceof Specification) {
                $name = $entity->getName();
                if (isset($unique[$name])) {
                    $em->remove($unique[$name]);
                }
                $unique[$name] = $entity;
            }
        }

        return count(array_values($unique)) > 1
            ? array_values($unique)
            : $arr;
    }

    /**
     * @param array $data
     * @param EntityManagerInterface $em
     * @return array
     */
    public function dropImagesToDoubles(array $data, EntityManagerInterface $em): array
    {
        $urls = [];
        $uniqueData = [];

        // Собираем уникальные URL изображений и уникальные объекты
        foreach ($data as $image) {
            if ($image instanceof Images) {
                $url = $image->getOriginalUrl();

                if (!isset($urls[$url])) {
                    $urls[$url] = true;
                    $uniqueData[] = $image;
                } else {
                    // Удаляем дубликаты из базы данных
                    $em->remove($image);
                }
            } else {
                // Если элемент не является объектом Images, добавляем его как есть в результат
                $uniqueData[] = $image;
            }
        }

        $em->flush();

        return $uniqueData;
    }

    /**
     * @param int $productId
     * @param array $new
     * @param array $old
     * @param EntityManagerInterface $em
     * @param bool $isLock
     * @description Update old \ Create new
     */
    public function compare(int $productId, array $new, array $old, EntityManagerInterface $em, bool $isLock)
    {
        $oldMap = array_reduce($old, function ($acc, $value) {
            $uuid = $this->getUuidWithType($value);
            $acc[$uuid] = $value;
            return $acc;
        }, []);

        foreach ($new as $value) {
            $uuid = $this->getUuidWithType($value);
            if (!isset($oldMap[$uuid])) {
                $em->persist($this->newAddCallback($value, $productId));
                continue;
            }

            $oldPrice = $oldMap[$uuid];
            $em->persist($this->oldUpdateCallback($oldPrice, $value, $productId, $isLock));
            unset($oldMap[$uuid]);
        }

        foreach ($oldMap as $value) {
            $this->oldDeleteCallback($em, $value, $productId);
        }
    }

    /**
     * @param $value
     * @return string
     * @description Return object type to key in array
     */
    private function getUuidWithType($value): string
    {
        $uuid = "";
        switch(\get_class($value)) {
            case Price::class:
                /**
                 * @var $value Price
                 */
                $uuid = $value->getUuid();
                break;
            case Images::class:
                /**
                 * @var $value Images
                 */
                $uuid = $value->getFilename();
                break;
            case Specification::class:
                /**
                 * @var $value Specification
                 */
                $uuid = $value->getName();
                break;
        }
        return $uuid;
    }

    /**
     * @param $new
     * @param int $id
     * @return mixed
     */
    private function newAddCallback($new, int $id)
    {
        switch(\get_class($new)) {
            case Specification::class:
            case Images::class:
            case Price::class:
                $new->setProductId($id);
                break;
        }
        return $new;
    }

    /**
     * @param $old
     * @param $new
     * @param int $id
     * @param bool $isLock
     * @return Images|Price|Specification
     */
    private function oldUpdateCallback($old, $new, int $id, bool $isLock)
    {
        switch(\get_class($new)) {
            case Specification::class:
                /**
                 * @var $old Specification
                 * @var $new Specification
                 */
                $old->setValue($new->getValue());
                $old->setName($new->getName());
                break;
            case Images::class:
                /**
                 * @var $old Images
                 * @var $new Images
                 */
                $old->setType($new->getType());
                $old->setDir($new->getDir());
                $old->setFilename($new->getFilename());
                $old->setOriginalUrl($new->getOriginalUrl());
                break;
            case Price::class:
                /**
                 * @var $old Price
                 * @var $new Price
                 */
                $old->setCount($new->getCount());
                if (!$isLock) {
                    $old->setPrice($new->getPrice());
                }
                $old->setOldPrice($new->getOldPrice());
                $old->setWidth($new->getWidth());
                $old->setHeight($new->getHeight());
                $old->setStorage($new->getStorage());
                $old->setMid($new->getMid());
                break;
        }

        $old->setProductId($id);
        return $old;
    }

    /**
     * @param EntityManagerInterface $em
     * @param $value
     * @param int $id
     */
    private function oldDeleteCallback(EntityManagerInterface $em, $value, int $id)
    {
        switch(\get_class($value)) {
            case Images::class:
                /**
                 * @var $value Images
                 */
                $em->remove($value);
                break;
            case Specification::class:
                /**
                 * @var $value Specification
                 */
                $em->remove($value);
                break;
            case Price::class:
                /**
                 * @var $value Price
                 */
                $value->setProductId($id);
                $value->setCount(0);
                $em->persist($value);
                break;
        }
    }

}
