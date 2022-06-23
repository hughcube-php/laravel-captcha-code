<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:31 下午.
 */

namespace HughCube\Laravel\CaptchaCode;

use Closure;
use HughCube\Laravel\ServiceSupport\LazyFacade;

/**
 * Class CaptchaCode.
 *
 * @method static Store store(string $name = null)
 * @method static Manager extendGenerator($driver, Closure $callback)
 * @method static Manager extendStorage($driver, Closure $callback)
 * @method static string getOrRand(string $key, int $ttl = null)
 * @method static null|string get(string $key)
 * @method static bool set(string $key, string $code, int $ttl = null)
 * @method static bool delete(string $key)
 * @method static bool validate(string $key, string $code, bool $deleteAfterSuccess = true)
 */
class CaptchaCode extends LazyFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'captchaCode';
    }

    protected static function registerServiceProvider($app)
    {
        $app->register(ServiceProvider::class);
    }
}
