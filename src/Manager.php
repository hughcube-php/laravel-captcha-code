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
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Manager as IlluminateManager;
use InvalidArgumentException;

class Manager extends IlluminateManager
{
    /**
     * @var Closure[]
     */
    protected $storageCustomCreators = [];

    /**
     * @var Closure[]
     */
    protected $generatorCustomCreators = [];

    /**
     * @param  callable|ContainerContract|null  $container
     */
    public function __construct($container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerContract
     */
    public function getContainer(): ContainerContract
    {
        if (!property_exists($this, 'container') || null === $this->container) {
            return IlluminateContainer::getInstance();
        }

        if (is_callable($this->container)) {
            $this->container = call_user_func($this->container);
        }

        return $this->container;
    }

    /**
     * @return Repository
     *
     * @throws
     * @phpstan-ignore-next-line
     */
    protected function getConfig(): Repository
    {
        if (!property_exists($this, 'config') || null === $this->config) {
            return $this->getContainer()->make('config');
        }

        if (is_callable($this->config)) {
            $this->config = call_user_func($this->config);
        }

        return $this->config;
    }

    /**
     * @param  null|string|int  $name
     * @param  mixed  $default
     * @return array|mixed
     */
    protected function getPackageConfig($name = null, $default = null)
    {
        $key = sprintf('%s.%s', CaptchaCode::getFacadeAccessor(), $name);

        return $this->getConfig()->get($key, $default);
    }

    /**
     * @return array
     */
    protected function getStoreDefaultConfig(): array
    {
        return $this->getPackageConfig('defaults', []);
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultStore(): string
    {
        return $this->getPackageConfig('default', 'default');
    }

    /**
     * Get the configuration for a client.
     *
     * @param  string  $name
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function configuration(string $name): array
    {
        $name = $name ?: $this->getDefaultStore();
        $config = $this->getPackageConfig("stores.$name");

        if (null === $config) {
            throw new InvalidArgumentException("CaptchaCode store [{$name}] not configured.");
        }

        return array_merge($this->getStoreDefaultConfig(), $config);
    }

    /**
     * Get a client by name.
     *
     * @param  string|null|integer  $name
     * @return Store
     */
    public function store($name = null): Store
    {
        return $this->driver($name);
    }

    public function getDefaultDriver(): string
    {
        return $this->getDefaultStore();
    }

    /**
     * @inheritdoc
     */
    protected function createDriver($driver)
    {
        $config = $this->configuration($driver);

        $storage = $this->makeStorage(($config['storage'] ?? []));
        $generator = $this->makeGenerator(($config['generator'] ?? []));

        $store = new Store($storage, $generator);

        return $store
            ->withDefaultTtl(($config['defaultTtl'] ?? 10 * 60))
            ->withDefaultCodes(($config['defaultCodes'] ?? []));
    }


    public function extendStorage($driver, Closure $callback): Manager
    {
        $this->storageCustomCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @param  array  $config
     *
     * @return Storage
     */
    protected function makeStorage(array $config): Storage
    {
        if (empty($driver = $config['driver'] ?? 'cache')) {
            throw new InvalidArgumentException("The store drive must be defined.");
        }

        if (isset($this->storageCustomCreators[$driver])) {
            return call_user_func($this->storageCustomCreators[$driver], $config);
        }

        $driverMethod = 'create'.ucfirst($driver).'StorageDriver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException(sprintf('The stored drive "%s" is not defined.', $driver));
    }

    /**
     * @param  array  $config
     *
     * @return Storage
     */
    protected function createCacheStorageDriver(array $config)
    {
        $cache = $config['cache'] ?? null;
        $cache = is_object($cache) ? $cache : Cache::store($cache);

        return new CacheStorage($cache);
    }

    public function extendGenerator($driver, Closure $callback): Manager
    {
        $this->generatorCustomCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @param  array  $config
     *
     * @return Generator
     */
    public function makeGenerator(array $config): Generator
    {
        if (empty(($driver = $config['driver'] ?? 'default'))) {
            throw new InvalidArgumentException("The generator drive must be defined.");
        }

        if (isset($this->generatorCustomCreators[$driver])) {
            return call_user_func($this->generatorCustomCreators[$driver], $config);
        }

        $method = 'create'.ucfirst($driver).'GeneratorDriver';
        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        throw new InvalidArgumentException(sprintf('The generator drive "%s" is not defined.', $driver));
    }

    public function createDefaultGeneratorDriver(array $config): DefaultGenerator
    {
        return new DefaultGenerator($config['length'], ($config['string'] ?: null));
    }
}
