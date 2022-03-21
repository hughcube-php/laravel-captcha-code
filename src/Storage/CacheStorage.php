<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:53 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Storage;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CacheStorage implements Storage
{
    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * save a code.
     *
     * @param  string  $key
     * @param  string  $code
     * @param  null  $ttl
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function set(string $key, string $code, $ttl = null): bool
    {
        return false !== $this->cache->set($key, $code, $ttl);
    }

    /**
     * Fetches a code.
     *
     * @param  string  $key
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key)
    {
        return $this->cache->get($key);
    }

    /**
     * Remove a code.
     *
     * @param  string  $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }
}
