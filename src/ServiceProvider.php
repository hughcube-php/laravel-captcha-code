<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:32 下午.
 */

namespace HughCube\Laravel\CaptchaCode;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
        $source = realpath(dirname(__DIR__).'/config/config.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('captchaCode.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('captchaCode');
        }
    }

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->app->singleton(
            CaptchaCode::class,
            function ($app) {
                $config = $app->make('config')->get('captchaCode', []);

                return new Manager($config);
            }
        );
    }
}
