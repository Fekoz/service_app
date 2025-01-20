<?php


namespace App\Service;


use App\Util\Constant;
use App\Util\Dto\CacheDto;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Cache
{
    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(AdapterInterface $cache, SerializerInterface $serializer)
    {
        $this->cache = $cache;
    }

    public function set(string $key, $value = null): void
    {
        $data = new CacheDto();
        $cacheItem = $this->cache->getItem($key);

        if (is_array($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_ARRAY);
        } elseif (is_object($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_OBJECT);
        } elseif (is_float($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_FLOAT);
        } elseif (is_bool($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_BOOL);
        } elseif (is_string($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_STRING);
        } elseif (is_integer($value)) {
            $data->setType(Constant::DECODE_CACHE_TYPE_INT);
        } else {
            $data->setType(Constant::DECODE_CACHE_TYPE_DEFAULT);
        }

        $data->setResult($value);
        $cacheItem->set($this->serialized($data));
        $this->cache->save($cacheItem);
    }

    public function get(string $key): ?CacheDto
    {
        $cacheItem = $this->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            return null;
        }

        try {
            return $this->deserialized($cacheItem->get());
        } catch (\Exception $e) {

        }

        return null;
    }

    public function result(string $key)
    {
        $data = $this->get($key);
        if ($data instanceof CacheDto) {
            return $data->getResult();
        }

        return null;
    }

    private function serialized(CacheDto $data): string
    {
        return base64_encode(serialize($data));
    }

    private function deserialized(string $data): ?CacheDto
    {
        return unserialize(base64_decode($data));
    }
}
