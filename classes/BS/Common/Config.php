<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 4:51
 */

namespace BS\Common;


class Config
{
    /**
     * @var mixed[]
     */
    protected static $data = array(
        'db' => array(
            'host' => 'localhost',
            'name' => 'test_task',
            'user' => '*',
            'pass' => '*'
        ),
        'file' => array(
            'path' => 'uploads/',
            'max_size' => '204800',
            'exts' => array('png', 'gif','jpeg', 'jpg')
        )
    );

    /**
     * Добавляет значение в реестр
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    /**
     * Возвращает значение из реестра по ключу
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }
} 