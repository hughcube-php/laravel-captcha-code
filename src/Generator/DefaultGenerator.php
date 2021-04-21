<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 5:19 下午
 */

namespace HughCube\Laravel\CaptchaCode\Generator;

class DefaultGenerator implements Generator
{
    /**
     * ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
     *
     * @var string
     */
    protected $string = '0123456789';

    protected $length = 6;

    protected $maxIndex;

    public function __construct($length, $string = null)
    {
        $this->string = null === $string ? $this->string : $string;
        $this->length = null === $length ? $this->length : $length;

        $this->maxIndex = 0 >= strlen($this->string) ? 0 : (strlen($this->string) - 1);
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        $code = "";
        for ($i = 1; $i <= $this->length; $i++) {
            $index = mt_rand(0, $this->maxIndex);
            $code .= $this->string[$index];
        }

        return $code;
    }
}
