<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/18
 * Time: 10:32 下午.
 */

namespace HughCube\Laravel\CaptchaCode;

class ServiceProvider extends \HughCube\Laravel\ServiceSupport\ServiceProvider
{
    protected function getPackageFacadeAccessor(): string
    {
        return CaptchaCode::getFacadeAccessor();
    }

    /**
     * @inheritDoc
     */
    protected function createPackageFacadeRoot($app)
    {
        return new Manager();
    }
}
