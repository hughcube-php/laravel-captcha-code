<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 10:58 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Tests\Storage;

use HughCube\Laravel\CaptchaCode\Storage\CacheStorage;
use HughCube\Laravel\CaptchaCode\Storage\Storage;
use HughCube\Laravel\CaptchaCode\Tests\TestCase;
use Illuminate\Contracts\Cache\Repository;

class CacheStorageTest extends TestCase
{
    public function testIsStorage()
    {
        $cache = $this->createMock(Repository::class);
        $storage = new CacheStorage($cache);

        $this->assertInstanceOf(Storage::class, $storage);
    }
}
