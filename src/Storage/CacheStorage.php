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

    protected function buildKey($key): string
    {
        return sprintf('captcha-code:%s', $key);
    }

    /**
     * save a code.
     *
     * @param string $key
     * @param string $code
     * @param null   $ttl
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function set(string $key, string $code, $ttl = null): bool
    {
        return false !== $this->cache->set($this->buildKey($key), $code, $ttl);
    }

    /**
     * Fetches a code.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->cache->get($this->buildKey($key));
    }

    /**
     * Remove a code.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($this->buildKey($key));
    }
}
