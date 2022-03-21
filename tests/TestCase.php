<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:36 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Tests;

use HughCube\Laravel\CaptchaCode\ServiceProvider as CaptchaCodeServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CaptchaCodeServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $appConfig */
        $appConfig = $app['config'];

        $appConfig->set('cache', [
            'default' => 'default',
            'stores'  => [
                'default' => [
                    'driver' => 'file',
                    'path'   => sprintf('/tmp/test/%s', md5(serialize([__METHOD__]))),
                ],
            ],
        ]);
        $app->register(CacheServiceProvider::class);

        $appConfig->set('captchaCode', (require dirname(__DIR__).'/config/config.php'));
    }
}
