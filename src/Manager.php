<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:19 下午.
 */

namespace HughCube\Laravel\CaptchaCode;

use Closure;
use HughCube\Laravel\CaptchaCode\Generator\DefaultGenerator;
use HughCube\Laravel\CaptchaCode\Generator\Generator;
use HughCube\Laravel\CaptchaCode\Storage\CacheStorage;
use HughCube\Laravel\CaptchaCode\Storage\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class Manager
{
    /**
     * The alifc server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The clients.
     *
     * @var Store[]
     */
    protected $stores = [];

    /**
     * @var Closure[]
     */
    protected $storageCustomCreators = [];

    /**
     * @var Closure[]
     */
    protected $generatorCustomCreators = [];

    /**
     * Manager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a client by name.
     *
     * @param string|null $name
     *
     * @return Store
     */
    public function store($name = null)
    {
        $name = null == $name ? $this->getDefaultClient() : $name;

        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }

        return $this->stores[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given client by name.
     *
     * @param string|null $name
     *
     * @return Store
     */
    protected function resolve($name = null)
    {
        $config = $this->configuration($name);

        $storage = $this->makeStorage($name, Arr::get($config, 'storage'));
        $generator = $this->makeGenerator($name, Arr::get($config, 'generator'));
        $defaultCodes = Arr::get($config, 'defaultCodes', []);
        $ttl = Arr::get($config, 'defaultTtl', 10 * 60);

        $store = new Store($storage, $generator);

        return $store->withDefaultTtl($ttl)->withDefaultCodes($defaultCodes);
    }

    public function extendStorage($driver, Closure $callback)
    {
        $this->storageCustomCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @param string $name
     * @param array  $config
     *
     * @return Storage
     */
    protected function makeStorage($name, $config)
    {
        $driver = Arr::get($config, 'driver');

        if (is_null($driver)) {
            throw new InvalidArgumentException("storage for captcha [{$name}] is not defined.");
        }

        if (isset($this->storageCustomCreators[$driver])) {
            return call_user_func($this->storageCustomCreators[$driver], $config);
        }

        $driverMethod = 'create'.ucfirst($driver).'StorageDriver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("storage driver [{$driver}] for captcha [{$name}] is not defined.");
    }

    /**
     * @param array $config
     *
     * @return Storage
     */
    protected function createCacheStorageDriver($config)
    {
        $cache = Arr::get($config, 'cache');
        $cache = is_object($cache) ? $cache : Cache::store($cache);

        return new CacheStorage($cache);
    }

    public function extendGenerator($driver, Closure $callback)
    {
        $this->generatorCustomCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @param string $name
     * @param array  $config
     *
     * @return Generator
     */
    public function makeGenerator($name, $config)
    {
        $driver = Arr::get($config, 'driver', 'default');

        if (is_null($driver)) {
            throw new InvalidArgumentException("generator for captcha [{$name}] is not defined.");
        }

        if (isset($this->generatorCustomCreators[$driver])) {
            return call_user_func($this->generatorCustomCreators[$driver], $config);
        }

        $driverMethod = 'create'.ucfirst($driver).'GeneratorDriver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("generator driver [{$driver}] for captcha [{$name}] is not defined.");
    }

    public function createDefaultGeneratorDriver($config)
    {
        $length = Arr::get($config, 'length');
        $string = Arr::get($config, 'string');

        return new DefaultGenerator($length, $string);
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultClient()
    {
        return Arr::get($this->config, 'default', 'default');
    }

    /**
     * Get the configuration for a client.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultClient();
        $stores = Arr::get($this->config, 'stores');
        $defaults = Arr::get($this->config, 'defaults');

        if (is_null($store = Arr::get($stores, $name))) {
            throw new \InvalidArgumentException("captcha store [{$name}] not configured.");
        }

        return array_merge($store, $defaults);
    }
}
