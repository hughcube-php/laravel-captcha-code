<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:53 下午.
 */

namespace HughCube\Laravel\CaptchaCode\Storage;

interface Storage
{
    /**
     * save a code.
     *
     * @param string $key
     * @param string $code
     * @param null   $ttl
     *
     * @return mixed
     */
    public function set(string $key, string $code, $ttl = null);

    /**
     * Fetches a code.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key);

    /**
     * Remove a code.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool;
}
