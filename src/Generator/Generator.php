<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 5:19 下午
 */

namespace HughCube\Laravel\CaptchaCode\Generator;

interface Generator
{
    /**
     * @return string
     */
    public function get();
}
