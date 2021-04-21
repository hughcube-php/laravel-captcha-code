<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:53 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Storage;

use Psr\SimpleCache\CacheInterface;

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
     * @param string $key
     * @param string $code
     * @param null   $ttl
     *
     * @return mixed
     */
    public function set($key, $code, $ttl = null)
    {
        return $this->cache->set($key, $code, $ttl);
    }

    /**
     * Fetches a code.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * Remove a code.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        return $this->cache->delete($key);
    }
}
