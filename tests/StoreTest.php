<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:36 下午
 */

namespace HughCube\Laravel\CaptchaCode\Tests;

use HughCube\Laravel\CaptchaCode\Generator\DefaultGenerator;
use HughCube\Laravel\CaptchaCode\Storage\CacheStorage;
use HughCube\Laravel\CaptchaCode\Store;
use Illuminate\Support\Facades\Cache;

class StoreTest extends TestCase
{
    public function testWithDefaultTtl()
    {
        $ttl = 1;
        $store = $this->getStore()->withDefaultTtl($ttl);

        $key = serialize([__METHOD__, mt_rand()]);
        $store->delete($key);

        $store->getOrRand($key);
        $this->assertNotNull($store->get($key));
        sleep($ttl + 1);
        $this->assertNull($store->get($key));
    }

    public function testWithDefaultCodes()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore()->withDefaultCodes([$key => '888888']);
        $store->delete($key);

        $this->assertSame($store->getOrRand($key), "888888");
    }

    public function testGetOrRand()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore();
        $store->delete($key);

        $ttl = 5;
        $code = $store->getOrRand($key, 5);

        $this->assertNotEmpty($code);

        for ($i = 1; $i < $ttl; $i++) {
            sleep(1);
            $this->assertSame($code, $store->getOrRand($key));
        }
        sleep(1);
        $this->assertNotSame($code, $store->getOrRand($key));
    }


    public function testGet()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore();
        $store->delete($key);

        $ttl = 5;
        $code = $store->getOrRand($key, 5);

        $this->assertNotEmpty($code);

        for ($i = 1; $i < $ttl; $i++) {
            sleep(1);
            $this->assertSame($code, $store->get($key));
        }
        sleep(1);
        $this->assertNull($store->get($key));
    }


    public function testSet()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore();
        $store->delete($key);

        $this->assertNull($store->get($key));

        $store->set($key, $key);
        $this->assertSame($store->get($key), $key);
    }

    public function testDelete()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore();

        $store->set($key, $key);
        $this->assertSame($store->get($key), $key);

        $store->delete($key);
        $this->assertNull($store->get($key));
    }


    public function testValidate()
    {
        $key = serialize([__METHOD__, mt_rand()]);
        $store = $this->getStore();

        $store->delete($key);
        $code = $store->getOrRand($key);
        $this->assertTrue($store->validate($key, $code));
        $this->assertFalse($store->validate($key, $code));

        $store->delete($key);
        $code = $store->getOrRand($key);
        $this->assertTrue($store->validate($key, $code, false));
        $this->assertTrue($store->validate($key, $code));

        $store->delete($key);
        $code = $store->getOrRand($key);
        $this->assertFalse($store->validate($key, $code . "---"));
    }


    protected function getStore()
    {
        $cache = Cache::store();

        $storage = new CacheStorage($cache);
        $generator = new DefaultGenerator(100);

        return new Store($storage, $generator);
    }
}
