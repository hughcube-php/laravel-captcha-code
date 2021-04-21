<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:21 下午.
 */

namespace HughCube\Laravel\CaptchaCode;

use HughCube\Laravel\CaptchaCode\Generator\Generator;
use HughCube\Laravel\CaptchaCode\Storage\Storage;

class Store
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var int
     */
    protected $defaultTtl = 10 * 60;

    /**
     * @var string[]
     */
    protected $defaultCodes = [];

    /**
     * Store constructor.
     *
     * @param Storage   $storage
     * @param Generator $generator
     */
    public function __construct(Storage $storage, Generator $generator)
    {
        $this->storage = $storage;
        $this->generator = $generator;
    }

    /**
     * @param int $ttl
     *
     * @return $this
     */
    public function withDefaultTtl($ttl)
    {
        $this->defaultTtl = $ttl;

        return $this;
    }

    /**
     * @param string[] $items
     *
     * @return $this
     */
    public function withDefaultCodes(array $items)
    {
        $this->defaultCodes = $items;

        return $this;
    }

    /**
     * @param string $key
     * @param int    $ttl
     *
     * @return string
     */
    public function getOrRand($key, $ttl = null)
    {
        if (null != ($existCode = $this->get($key))) {
            return $existCode;
        }

        if (isset($this->defaultCodes[$key])) {
            $code = $this->defaultCodes[$key];
        } else {
            $code = $this->generator->get();
        }

        return $this->set($key, $code, $ttl);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $code = $this->storage->get($key);

        return empty($code) ? null : $code;
    }

    /**
     * @param string      $key
     * @param null|string $code
     * @param null|int    $ttl
     *
     * @return string
     */
    public function set($key, $code, $ttl = null)
    {
        $ttl = null === $ttl ? $this->defaultTtl : $ttl;
        $this->storage->set($key, $code, $ttl);

        return $code;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);

        return $this->storage->delete($key);
    }

    /**
     * @param mixed  $key
     * @param string $code
     * @param bool   $deleteAfterSuccess
     *
     * @return bool
     */
    public function validate($key, $code, $deleteAfterSuccess = true)
    {
        $key = $this->buildKey($key);

        if (empty($code)) {
            return false;
        }

        $existCode = $this->storage->get($key);
        if (empty($existCode)) {
            return false;
        }

        if (strtolower($existCode) !== strtolower($code)) {
            return false;
        }

        if ($deleteAfterSuccess) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function buildKey($key)
    {
        return $key;
    }
}
