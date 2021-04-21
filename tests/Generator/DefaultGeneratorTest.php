<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 8:49 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Tests\Generator;

use HughCube\Laravel\CaptchaCode\Generator\DefaultGenerator;
use HughCube\Laravel\CaptchaCode\Generator\Generator;
use HughCube\Laravel\CaptchaCode\Tests\TestCase;

class DefaultGeneratorTest extends TestCase
{
    public function testIsGenerator()
    {
        $generator = $this->getGenerator();
        $this->assertInstanceOf(Generator::class, $generator);
    }

    public function testGet()
    {
        for ($i = 0; $i < 1000; $i++) {
            $generator = $this->getGenerator($i);
            $this->assertSame(strlen($generator->get()), $i);
        }
    }

    protected function getGenerator($length = null)
    {
        return new DefaultGenerator($length, null);
    }
}
