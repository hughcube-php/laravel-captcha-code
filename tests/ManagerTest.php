<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:45 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Tests;

use HughCube\Laravel\CaptchaCode\Generator\Generator;
use HughCube\Laravel\CaptchaCode\Manager;
use HughCube\Laravel\CaptchaCode\Storage\Storage;
use HughCube\Laravel\CaptchaCode\Store;
use ReflectionClass;
use ReflectionException;
use Throwable;

class ManagerTest extends TestCase
{
    public function testStore()
    {
        $manager = new Manager();

        $this->assertInstanceOf(Store::class, $manager->store());
        $this->assertInstanceOf(Store::class, $manager->store('default'));

        try {
            $manager->store(md5(serialize([__METHOD__, mt_rand()])));
            $this->fail('Expected Exception has not been raised.');
        } catch (Throwable $e) {
            $this->assertInstanceOf(Throwable::class, $e);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function testExtendStorage()
    {
        $manager = new Manager();

        $storage = $this->createMock(Storage::class);
        $manager->extendStorage('cache', function () use ($storage) {
            return $storage;
        });

        $store = $manager->store();

        $reflection = new ReflectionClass($store);
        $property = $reflection->getProperty('storage');
        $property->setAccessible(true);

        $this->assertSame($storage, $property->getValue($store));
    }


    /**
     * @throws ReflectionException
     */
    public function testExtendGenerator()
    {
        $manager = new Manager();

        $generator = $this->createMock(Generator::class);
        $manager->extendGenerator('default', function () use ($generator) {
            return $generator;
        });

        $store = $manager->store();

        $reflection = new ReflectionClass($store);
        $property = $reflection->getProperty('generator');
        $property->setAccessible(true);

        $this->assertSame($generator, $property->getValue($store));
    }
}
