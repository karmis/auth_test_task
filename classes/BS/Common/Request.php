<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 6:57
 */

namespace BS\Common;


class Request {
    protected $request;
    public function __construct($request = null)
    {
        $this->request = $request;
    }
    public function getRequestParam($name)
    {
        if (array_key_exists($name, $this->request)) {
            return trim($this->request[$name]);
        }
        return null;
    }
} 