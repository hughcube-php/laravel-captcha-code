<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:31 下午
 */

namespace HughCube\Laravel\CaptchaCode;

use Closure;
use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * Class CaptchaCode
 * @method static Store store(string $name = null)
 * @method static Manager extendGenerator($driver, Closure $callback)
 * @method static Manager extendStorage($driver, Closure $callback)
 */
class CaptchaCode extends IlluminateFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return self::class;
    }

    /**
     * @param string $key
     * @param null|integer $ttl
     * @return string|null
     */
    public static function getOrRand($key, $ttl = null)
    {
        return static::store()->getOrRand($key, $ttl);
    }

    public static function get($key)
    {
        return static::store()->get($key);
    }

    public static function set($key, $code, $ttl = null)
    {
        return static::store()->set($key, $code, $ttl);
    }

    public static function delete($key)
    {
        return static::store()->delete($key);
    }

    public static function validate($key, $code, $deleteAfterSuccess = true)
    {
        return static::store()->validate($key, $code, $deleteAfterSuccess);
    }
}
